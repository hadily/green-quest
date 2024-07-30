<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

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
                'localisation' => $client->getLocalisation()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'getClientById', methods: ['GET'])]
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
            'localisation' => $client->getLocalisation()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'createClient', methods: ['POST'])]
    public function createClient(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setEmail($data['email']);
        $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setPhoneNumber($data['phoneNumber']);
        $client->setRoles($data['roles'] ?? ['ROLE_CLIENT']);
        $client->setLocalisation($data['localisation']);

        $em->persist($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'updateClient', methods: ['PUT'])]
    public function updateClient(int $id, Request $request, EntityManagerInterface $em, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $client->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['firstName'])) {
            $client->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $client->setLastName($data['lastName']);
        }
        if (isset($data['phoneNumber'])) {
            $client->setPhoneNumber($data['phoneNumber']);
        }
        if (isset($data['roles'])) {
            $client->setRoles($data['roles']);
        }
        if (isset($data['localisation'])) {
            $client->setLocalisation($data['localisation']);
        }

        $em->persist($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client updated'], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'deleteClient', methods: ['DELETE'])]
    public function deleteClient(int $id, EntityManagerInterface $em, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->remove($client);
        $em->flush();

        return new JsonResponse(['message' => 'Client deleted'], JsonResponse::HTTP_NO_CONTENT);
    }
}
