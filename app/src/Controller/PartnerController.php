<?php

namespace App\Controller;


use App\Entity\Partner;
use App\Entity\Admin;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;



#[Route('/api/partner')]
class PartnerController extends AbstractController
{
    #[Route('/', name: 'getAllPartners', methods: ['GET'])]
    public function getAllPartners(PartnerRepository $partnerRepository): JsonResponse
    {
        $partners = $partnerRepository->findAll();
        $data = [];

        foreach ($partners as $partner) {
            $data[] = [
                'id' => $partner->getId(),
                'email' => $partner->getEmail(),
                'firstName' => $partner->getFirstName(),
                'lastName' => $partner->getLastName(),
                'phoneNumber' => $partner->getPhoneNumber(),
                'companyName' => $partner->getCompanyName(),
                'companyDescription' => $partner->getCompanyDescription(),
                'localisation' => $partner->getLocalisation(),
                'imageFilename' => $partner->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getPartnerByID', methods: ['GET'])]
    public function getPartnerByID(int $id, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->find($id);

        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $partner->getId(),
            'email' => $partner->getEmail(),
            'firstName' => $partner->getFirstName(),
            'lastName' => $partner->getLastName(),
            'phoneNumber' => $partner->getPhoneNumber(),
            'companyName' => $partner->getCompanyName(),
            'companyDescription' => $partner->getCompanyDescription(),
            'localisation' => $partner->getLocalisation(),
            'imageFilename' => $partner->getImageFilename()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/', name: 'createPartner', methods: ['POST'])]
    public function createPartner(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->submit($data);

        $file = $form->get('imageFilename')->getData();
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $partner->setImageFilename($fileName);
        }

        $partner->setPassword(custom_password_hash($data['password'], PASSWORD_BCRYPT));

        // Fetch the Admin entity or use default admin ID 6
        $adminId = $data['admin_id'] ?? 1;
        $admin = $em->getRepository(Admin::class)->find($adminId);
        if (!$admin) {
            return new JsonResponse(['error' => 'Admin with ID ' . $adminId . ' not found'], Response::HTTP_NOT_FOUND);
        }
        $partner->setAdmin($admin);

        $em->persist($partner);
        $em->flush();

        return new JsonResponse(['message' => 'Partner created'], Response::HTTP_CREATED);

    }

    #[Route('/{id}', name: 'updatePartner', methods: ['POST'])]
    public function updatePartner(int $id, Request $request, PartnerRepository $partnerRepository, EntityManagerInterface $em): JsonResponse
    {
        $partner = $partnerRepository->find($id);

        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->request->all();  // Handles the form data part
        $file = $request->files->get('imageFilename');

        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        if (isset($data['firstName'])) {
            $partner->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $partner->setLastName($data['lastName']);
        }

        if (isset($data['phoneNumber'])) {
            $partner->setPhoneNumber($data['phoneNumber']);
        }

        if (isset($data['email'])) {
            $partner->setEmail($data['email']);
        }

        if (isset($data['localisation'])) {
            $partner->setLocalisation($data['localisation']);
        }

        if (isset($data['companyName'])) {
            $partner->setCompanyName($data['companyName']);
        }

        if (isset($data['companyDescription'])) {
            $partner->setCompanyDescription($data['companyDescription']);
        }

        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $partner->setImageFilename($fileName);  // Save filename in DB
        }

        $em->persist($partner);
        $em->flush();


        return new JsonResponse(['status' => 'Partner updated successfully']);
    }

    #[Route('/{id}', name: 'deletePartner', methods: ['DELETE'])]
    public function deletePartner(int $id, PartnerRepository $partnerRepository, EntityManagerInterface $em): JsonResponse
    {
        $partner = $partnerRepository->find($id);
        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($partner);
        $em->flush();

        return new JsonResponse(['message' => 'Partner deleted'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'searchPartners', methods: ['GET'])]
    public function searchPartners(Request $request, PartnerRepository $partnerRepository): JsonResponse
    {
        $query = $request->query->get('query', '');

        // Perform the search based on the query
        $partners = $partnerRepository->searchPartners($query);

        // Convert entities to array
        $data = [];
        foreach ($partners as $partner) {
            $data[] = [
                'id' => $partner->getId(),
                'email' => $partner->getEmail(),
                'firstName' => $partner->getFirstName(),
                'lastName' => $partner->getLastName(),
                'phoneNumber' => $partner->getPhoneNumber(),
                'companyName' => $partner->getCompanyName(),
                'companyDescription' => $partner->getCompanyDescription(),
                'localisation' => $partner->getLocalisation(),
                'roles' => $partner->getRoles(),
                'imageFilename' => $partner->getImageFilename()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}/events', name:'getAllEvents', methods:['GET'])]
    public function getPartnerEvents(int $id, PartnerRepository $partnerRepository, SerializerInterface $serializer, EventRepository $eventRepository): Response
    {
        // Fetch the partner from the database
        $partner = $partnerRepository->find($id);
        
        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], Response::HTTP_NOT_FOUND);
        }
        
        // Fetch events associated with the partner
        $events = $eventRepository->findBy(['owner' => $partner]);
        
        // Serialize the events data
        $jsonEvents = $serializer->serialize($events, 'json', ['groups' => ['event_details']]);
        
        return new JsonResponse($jsonEvents, Response::HTTP_OK, [], true);
    }
}

