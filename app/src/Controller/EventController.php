<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EventRepository;
use App\Repository\PartnerRepository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use App\Form\EventType;
use Psr\Log\LoggerInterface;




#[Route('/api/event')]
class EventController extends AbstractController
{
    private $eventRepository;
    private $partnerRepository;
    private $entityManager;
    private $logger;


    public function __construct(
        EventRepository $eventRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->eventRepository = $eventRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/', name: 'list_events', methods: ['GET'])]
    public function listEvents(): JsonResponse
    {
        $events = $this->eventRepository->findAll();

        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'startDate' => $event->getStartDate()?->format('Y-m-d'),
                'endDate' => $event->getEndDate()?->format('Y-m-d'),
                'price' => $event->getPrice(),            
                'nbParticipants' => $event->getNbParticipants(),
                'organizer' => $event->getOrganizer()->getId(),
                'category' => $event->getCategory(),
                'imageFilename' => $event->getImageFilename()

            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'getEventeById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getEventById(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $event->getId(),
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'startDate' => $event->getStartDate()?->format('Y-m-d'),
            'endDate' => $event->getEndDate()?->format('Y-m-d'),
            'price' => $event->getPrice(),
            'nbParticipants' => $event->getNbParticipants(),
            'organizer' => $event->getOrganizer()->getId(),
            'category' => $event->getCategory(),
            'imageFilename' => $event->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

   

    #[Route('/', name: 'create_event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        // Create a new Event entity
        $event = new Event();

        // Manually set startDate and endDate
        if (isset($data['startDate'])) {
            $event->setStartDate(new \DateTime($data['startDate']));
        }
    
        if (isset($data['endDate'])) {
            $event->setEndDate(new \DateTime($data['endDate']));
        }

        // Handle file upload
        $file = $request->files->get('imageFilename');
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $event->setImageFilename($fileName);
        }

        
        $form = $this->createForm(EventType::class, $event, [
            'allow_extra_fields' => true,  // Allow extra fields
            'csrf_protection' => false
        ]);

        // Handle form submission
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Persist and flush
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Event created'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'update_event', methods: ['PUT'])]
    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['message' => 'Event not found']);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        if (isset($data['name'])) {
            $event->setName($data['name']);
        }

        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }

        if (isset($data['startDate'])) {
            $startDate = \DateTime::createFromFormat('Y-m-d', $data['startDate']);
            $event->setStartDate($startDate);
        }

        if (isset($data['endDate'])) {
            $endDate = \DateTime::createFromFormat('Y-m-d', $data['endDate']); 
            $event->setEndDate($endDate);
        }

        if (isset($data['category'])) {
            $event->setCategory($data['category']);
        }

        if (isset($data['price'])) {
            $event->setPrice($data['price']);
        }

        if (isset($data['nbParticipants'])) {
            $event->setNbParticipants($data['nbParticipants']);
        }
        
        $imageFile = $request->files->get('imageFilename');
        if ($imageFile) {
            $imageName = $ufService->uploadFile($imageFile);
            $event->setImageFilename($imageName);
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Event updated successfully']);
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

        $events = $this->eventRepository->findBy(['organizer' => $partner]);

        if (!$events) {
            return new JsonResponse(['message' => 'No events found for this partner'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'startDate' => $event->getStartDate()?->format('Y-m-d'),
                'endDate' => $event->getEndDate()?->format('Y-m-d'),
                'price' => $event->getPrice(),
                'nbParticipants' => $event->getNbParticipants(),
                'organizer' => $event->getOrganizer()->getId(),
                'imageFilename' => $event->getImageFilename()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

}
