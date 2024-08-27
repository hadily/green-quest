<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Admin;
use App\Entity\Article;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/api/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'getAllClients', methods: ['GET'])]
    public function getAllClients(ClientRepository $clientRepository): JsonResponse
    {
        $clients = $clientRepository->findAll();
        $data = [];

        foreach($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'email' => $client->getEmail(),
                'roles' => $client->getRoles(),
                'firstName' => $client->getFirstName(),
                'lastName' => $client->getLastName(),
                'phoneNumber' => $client->getPhoneNumber(),
                'localisation' => $client->getLocalisation(),
                'imageFilename' => $client->getImageFilename()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'getClientById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getClientById(int $id, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $client->getId(),
            'email' => $client->getEmail(),
            'roles' => $client->getRoles(),
            'firstName' => $client->getFirstName(),
            'lastName' => $client->getLastName(),
            'phoneNumber' => $client->getPhoneNumber(),
            'localisation' => $client->getLocalisation(),
            'imageFilename' => $client->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'createClient', methods: ['POST'])]
    public function createClient(Request $request, EntityManagerInterface $em, UploadFileService $ufService): JsonResponse
    {
        // $data = json_decode($request->getContent(), true);
        $data = $request->request->all();

        $client = new Client();

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $ufService->uploadFile($imageFilename);
            $client->setImageFilename($imageName);
        }

        $client->setEmail($data['email']);
        $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setPhoneNumber($data['phoneNumber']);
        $client->setRoles($data['roles'] ?? ['CLIENT']);
        $client->setLocalisation($data['localisation']);
        
        // Fetch the Admin entity or use default admin ID 6
        $adminId = $data['admin_id'] ?? 6;
        $admin = $em->getRepository(Admin::class)->find($adminId);
        if (!$admin) {
            return new JsonResponse(['error' => 'Admin with ID ' . $adminId . ' not found'], Response::HTTP_NOT_FOUND);
        }
        $client->setAdmin($admin);

        $em->persist($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'updateClient', methods: ['POST'])]
    public function updateClient(int $id, Request $request, EntityManagerInterface $em, ClientRepository $clientRepository, UploadFileService $ufService): JsonResponse
    {
        $client = $clientRepository->find($id); // Get the logged-in client or fetch client by ID

        // Handle file upload
        /** @var UploadedFile|null $file */
        $file = $request->files->get('imageFilename');

        if ($file instanceof UploadedFile) {
            $imageName = $ufService->uploadFile($file);
            $client->setImageFilename($imageName); // Assuming you have a method to set the file name
        }

        // Extract data from the request
        $email = $request->request->get('email');
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $phoneNumber = $request->request->get('phoneNumber');
        $localisation = $request->request->get('localisation');

        // Update client details if the provided values are not null
        if ($email !== null) {
            $client->setEmail($email);
        }
        if ($firstName !== null) {
            $client->setFirstName($firstName);
        }
        if ($lastName !== null) {
            $client->setLastName($lastName);
        }
        if ($phoneNumber !== null) {
            $client->setPhoneNumber($phoneNumber);
        }
        if ($localisation !== null) {
            $client->setLocalisation($localisation);
        }
        
        $em->persist($client);
        $em->flush();

        return new JsonResponse(['status' => 'Client updated successfully']);
    }

    #[Route('/{id}', name: 'deleteClient', methods: ['DELETE'])]
    public function deleteClient(int $id, EntityManagerInterface $em, ClientRepository $clientRepository, UserRepository $userRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $articles = $em->getRepository(Article::class)->findBy(['writer' => $client]);

        foreach ($articles as $article) {
            // Fetch the user with ID 6
            $superAdmin = $userRepository->find(6);
            
            // Set the writer to the Super Admin
            $article->setWriter($superAdmin);

            $em->persist($article);
        }

        $em->remove($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client deleted'], JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'searchClients', methods: ['GET'])]
    public function searchClients(Request $request, ClientRepository $clientRepository): JsonResponse
    {
        $query = $request->query->get('query', '');

        // Perform the search based on the query
        $clients = $clientRepository->searchClients($query);

        // Convert entities to array
        $data = [];
        foreach ($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'email' => $client->getEmail(),
                'firstName' => $client->getFirstName(),
                'lastName' => $client->getLastName(),
                'phoneNumber' => $client->getPhoneNumber(),
                'localisation' => $client->getLocalisation(),
                'roles' => $client->getRoles(),
                'imageFilename' => $client->getImageFilename()
            ];
        }

        return new JsonResponse($data);
    }

}
