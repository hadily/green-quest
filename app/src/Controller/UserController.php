<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserType;
use App\Service\UploadFileService;


#[Route('/api/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'getUsers', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): JsonResponse
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
                'phoneNumber' => $user->getPhoneNumber(),
                'imageFilename' => $user->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getUserByID', methods: ['GET'])]
    public function getUserByID(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phoneNumber' => $user->getPhoneNumber(),
            'roles' => $user->getRoles(),
            'imageFilename' => $user->getImageFilename()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'updateUser', methods: ['POST'])]
    public function updateUser(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $em, UploadFileService $ufService): JsonResponse
    {
        $user = $userRepository->find($id);
    
        if (!$user) {
            return new JsonResponse(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
    
        // Use the form just like in createPartner
        $form = $this->createForm(UserType::class, $user, [
            'allow_extra_fields' => true,  // Allow extra fields if necessary
            'csrf_protection' => false
        ]);
    
        $imageFile = $request->files->get('imageFilename');
        if ($imageFile) {
            $imageName = $ufService->uploadFile($imageFile);
            $user->setImageFilename($imageName);
        }
    
        // Submit form data
        $form->submit($request->request->all());
    
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Persist the changes
        $em->persist($user);
        $em->flush();
    
        return new JsonResponse(['message' => 'user updated successfully'], JsonResponse::HTTP_OK);
    }

}
