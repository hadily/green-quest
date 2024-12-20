<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use App\Repository\PartnerRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\UploadFileService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



#[Route('/api/admin')]
class AdminController extends AbstractController
{
    private $uploadFileService;

    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
    }


    #[Route('/', name: 'getAllAdmins', methods: ['GET'])]
    public function getAllAdmins(AdminRepository $adminRepository): JsonResponse
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
                'phoneNumber' => $admin->getPhoneNumber(),
                'imageFilename' => $admin->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getAdminByID', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getAdminByID(int $id, AdminRepository $adminRepository): JsonResponse
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
            'roles' => $admin->getRoles(),
            'imageFilename' => $admin->getImageFilename()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/', name: 'createAdmin', methods: ['POST'])]
    public function createAdmin(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $admin = new Admin();
    
        // Get all request parameters
        $data = $request->request->all();
    
        // Handle file upload
        $file = $request->files->get('imageFilename');
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $admin->setImageFilename($fileName);
        }
    
        // Create and submit the form
        $form = $this->createForm(AdminType::class, $admin, [
            'allow_extra_fields' => true,  // Allow extra fields
            'csrf_protection' => false
        ]);
        $form->submit($data);
    
        // Check if the form is valid
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Persist and flush the admin entity
        $em->persist($admin);
        $em->flush();
    
        return new JsonResponse(['message' => 'Admin created'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'updateAdmin', methods: ['POST'])]
    public function updateAdmin(int $id, Request $request, EntityManagerInterface $em, AdminRepository $adminRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Find the admin entity
        $admin = $adminRepository->find($id);
    
        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Decode JSON data from the request
        $data = json_decode($request->getContent(), true);
    
        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }
    
        // Update fields if they exist in the request data
        if (isset($data['firstName'])) {
            $admin->setFirstName($data['firstName']);
        }
    
        if (isset($data['lastName'])) {
            $admin->setLastName($data['lastName']);
        }
    
        if (isset($data['phoneNumber'])) {
            $admin->setPhoneNumber($data['phoneNumber']);
        }
    
        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $admin->setPassword($data['password'], $passwordHasher);
        }
    
        // Handle file upload
        $file = $request->files->get('imageFilename');
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $admin->setImageFilename($fileName);
        }
    
        // Persist changes to the database
        $em->persist($admin);
        $em->flush();
    
        return new JsonResponse(['message' => 'Admin updated successfully']);
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
        $admin = $this->getUser(); 
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }

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
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }
    
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
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }

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
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }
    
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
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }

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
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            return new JsonResponse(['message' => 'Access Denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $admin->removeClient($client);
        $em->persist($admin);
        $em->flush();

        return new JsonResponse(['message' => 'Client removed successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/search', name: 'searchAdmins', methods: ['GET'])]
    public function searchAdmins(Request $request, AdminRepository $adminRepository): JsonResponse
    {
        $query = $request->query->get('query', '');

        // Perform the search based on the query
        $admins = $adminRepository->searchAdmins($query);

        // Convert entities to array
        $data = [];
        foreach ($admins as $admin) {
            $data[] = [
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'firstName' => $admin->getFirstName(),
                'lastName' => $admin->getLastName(),
                'phoneNumber' => $admin->getPhoneNumber(),
                'roles' => $admin->getRoles(),
                'imageFilename' => $admin->getImageFilename()
            ];
        }

        return new JsonResponse($data);
    }


    
}
