<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createFormBuilder()
            ->add('username')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('register', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();

            $user = new User();   
            $user->setUsername($data['username']);

            // check if passwords match
            try{
                // Password hashing
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $data['password'])
                );
            } catch(\Exception $e){
                $errors = ["Password doesn't match!"];
                return $this->render('registration/index.html.twig', [
                    'errors' => $errors,
                    'form' => $form->createView()
                ]);
            }
            

            # check for errors
            $em = $this->getDoctrine()->getManager();
            
            $RAW_QUERY = "select * from user where username = '".$user->getUsername()."'";

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $result = $statement->fetch();

            if($user->getUsername() == $result['username'])
            {
                $errors = ["This user already exists!"];
                return $this->render('registration/index.html.twig', [
                    'errors' => $errors,
                    'form' => $form->createView()
                ]);
            }
            else{
                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'User registered'
                );

                return $this->redirect($this->generateUrl('app_login'));
            }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
