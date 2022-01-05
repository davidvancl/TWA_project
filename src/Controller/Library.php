<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Library extends AbstractController
{
    /**
     * @Route("/library/{skip}", name="app_library")
     */
    public function index(int $skip = null, Connection $conn): Response
    {
        $page = 10;
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('event', 'e')
            ->leftJoin('e', 'user', 'u', 'e.creator_id=u.id')
            ->where('visibility = "public"')
            ->andWhere('status != "completed"');
            if($skip != null){
                $queryBuilder->setFirstResult(($skip - 1) * $page);
            }

        $articles = $queryBuilder->setMaxResults($page)
            ->execute()
            ->fetchAll();

        $total = $conn->createQueryBuilder()
            ->select('COUNT(id) as count')
            ->from('event')
            ->where('visibility = "public"')
            ->andWhere('status != "completed"')
            ->execute()
            ->fetch();

        $odd_number = (intval($total['count']) % 2) == 1;
        $pages =((intval($total['count']) / $page) < 1) ? [1] : range(1, (intval($total['count']) / $page) + (($odd_number != 0) ? 1 : 0));

        return $this->render('Main/library.blocek.html.twig', [
            'subtitle' => 'Knihovna',
            'list' => $articles,
            'pages' => $pages,
            'actual_page' => $skip
        ]);
    }
}