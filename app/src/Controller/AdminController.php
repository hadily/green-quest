<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'getAllAdmins', methods: ['GET'])]
    public function getAllAdmins(EntityManagerInterface $em, AdminRepository $adminRepository): JsonResponse
    {
        $admins = $adminRepository->findAll();
        $data = [];

        foreach($admins as $admin) {
            $data[] = [
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'role' => $admin->getRoles(),
                'firstName' => $admin->getFirstName(),
                'lastName' => $admin->getLastName(),
                'phoneNumber' => $admin->getPhoneNumber()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getAdminByID', methods: ['GET'])]
    public function getAdminByID(int $id, EntityManagerInterface $em, AdminRepository $adminRepository): JsonResponse
    {
        $admin = $adminRepository->find($id);
        $data = [];

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $admin->getId(),
            'email' => $admin->getEmail(),
            'firstName' => $admin->getFirstName(),
            'lastName' => $admin->getLastName(),
            'phoneNumber' => $admin->getPhoneNumber(),
            'roles' => $admin->getRoles()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/', name: 'createAdmin', methods: ['POST'])]
    public function createAdmin(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['phoneNumber'])) {
            return new JsonResponse(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $admin = new Admin();
        $admin->setEmail($data['email']);
        $admin->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $admin->setFirstName($data['firstName']);
        $admin->setLastName($data['lastName']);
        $admin->setPhoneNumber($data['phoneNumber']);
        $admin->setRoles($data['roles'] ?? ['ADMIN']);

        $em->persist($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Admin created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'updateAdmin', methods: ['PUT'])]
    public function updateAdmin(int $id, Request $request, EntityManagerInterface $em, AdminRepository $adminRepository): JsonResponse
    {
        $admin = $adminRepository->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], Response::HTTP_NOT_FOUND);
        }

        if ($id === 1 || in_array('SUPER_ADMIN', $admin->getRoles())) {
            return new JsonResponse(['message' => 'Cannot update this admin'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $admin->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['firstName'])) {
            $admin->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $admin->setLastName($data['lastName']);
        }
        if (isset($data['phoneNumber'])) {
            $admin->setPhoneNumber($data['phoneNumber']);
        }
        if (isset($data['roles'])) {
            $admin->setRoles($data['roles']);
        }

        $em->persist($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Admin updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'deleteAdmin', methods: ['DELETE'])]
    public function deleteAdmin(int $id, EntityManagerInterface $em, AdminRepository $adminRepository): JsonResponse
    {
        $admin = $adminRepository->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], Response::HTTP_NOT_FOUND);
        }

        if ($id === 1 || in_array('SUPER_ADMIN', $admin->getRoles())) {
            return new JsonResponse(['message' => 'Cannot delete this admin'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Admin deleted'], Response::HTTP_NO_CONTENT);
    }

    /**
     * APIs Manage Partners Relation
     */

    #[Route('/partners', name: 'admin_partners', methods: ['GET'])]
    public function getPartners(AdminRepository $adminRepository): JsonResponse
    {
        // $admin = $this->getUser(); 
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }

        $partners = $admin->getPartners();

        $partnerData = [];
        foreach ($partners as $partner) {
            $partnerData[] = [
                'id' => $partner->getId(),
                'companyName' => $partner->getCompanyName(),
                'companyDescription' => $partner->getCompanyDescription(),
                'localisation' => $partner->getLocalisation(),
            ];
        }

        return new JsonResponse($partnerData, JsonResponse::HTTP_OK);
    }

    #[Route('/partners/{id}', name: 'admin_add_partner', methods: ['POST'])]
    public function addPartner(int $id, PartnerRepository $partnerRepository, EntityManagerInterface $em): JsonResponse
    {
        // $admin = $this->getUser();
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }
    
        $partner = $partnerRepository->find($id);
        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $admin->addPartner($partner);
        $em->persist($admin);
        $em->flush();
    
        return new JsonResponse(['message' => 'Partner added successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/partners/{id}', name: 'admin_remove_partner', methods: ['DELETE'])]
    public function removePartner(int $id, PartnerRepository $partnerRepository, EntityManagerInterface $em): JsonResponse
    {
        // $admin = $this->getUser();
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }

        $partner = $partnerRepository->find($id);
        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $admin->removePartner($partner);
        $em->persist($admin);
        $em->flush();
    
        return new JsonResponse(['message' => 'Partner removed successfully'], JsonResponse::HTTP_OK);
    }

    /**
     * APIs Manage Clients Relation
     */

    #[Route('/clients', name: 'admin_get_clients', methods: ['GET'])]
    public function getClients(ClientRepository $clientRepository): JsonResponse
    {
        // $admin = $this->getUser();
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }
    
        $clients = $clientRepository->findBy(['admin' => $admin]);
    
        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = [
                'id' => $client->getId(),
                'firstName' => $client->getFirstName(),
                'lastName' => $client->getLastName(),
            ];
        }
    
        return new JsonResponse($clientData, JsonResponse::HTTP_OK);
    }

     #[Route('/clients/{id}', name: 'admin_add_client', methods: ['POST'])]
    public function addClient(int $id, ClientRepository $clientRepository, EntityManagerInterface $em): JsonResponse
    {
        // $admin = $this->getUser();
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }

        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $admin->addClient($client);
        $em->persist($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Client added successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/clients/{id}', name: 'admin_remove_client', methods: ['DELETE'])]
    public function removeClient(int $id, ClientRepository $clientRepository, EntityManagerInterface $em): JsonResponse
    {
        // $admin = $this->getUser();
        // if (!$admin instanceof Admin) {
        //     return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        // }

        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $admin->removeClient($client);
        $em->persist($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Client removed successfully'], JsonResponse::HTTP_OK);
    }


    
}
