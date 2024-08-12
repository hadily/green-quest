<?php

namespace App\Controller;

use App\Entity\Complaints;
use App\Entity\Admin;
use App\Entity\User;
use App\Form\ComplaintsType;
use App\Repository\ComplaintsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/complaints')]
class ComplaintsController extends AbstractController
{
    #[Route('/', name: 'getAllComplaints', methods: ['GET'])]
    public function getAllComplaints(EntityManagerInterface $em, ComplaintsRepository $complaintRepository): JsonResponse
    {
        $complaints = $complaintRepository->findAll();
        $data = [];

        foreach ($complaints as $complaint) {
            $owner = $complaint->getOwner(); // Get the owner (User object)
            $ownerName = $owner ? $owner->getFullName() : null; // Get the owner's name
            
            $admin = $complaint->getAdmin(); // Get the admin (Admin object)
            $adminName = $admin ? $admin->getFullName() : null; // Get the admin's name

            $data[] = [
                'id' => $complaint->getId(),
                'subject' => $complaint->getSubject(),
                'details' => $complaint->getDetails(),
                'reply' => $complaint->getReply(),
                'owner' => $ownerName, // Get the full name of the owner
                'admin' => $adminName, // Get the full name of the admin
                'status' => $complaint->getStatus(),
                'date' => $complaint->getDate()->format('Y-m-d'),
                'relatedTo' => $complaint->getRelatedTo() ? $complaint->getRelatedTo()->getTitle() : null, // Get related article title
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getComplaintByID', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getComplaintByID(int $id, ComplaintsRepository $complaintRepository): JsonResponse
    {
        $complaint = $complaintRepository->find($id);

        if (!$complaint) {
            return new JsonResponse(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $owner = $complaint->getOwner();
        $ownerName = $owner ? $owner->getFullName() : null;

        $admin = $complaint->getAdmin();
        $adminName = $admin ? $admin->getFullName() : null;

        $data = [
            'id' => $complaint->getId(),
            'subject' => $complaint->getSubject(),
            'details' => $complaint->getDetails(),
            'reply' => $complaint->getReply(),
            'owner' => $ownerName,
            'admin' => $adminName,
            'status' => $complaint->getStatus(),
            'date' => $complaint->getDate()->format('Y-m-d'),
            'relatedTo' => $complaint->getRelatedTo() ? $complaint->getRelatedTo()->getTitle() : null,
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/', name: 'createComplaint', methods: ['POST'])]
    public function createComplaint(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $complaint = new Complaints();
        $complaint->setSubject($data['subject']);
        $complaint->setDetails($data['details']);
        $complaint->setStatus($data['status']);
        $complaint->setReply($data['reply']);
        $complaint->setDate(new \DateTime());

        // Fetch the User entity for owner
        $ownerId = $data['ownerId'];
        $owner = $em->getRepository(User::class)->find($ownerId);
        if (!$owner) {
            return new JsonResponse(['error' => 'User with ID ' . $ownerId . ' not found'], Response::HTTP_NOT_FOUND);
        }
        $complaint->setOwner($owner);

        // Fetch the Admin entity for admin
        $adminId = $data['adminId'];
        $admin = $em->getRepository(Admin::class)->find($adminId);
        if (!$admin) {
            return new JsonResponse(['error' => 'Admin with ID ' . $adminId . ' not found'], Response::HTTP_NOT_FOUND);
        }
        $complaint->setAdmin($admin);

        $em->persist($complaint);
        $em->flush();

        return new JsonResponse(['message' => 'Complaint created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'updateComplaint', methods: ['PUT'])]
    public function updateComplaint(int $id, Request $request, EntityManagerInterface $em, ComplaintsRepository $complaintRepository): JsonResponse
    {
        $complaint = $complaintRepository->find($id);

        if (!$complaint) {
            return new JsonResponse(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['subject'])) {
            $complaint->setSubject($data['subject']);
        }
        if (isset($data['details'])) {
            $complaint->setDetails($data['details']);
        }
        if (isset($data['status'])) {
            $complaint->setStatus($data['status']);
        }
        if (isset($data['date'])) {
            $complaint->setDate(new \DateTime());
        }
        if (isset($data['reply'])) {
            $complaint->setReply($data['reply']);
        }

        $em->persist($complaint);
        $em->flush();

        return new JsonResponse(['message' => 'Complaint updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'deleteComplaint', methods: ['DELETE'])]
    public function deleteComplaint(int $id, EntityManagerInterface $em, ComplaintsRepository $complaintRepository): JsonResponse
    {
        $complaint = $complaintRepository->find($id);

        if (!$complaint) {
            return new JsonResponse(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($complaint);
        $em->flush();

        return new JsonResponse(['message' => 'Complaint deleted'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'searchComplaints', methods: ['GET'])]
    public function searchComplaints(Request $request, ComplaintsRepository $complaintRepository): JsonResponse
    {
        $query = $request->query->get('query', '');

        // Perform the search based on the query
        $complaints = $complaintRepository->searchComplaints($query);

        // Convert entities to array
        $data = [];
        foreach ($complaints as $complaint) {
            $owner = $complaint->getOwner();
            $ownerName = $owner ? $owner->getFullName() : null;

            $admin = $complaint->getAdmin();
            $adminName = $admin ? $admin->getFullName() : null;

            $data[] = [
                'id' => $complaint->getId(),
                'subject' => $complaint->getSubject(),
                'details' => $complaint->getDetails(),
                'reply' => $complaint->getReply(),
                'owner' => $ownerName,
                'admin' => $adminName,
                'status' => $complaint->getStatus(),
                'date' => $complaint->getDate()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/client-complaints', name: 'getClientComplaints', methods: ['GET'])]
    public function getClientComplaints(ComplaintsRepository $complaintRepository): JsonResponse
    {
        // Fetch all complaints
        $complaints = $complaintRepository->findAll();
        $data = [];
    
        foreach ($complaints as $complaint) {
            $owner = $complaint->getOwner();
    
            // Check if the owner is a Client
            if ($owner->hasRole('CLIENT')) { // Assuming hasRole checks the user's roles
                $ownerName = $owner ? $owner->getFullName() : null;
                $adminName = $complaint->getAdmin() ? $complaint->getAdmin()->getFullName() : null;
                $relatedArticleTitle = $complaint->getRelatedTo() ? $complaint->getRelatedTo()->getTitle() : null;
    
                $data[] = [
                    'id' => $complaint->getId(),
                    'subject' => $complaint->getSubject(),
                    'details' => $complaint->getDetails(),
                    'reply' => $complaint->getReply(),
                    'status' => $complaint->getStatus(),
                    'owner' => $ownerName,
                    'admin' => $adminName,
                    'relatedTo' => $relatedArticleTitle,
                    'date' => $complaint->getDate()->format('Y-m-d')
                ];
            }
        }
    
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/partner-complaints', name: 'getPartnerComplaints', methods: ['GET'])]
    public function getPartnertComplaints(ComplaintsRepository $complaintRepository): JsonResponse
    {
        // Fetch all complaints
        $complaints = $complaintRepository->findAll();
        $data = [];
    
        foreach ($complaints as $complaint) {
            $owner = $complaint->getOwner();
    
            // Check if the owner is a Client
            if ($owner->hasRole('PARTNER')) { // Assuming hasRole checks the user's roles
                $ownerName = $owner ? $owner->getFullName() : null;
                $adminName = $complaint->getAdmin() ? $complaint->getAdmin()->getFullName() : null;
                $relatedArticleTitle = $complaint->getRelatedTo() ? $complaint->getRelatedTo()->getTitle() : null;
    
                $data[] = [
                    'id' => $complaint->getId(),
                    'subject' => $complaint->getSubject(),
                    'details' => $complaint->getDetails(),
                    'reply' => $complaint->getReply(),
                    'status' => $complaint->getStatus(),
                    'owner' => $ownerName,
                    'admin' => $adminName,
                    'relatedTo' => $relatedArticleTitle,
                    'date' => $complaint->getDate()->format('Y-m-d')
                ];
            }
        }
    
        return new JsonResponse($data, Response::HTTP_OK);
    }

}
