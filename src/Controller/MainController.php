<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_controller")
     */
    public function index(): Response
    {
        return $this->render('Main/base.blocek.html.twig', [
            'subtitle' => 'Hlavní stránka'
        ]);
    }
}