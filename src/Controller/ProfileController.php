<?php

namespace App\Controller;

use App\Form\EventFilterFormType;
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
     * @param Request $request
     * @param Connection $conn
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function index(Request $request, Connection $conn): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Nelze přistoupit na profilovou stránku. Přihlašte se prosím.');
            return $this->redirectToRoute('app_login');
        }
        $filterForm = $this->createForm(EventFilterFormType::class, null, [
            'admin' => in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)
        ]);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $parameters = ['show_finished', 'only_after_term', 'only_before_term', 'sort'];
            $parameters = $this->loadParams($parameters, $filterForm->getData());
            $this->addFlash('success', 'Filter nastaven.');
            return $this->redirect($this->generateUrl('app_profile', $parameters));
        }

        return $this->render('Main/profile.blocek.html.twig', [
            'subtitle' => 'Profil',
            'filterForm' => $filterForm->createView(),
            'events' => $this->fetchEvents($request, $conn)
        ]);
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

        if($request->query->get('sort') != "none" && $request->query->get('sort') != ''){
            $queryBuilder->orderBy($request->query->get('sort'));
        } else {
            $queryBuilder->orderBy('date_to');
        }

        return $queryBuilder
            ->setParameter('user_id', $this->getUser()->getId())
            ->execute()
            ->fetchAll();
    }

    private function loadParams($parameters, $data): array
    {
        $params = array();
        foreach ($parameters as $parameter){
            if ($data[$parameter]){
                if ($parameter == 'sort' && $data[$parameter] == 'none') {
                    continue;
                } else {
                    $params[$parameter] = $data[$parameter];
                }
            }
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true) && $data['show_main_page']) {
            $params['show_main_page'] = $data['show_main_page'];
        }
        return $params;
    }
}