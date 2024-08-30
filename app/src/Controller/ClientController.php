<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/api/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'getAllClients', methods: ['GET'])]
    public function getAllClients(ClientRepository $clientRepository): JsonResponse
    {
        $clients = $clientRepository->findAll();
        $data = [];

        foreach ($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'email' => $client->getEmail(),
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
        $data = json_decode($request->getContent(), true);
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->submit($data);

        $file = $form->get('imageFilename')->getData();
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $client->setImageFilename($fileName);
        }

        $client->setEmail($data['email']);
        $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        // Fetch the Admin entity or use default admin ID 6
        $adminId = $data['admin_id'] ?? 1;
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
        $client = $clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        $client = $form->getData();

        $file = $form->get('imageFilename')->getData();
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $client->setImageFilename($fileName);
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
            $superAdmin = $userRepository->find(1);
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

        $clients = $clientRepository->searchClients($query);

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

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
