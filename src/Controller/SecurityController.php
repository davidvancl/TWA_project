<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Nelze přistoupit na přihlašovací stránku. Nejdříve se odhlaste.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('Main/login.blocek.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'year' => (new \DateTime())->format('Y'),
            'subtitle' => 'Přihlášení',
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): RedirectResponse
    {
        return $this->redirectToRoute('main_controller');
    }
}
