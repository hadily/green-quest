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
use App\Service\UploadFileService;
use Symfony\Component\HttpFoundation\File\UploadedFile; 



#[Route('/api/event')]
class EventController extends AbstractController
{
    private $eventRepository;
    private $partnerRepository;
    private $entityManager;
    private $validator;
    private $ufService;

    public function __construct(
        EventRepository $eventRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UploadFileService $ufService
    ) {
        $this->eventRepository = $eventRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->ufService = $ufService;
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
            'available' => $event->isAvailable(),
            'imageFilename' => $event->getImageFilename()
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
                'available' => $event->isAvailable(),
                'imageFilename' => $event->getImageFilename()

            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'create_event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        $data = $request->request->all();

        $event = new Event();

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $this->ufService->uploadFile($imageFilename);
            $event->setImageFilename($imageName);
        }

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

    #[Route('/{id}', name: 'update_event', methods: ['POST'])]
    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $this->ufService->uploadFile($imageFilename);
            $event->setImageFilename($imageName);
        } else {
            return new JsonResponse(['error' => 'Image is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Extract data from the request
        $serviceName = $request->request->get('serviceName');
        $description = $request->request->get('description');
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $price = $request->request->get('price');
        $available = $request->request->get('available');

        if (isset($data['serviceName'])) {
          $event->setServiceName($data['serviceName']);
        }
        if (isset($data['description'])) {
          $event->setDescription($data['description']);
        }
        if (isset($data['startDate'])) {
          $event->setStartDate($data['startDate']);
        }
        if (isset($data['endDate'])) {
          $event->setEndDate($data['endDate']);
        }
        if (isset($data['price'])) {
          $event->setPrice($data['price']);
        }
        if (isset($data['available'])) {
          $event->setAvailable($data['available']);
        }

        $this->entityManager->flush();

        return new JsonResponse(['event created']);
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

    #[Route('/owner/{id}', name: 'getEventsByOwner', methods: ['GET'])]
    public function getEventsByOwner(int $id): JsonResponse
    {
        $partner = $this->partnerRepository->find($id);

        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $events = $this->eventRepository->findBy(['owner' => $partner]);

        if (!$events) {
            return new JsonResponse(['message' => 'No events found for this partner'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'serviceName' => $event->getServiceName(),
                'description' => $event->getDescription(),
                'startDate' => $event->getStartDate()?->format('Y-m-d'),
                'endDate' => $event->getEndDate()?->format('Y-m-d'),
                'price' => $event->getPrice(),
                'ownerId' => $event->getOwner()->getId(),
                'available' => $event->isAvailable(),
                'imageFilename' => $event->getImageFilename()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

}
