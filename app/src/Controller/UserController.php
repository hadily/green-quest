<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/allUsers', name: 'get_all_users')]
    public function getAllUsers(UserRepository $userRepository): Response
    {
        // Fetch all users from the repository
        $users = $userRepository->findAll();

        // Transform user entities into a serializable format
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'phoneNumber' => $user->getPhoneNumber(),
                'role' => $user->getRole(),
            ];
        }

        return new JsonResponse($data);
    }

}
