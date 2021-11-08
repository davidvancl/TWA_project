<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Nelze přistoupit na registrační stránku. Nejdříve se odhlaste.');
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles( array('ROLE_USER') );
            $user->setProfilePicture($form->get('gender') == 'woman' ? 'default_woman.jpg' : 'default_man.jpg');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Úspěšně jste se registrovali. Přihlašte se prosím.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('Main/register.blocek.html.twig', [
            'registrationForm' => $form->createView(),
            'subtitle' => 'Registrace'
        ]);
    }
}