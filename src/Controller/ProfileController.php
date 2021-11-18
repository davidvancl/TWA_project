<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', $this->getParameter('message.user_required'));
            return $this->redirectToRoute('app_login');
        }
        return $this->render('Main/profile.blocek.html.twig', [
            'subtitle' => 'Profil'
        ]);
    }

    /**
     * @Route("/events", name="events_data")
     * @param Request $request
     * @param Connection $conn
     * @return JsonResponse
     */
    public function events(Request $request, Connection $conn): JsonResponse
    {
        if (!$this->getUser()) {
            return $this->json(['error' => $this->getParameter('message.user_required')]);
        }
        return $this->json($this->fetchEvents($request, $conn));
    }

    /**
     * @Route("/api")
     * @param Request $request
     * @param Connection $conn
     * @return JsonResponse
     */
    public function api(Request $request, Connection $conn): JsonResponse
    {
        return $this->json($this->fetchEvents($request, $conn), 200);
    }

    private function fetchEvents(Request $request, Connection $conn): array
    {
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->select('*, NOW() as actual_datetime')
            ->from('event')
            ->where('creator_id = :user_id');

        if ($request->query->get('show_finished')){
            $queryBuilder->andWhere('status = "completed" OR status = "not_completed"');
        } else {
            $queryBuilder->andWhere('status = "not_completed"');
        }

        if($request->query->get('show_main_page')){
            $queryBuilder->andWhere('section = "personal" OR section = "main_page"');
        } else {
            $queryBuilder->andWhere('section = "personal"');
        }

        if($request->query->get('only_after_term')){
            $queryBuilder->andWhere('date_to <= NOW()');
        }

        if($request->query->get('only_before_term')){
            $queryBuilder->andWhere('date_to >= NOW()');
        }

        if($request->query->get('show_only_private')){
            $queryBuilder->andWhere('visibility = "private"');
        }

        if($request->query->get('show_only_public')){
            $queryBuilder->andWhere('visibility = "public"');
        }

        if($request->query->get('sort') != "none" && $request->query->get('sort') != ''){
            if ($request->query->get('sort_type') != "none" && $request->query->get('sort_type') != ''){
                $queryBuilder->orderBy($request->query->get('sort'), $request->query->get('sort_type'));
            } else {
                $queryBuilder->orderBy($request->query->get('sort'));
            }
        }

        return $queryBuilder
            ->setParameter('user_id', $this->getUser()->getId())
            ->execute()
            ->fetchAll();
    }
}