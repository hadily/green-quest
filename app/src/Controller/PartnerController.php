<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Admin;
use App\Repository\PartnerRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

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
                'role' => $partner->getRoles(),
                'firstName' => $partner->getFirstName(),
                'lastName' => $partner->getLastName(),
                'companyName' => $partner->getCompanyName(),
                'companyDescription' => $partner->getCompanyDescription(),
                'localisation' => $partner->getLocalisation(),
                'phoneNumber' => $partner->getPhoneNumber(),
                'imageFilename' => $partner->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getPartnerByID', methods: ['GET'], requirements: ['id' => '\d+'])]
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
            'roles' => $partner->getRoles(),
            'imageFilename' => $partner->getImageFilename()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/', name: 'createPartner', methods: ['POST'])]
    public function createPartner(Request $request, EntityManagerInterface $em, UploadFileService $ufService): JsonResponse
    {
        // $data = json_decode($request->getContent(), true);
        $data = $request->request->all();

        $partner = new Partner();

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $ufService->uploadFile($imageFilename);
            $partner->setImageFilename($imageName);
        }

        $partner->setEmail($data['email']);
        $partner->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $partner->setFirstName($data['firstName']);
        $partner->setLastName($data['lastName']);
        $partner->setPhoneNumber($data['phoneNumber']);
        $partner->setRoles($data['roles'] ?? ['PARTNER']);
        $partner->setCompanyName($data['companyName']);
        $partner->setCompanyDescription($data['companyDescription']);
        $partner->setLocalisation($data['localisation']);


        // Fetch the Admin entity or use default admin ID 6
        $adminId = $data['admin_id'] ?? 6;
        $admin = $em->getRepository(Admin::class)->find($adminId);
        if (!$admin) {
            return new JsonResponse(['error' => 'Admin with ID ' . $adminId . ' not found'], Response::HTTP_NOT_FOUND);
        }
        $partner->setAdmin($admin);

        $em->persist($partner);
        $em->flush();

        return new JsonResponse(['message' => 'Partner created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'updatePartner', methods: ['PUT'])]
    public function updatePartner(int $id, Request $request, EntityManagerInterface $em, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->find($id);

        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $partner->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $partner->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
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
        if (isset($data['companyName'])) {
            $partner->setCompanyName($data['companyName']);
        }
        if (isset($data['companyDescription'])) {
            $partner->setCompanyDescription($data['companyDescription']);
        }
        if (isset($data['localisation'])) {
            $partner->setLocalisation($data['localisation']);
        }
        if (isset($data['roles'])) {
            $partner->setRoles($data['roles']);
        }

        $em->persist($partner);
        $em->flush();

        return new JsonResponse(['message' => 'Partner updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'deletePartner', methods: ['DELETE'])]
    public function deletePartner(int $id, EntityManagerInterface $em, PartnerRepository $partnerRepository): JsonResponse
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
            ];
        }

        return new JsonResponse($data);
    }
}

