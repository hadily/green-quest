<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\UploadFileService;

#[Route('/api/user')]
class UserController extends AbstractController
{
    private $security;
    private $passwordHasher;
    private $formFactory;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher, FormFactoryInterface $formFactory)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
        $this->formFactory = $formFactory;
    }

    #[Route('/new', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UploadFileService $ufService): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($id === 1 || in_array('SUPER_USER', $user->getRoles())) {
            return new JsonResponse(['message' => 'Cannot update this user'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile|null $file */
            $file = $request->files->get('imageFilename');

            if ($file instanceof UploadedFile) {
                $imageName = $ufService->uploadFile($file);
                $user->setImageFilename($imageName);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Invalid form data'], Response::HTTP_BAD_REQUEST);
    }
}
