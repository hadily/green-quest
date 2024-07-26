<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\OpenApi\Annotations\Tag;

use OpenApi\Annotations as OA;

#[Route('/api')]
class AdminController extends AbstractController
{
    #[Route('/admin/createSubAdmin', name: 'create_sub_admin', methods: ['POST'])]
    public function createSubAdmin(EntityManagerInterface $em, AdminRepository $adminRepository, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        // $admin = $adminRepository->find($data['id']);

        // if (!$admin) {
        //     return new Response('Error! Admin not found!');
        // }

        $subAdmin = new Admin(
            $data['email'],
            $data['password'],
            'SUB_ADMIN',
            $data['firstName'] ?? null,
            $data['lastName'] ?? null,
            $data['phoneNumber'] ?? null
        );

        // $admin->addSubAdmin($subAdmin);

        $em->persist($subAdmin);
        $em->flush();

        return new Response('Sub-admin created successfully!');
    }
}