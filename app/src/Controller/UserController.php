<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



#[Route('/api/user')]
class UserController extends AbstractController
{
    private $security;
    private $passwordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'getCurretUser', methods: ['GET'])]
    public function getCurrentUser(EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Return user data as needed
        return new JsonResponse([
            'id' => $userRepository->getId(),
            'email' => $userRepository->getEmail(),
        ]);
    }

    #[Route('/all', name: 'getAllUsers', methods: ['GET'])]
    public function getUsers(EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = [];

        foreach($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRoles(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'phoneNumber' => $user->getPhoneNumber()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getUserByID', methods: ['GET'])]
    public function getUserByID(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phoneNumber' => $user->getPhoneNumber(),
            'roles' => $user->getRoles(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // #[Route('/', name: 'createUser', methods: ['POST'])]
    // public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $user = new User();
    //     $user->setEmail($data['email']);
    //     $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
    //     $user->setFirstName($data['firstName']);
    //     $user->setLastName($data['lastName']);
    //     $user->setPhoneNumber($data['phoneNumber']);
    //     $user->setRoles($data['roles'] ?? ['USER']);

    //     $entityManager->persist($user);
    //     $entityManager->flush();

    //     return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    // }

    #[Route('/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($id === 1 || in_array('SUPER_USER', $user->getRoles())) {
            return new JsonResponse(['message' => 'Cannot update this user'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['phoneNumber'])) {
            $user->setPhoneNumber($data['phoneNumber']);
        }
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
    }

    #[Route('/reset-password/{id}', name: 'reset_password', methods: ['PUT'])]
    public function resetPassword(int $id, Request $request, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        $currentPassword = $data['currentPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;
    
        $user = $userRepository->find($id);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse(['message' => 'Current password is incorrect'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!$newPassword || strlen($newPassword) < 8) {
            return new JsonResponse(['message' => 'New password is too short'], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Password updated successfully'], Response::HTTP_OK);
    }

    // #[Route('/{id}', name: 'deleteUser', methods: ['DELETE'])]
    // public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    // {
    //     $user = $userRepository->find($id);

    //     if (!$user) {
    //         return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     if ($id === 1 || in_array('SUPER_USER', $user->getRoles())) {
    //         return new JsonResponse(['message' => 'Cannot delete this user'], Response::HTTP_FORBIDDEN);
    //     }

    //     $entityManager->remove($user);
    //     $entityManager->flush();

    //     return new JsonResponse(['message' => 'User deleted'], Response::HTTP_NO_CONTENT);
    // }
}
