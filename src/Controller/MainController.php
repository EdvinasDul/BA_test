<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->redirect($this->generateUrl('contact.index'));
    }

    /**
     * @Route("/custom/{name?}", name="custom")
     */
    public function custom(Request $request)
    {
        $name = $request->get('name');
        #return new Response($content = '<h1>Welcome '. $name .' !</h1>');
        return $this->render('main/custom.html.twig', [
            'controller_name' => 'MainController',
            'name' => $name
        ]);
    }
}
