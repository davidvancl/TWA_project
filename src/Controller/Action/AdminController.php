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
     * @Route("/admin/{skip}", name="app_admin")
     */
    public function admin(int $skip = null, Connection $conn): Response
    {
        $page = 10;
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->select('id, email, name, surname, roles, gender')
            ->from('user')
            ->setMaxResults($page);
        if($skip) {
            $queryBuilder->setFirstResult($skip * $page);
        }

        $total = $conn->createQueryBuilder()
            ->select('COUNT(id) as count')
            ->from('user')
            ->execute()
            ->fetch();

        $registered_users = $queryBuilder
            ->execute()
            ->fetchAll();

        $odd_number = (intval($total['count']) % 2) == 1;

        $pages =((intval($total['count']) / $page) < 1) ? [1] : range(1, (intval($total['count']) / $page) + (($odd_number != 0) ? 1 : 0));

        return $this->render('Main/admin.blocek.html.twig', [
            'pages' => $pages,
            'registered_users' => $registered_users,
            'subtitle' => 'Admin'
        ]);
    }

    /**
     * @Route("/role/{id}/{role}", name="role_changer")
     */
    public function change_role(int $id, string $role, Connection $conn): RedirectResponse
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

    /**
     * @Route("/user/delete/{id}", name="delete_user")
     */
    public function delete_user(int $id, Connection $conn): RedirectResponse
    {
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            $this->addFlash('error', $this->getParameter('message.permissions_required'));
            return $this->redirectToRoute('app_admin');
        }

        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->delete('user')
            ->where('id = :id_user')
            ->setParameter("id_user", $id)
            ->execute();

        $this->addFlash('success', "Uživatel byl smazán.");
        return $this->redirectToRoute('app_admin');
    }
}