<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_controller")
     * @param Connection $conn
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function index(Connection $conn): Response
    {
        $queryBuilder = $conn->createQueryBuilder();
        $articles = $queryBuilder
            ->select('e.*, u.name, u.surname')
            ->from('event', 'e')
            ->leftJoin('e', 'user', 'u', 'u.id=e.creator_id')
            ->where('section = "main_page"')
            ->orderBy('date_created')
            ->execute()
            ->fetchAll();

        return $this->render('Main/base.blocek.html.twig', [
            'subtitle' => 'Hlavní stránka',
            'articles' => $articles
        ]);
    }
}