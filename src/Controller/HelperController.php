<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelperController extends AbstractController
{
    /**
     * @Route("/help/{page}", name="helper_controller")
     * @param Connection $conn
     */
    public function help(string $page, Connection $conn): Response
    {
        $queryBuilder = $conn->createQueryBuilder();
        $content = [];
//            $queryBuilder
//            ->select('e.*, u.name, u.surname')
//            ->from('event', 'e')
//            ->leftJoin('e', 'user', 'u', 'u.id=e.creator_id')
//            ->where('section = "main_page"')
//            ->orderBy('date_created')
//            ->execute()
//            ->fetchAll();

        return $this->render('Help/' . $page . '.help.blocek.html.twig', [
            'subtitle' => 'Nápověda',
            'content' => $content
        ]);
    }
}