<?php

namespace App\Controller\Action;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin(Connection $conn): Response
    {
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->select('id, email, name, surname, roles, gender')
            ->from('user');

        $registered_users = $queryBuilder
            ->execute()
            ->fetchAll();

        return $this->render('Main/admin.blocek.html.twig', [
            'registered_users' => $registered_users,
            'subtitle' => 'Admin'
        ]);
    }

    /**
     * @Route("/role/{id}/{role}", name="role_changer")
     */
    public function change_role (int $id, string $role, Connection $conn): RedirectResponse
    {
        $allowed_roles = ["ROLE_ADMIN", "ROLE_USER"];
        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $this->addFlash('error', $this->getParameter('message.permissions_required'));
            return $this->redirectToRoute('app_admin');
        }

        if (!in_array($role, $allowed_roles)){
            $this->addFlash('error', "Nelze provést danou operaci. Je vše v pořádku?");
            return $this->redirectToRoute('app_admin');
        }

        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->update('user')
            ->set('roles', ':new_role')
            ->where('id = :id_user')
            ->setParameter("id_user", $id)
            ->setParameter("new_role", '["' . $role . '"]')
            ->execute();

        $this->addFlash('success', "Role byla změněna.");
        return $this->redirectToRoute('app_admin');
    }
}