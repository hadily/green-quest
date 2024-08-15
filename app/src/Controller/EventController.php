<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EventRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Event;

#[Route('/api/event')]
class EventController extends AbstractController
{
    private $eventRepository;
    private $partnerRepository;
    private $entityManager;
    private $validator;

    public function __construct(
        EventRepository $eventRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->eventRepository = $eventRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/{id}', name: 'get_event', methods: ['GET'])]
    public function getEvent(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $event->getId(),
            'serviceName' => $event->getServiceName(),
            'description' => $event->getDescription(),
            'startDate' => $event->getStartDate()?->format('Y-m-d'),
            'endDate' => $event->getEndDate()?->format('Y-m-d'),
            'price' => $event->getPrice(),
            'ownerId' => $event->getOwner()->getId(),
            'available' => $event->isAvailable()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'list_events', methods: ['GET'])]
    public function listEvents(): JsonResponse
    {
        $events = $this->eventRepository->findAll();

        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'serviceName' => $event->getServiceName(),
                'description' => $event->getDescription(),
                'startDate' => $event->getStartDate()?->format('Y-m-d'),
                'endDate' => $event->getEndDate()?->format('Y-m-d'),
                'price' => $event->getPrice(),
                'ownerId' => $event->getOwner()->getId(),
                'available' => $event->isAvailable()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'create_event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $event = new Event();
        $event->setServiceName($data['serviceName']);
        $event->setDescription($data['description'] ?? '');
        $event->setStartDate(isset($data['startDate']) ? new \DateTime($data['startDate']) : null);
        $event->setEndDate(isset($data['endDate']) ? new \DateTime($data['endDate']) : null);
        $event->setPrice($data['price'] ?? null);
        $event->setAvailable($data['available'] ?? false);

        $owner = $this->partnerRepository->find($data['ownerId']);
        if (!$owner) {
            $owner = $this->partnerRepository->find(15);
        }
        $event->setOwner($owner);


        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Event created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_event', methods: ['PUT'])]
    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $event->setServiceName($data['serviceName'] ?? $event->getServiceName());
        $event->setDescription($data['description'] ?? $event->getDescription());
        $event->setStartDate(isset($data['startDate']) ? new \DateTime($data['startDate']) : $event->getStartDate());
        $event->setEndDate(isset($data['endDate']) ? new \DateTime($data['endDate']) : $event->getEndDate());
        $event->setPrice($data['price'] ?? $event->getPrice());

        $owner = $this->partnerRepository->find($data['ownerId'] ?? $event->getOwner()->getId());
        if (!$owner) {
            return new JsonResponse(['message' => 'Owner not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $event->setOwner($owner);

        $errors = $this->validator->validate($event);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $event->getId(),
            'serviceName' => $event->getServiceName(),
            'description' => $event->getDescription(),
            'startDate' => $event->getStartDate()?->format('Y-m-d'),
            'endDate' => $event->getEndDate()?->format('Y-m-d'),
            'price' => $event->getPrice(),
            'ownerId' => $event->getOwner()->getId(),
        ]);
    }

    #[Route('/{id}', name: 'delete_event', methods: ['DELETE'])]
    public function deleteEvent(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Event deleted successfully']);
    }

}
