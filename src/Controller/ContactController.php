<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Validator\Validator;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
* @Route("/contact", name="contact.")
*/
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function index(ContactRepository $contactRepository)
    {
        // display all the existing contacts
        $contacts = $contactRepository->findBy(['owned_by' => $this->getUser()->getId()]);
        $notOwned = [];

        if(!empty($contactRepository->findBy(['shared_with' => $this->getUser()->getId()]))){
            $temp = $contactRepository->findBy(['shared_with' => $this->getUser()->getId()]);
            foreach($temp as $t){
                array_push($contacts, $t);
                array_push($notOwned, $t->getId());
            }
        }

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'notOwned' => $notOwned
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // create new contact and validator
        $contact = new Contact();
        $validator = new Validator();
        
        $contact->setOwnedBy($this->getUser()->getId());

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);        

        if($form->isSubmitted()){
            # check for errors
            $errors = $validator->validate($contact);

            if (count($errors) > 0) {
                return $this->render('contact/create.html.twig', [
                    'errors' => $errors,
                    'form' => $form->createView()
                ]);
            }
            else{
                // Entity manager (connects and talk to the database)
                $em = $this->getDoctrine()->getManager();

                $em->persist($contact);     // constructs INSERT query
                $em->flush();               // send commands to database

                $this->addFlash(
                    'notice',
                    'Contact added'
                );

                return $this->redirect($this->generateUrl('contact.index'));
            }
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @return Response
     */
    public function delete(Contact $contact)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $em->remove($contact);
        $em->flush();

        $this->addFlash(
            'notice',
            'Contact Removed'
        );

        return $this->redirect($this->generateUrl('contact.index'));
    }


    /**
     * @Route("/edit/{id}", name="edit")
     * @return Response
     */
    public function edit($id, ContactRepository $contactRepository, Request $request)
    {
        // create new contact
        $contact = $contactRepository->find($id);

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            // Entity manager (connects and talk to the database)
            $em = $this->getDoctrine()->getManager();

            $em->persist($contact);     // constructs INSERT query
            $em->flush();               // send commands to database

            $this->addFlash(
                'notice',
                'Contact edited'
            );

            return $this->redirect($this->generateUrl('contact.index'));
        }

        return $this->render('contact/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cancel/{id}", name="cancel_sharing")
     * @return Response
     */
    public function cancelSharing($id, Request $request){

        $em = $this->getDoctrine()->getManager();
               
        $RAW_QUERY = "update contact set shared_with = '".null."' where id = '".$id."'";

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $this->addFlash(
            'notice',
            'Contact Sharing Canceled'
        );

        return $this->redirect($this->generateUrl('contact.index'));
    }

    /**
     * @Route("/share/{id}", name="share")
     * @return Response
     */
    public function share($id, ContactRepository $contactRepository, UserRepository $userRepository, Request $request)
    {
        // Entity manager
        $contact = $contactRepository->find($id);   
        $temp = $userRepository->findAll();
        $currShared = null;

        if($contact->getSharedWith() != null){
            $currShared = $userRepository->findBy(['id' => $contact->getSharedWith()])[0]->getUsername();
        }

        $users = [''];
        
        foreach($temp as $t){
            if($t->getId() != $this->getUser()->getId()){
                array_push($users, $t->getUsername());
            }
        }
        $flipped = array_flip($users); 
        
        $form = $this->createFormBuilder($contact)
            ->add('shared_with', ChoiceType::class, array('choices' => $flipped, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 15px')))
            ->add('save', SubmitType::class, array('label' => 'Share Contact', 'attr' => array('class' => 'btn btn-success')))
            ->getForm();

        $form->handleRequest($request);  

        if($form->isSubmitted()){
            if(!empty($form['shared_with']->getData())){
                $tt = $userRepository->findBy(['username' => $users[$form['shared_with']->getData()]]);
                
                $em = $this->getDoctrine()->getManager();
               
                $RAW_QUERY = "update contact set shared_with = '".$tt[0]->getId()."' where id = '".$contact->getId()."'";

                $statement = $em->getConnection()->prepare($RAW_QUERY);
                $statement->execute();

                $this->addFlash(
                    'notice',
                    'Contact Shared'
                );

                return $this->redirect($this->generateUrl('contact.index'));
            }else{
                $this->addFlash(
                    'error',
                    'Select user to share the contact with!'
                );

                return $this->render('contact/share.html.twig', [
                    'form' => $form->createView(),
                    'shared' => $currShared,
                    'contact' => $contact
                ]);
            }
            
        }

        return $this->render('contact/share.html.twig', [
            'form' => $form->createView(),
            'shared' => $currShared,
            'contact' => $contact
        ]);
    }
}
