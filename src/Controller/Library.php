<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Library extends AbstractController
{
    /**
     * @Route("/library", name="app_library")
     */
    public function index(): Response
    {
        return $this->render('Main/library.blocek.html.twig', [
            'subtitle' => 'Knihovna',
        ]);
    }
}