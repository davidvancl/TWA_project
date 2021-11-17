<?php

namespace App\Controller\Action;

use App\Entity\Event;
use App\Form\AddEventFormType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventManagerController extends AbstractController
{
    /**
     * @Route("/create", name="create_event")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        if ($route = $this->securityCheck()) return $route;

        $event = new Event();
        $form = $this->createForm(AddEventFormType::class, $event, ['images' => $this->loadImages()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $now = (new \DateTime());
            $event->setDateCreated($now);
            $event->setCreatorId($this->getUser()->getId());
            $event->setDateUpdated($now);
            $event->setDateTo(\DateTime::createFromFormat('d-m-Y H:i:s', $data->getDateTo()));
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                $event->setSection($data->getSection());
            } else {
                $event->setSection('personal');
            }
            $event->setStatus('not_completed');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Váše událost byla úspěšně vytvořena.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('Action/addEvent.blocek.html.twig', [
            'addForm' => $form->createView(),
            'subtitle' => 'Přidání události',
            'edit' => false
        ]);
    }

    /**
     * @Route("/event/edit/{id}", name="edit_event")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id) : Response
    {
        if ($route = $this->securityCheck()) return $route;

        $event = $this->fetchEventEntityById($id);
        if ($route = $this->checkOwnerShip($event)) return $route;

        $event->setDateTo($event->getDateTo()->format('d-m-Y H:i:s'));
        $event->setDateUpdated($event->getDateUpdated()->format('d-m-Y H:i:s'));
        $event->setDateCreated($event->getDateCreated()->format('d-m-Y H:i:s'));

        $form = $this->createForm(AddEventFormType::class, $event, ['images' => $this->loadImages()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $event->setDateUpdated(new \DateTime());
            $event->setDateCreated(\DateTime::createFromFormat('d-m-Y H:i:s', $event->getDateCreated()));
            $event->setDateTo(\DateTime::createFromFormat('d-m-Y H:i:s', $data->getDateTo()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Váše událost byla úspěšně upravena.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('Action/addEvent.blocek.html.twig', [
            'addForm' => $form->createView(),
            'subtitle' => 'Úprava události',
            'edit' => true
        ]);
    }

    /**
     * @Route("/event/finish/{id}", name="finish_event")
     * @param int $id
     * @return RedirectResponse
     * @throws \Doctrine\DBAL\Exception
     */
    public function finish(int $id): RedirectResponse
    {
        if ($route = $this->securityCheck()) return $route;

        $event = $this->fetchEventEntityById($id);
        if ($route = $this->checkOwnerShip($event)) return $route;

        $event->setStatus("completed");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        $this->addFlash('success', 'Úspěšně jste dokončil událost.');
        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/event/delete/{id}", name="delete_event")
     * @param int $id
     * @param Connection $conn
     * @return RedirectResponse
     */
    public function delete(int $id, Connection $conn) : RedirectResponse
    {
        if ($route = $this->securityCheck()) return $route;

        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->delete('event')
            ->where('id = :event_id')
            ->andWhere('creator_id = :user_id')
            ->setParameter("user_id", $this->getUser()->getId(), ParameterType::INTEGER)
            ->setParameter("event_id", $id, ParameterType::INTEGER)
            ->execute();

        $this->addFlash('success', 'Vaše událost byla úspěšně smazána.');
        return $this->redirectToRoute('app_profile');
    }

    private function securityCheck(): ?RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('error', $this->getParameter('message.user_required'));
            return $this->redirectToRoute('app_login');
        }
        return null;
    }

    private function fetchEventEntityById($id){
        return $this
            ->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);
    }

    private function checkOwnerShip($event): ?RedirectResponse
    {
        if (!$event || $this->getUser()->getId() != $event->getCreatorId()) {
            $this->addFlash('error', $this->getParameter('message.permissions_required'));
            return $this->redirectToRoute('app_profile');
        }
        return null;
    }

    private function loadImages(){
        $imgs = [];
        $package = new Package(new EmptyVersionStrategy());
        if ($handle = opendir($package->getUrl('images'))) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $imgs[$entry] = $entry;
                }
            }
            closedir($handle);
        }
        return $imgs;
    }
}