<?php

namespace App\Controller\frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Event;
use App\Repository\EventRepository;

#[Route('/event', name: 'frontend_event')]
class EventController extends AbstractController
{
    private $eventRepository;

    public function __construct(
        EventRepository $eventRepository
    ) { 
        $this->eventRepository = $eventRepository;
    }

    #[Route('', name: '_list')]
    public function index()
    {
        $events = $this->eventRepository->getAll();
        return $this->render('frontend/event/list.html.twig', ['list'=>$events]);
    }

    #[Route('/{id}/detail', name: '_detail')]
    public function detail(Event $event)
    {
        return $this->render('frontend/event/detail.html.twig', ['blog'=>$event]);
    }
}