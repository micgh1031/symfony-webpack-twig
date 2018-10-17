<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="frontpage")
     */
    public function indexAction(Request $request)
    {
        //return $this->redirectToRoute('login');
        return $this->render('front/index.html.twig');
    }
}
