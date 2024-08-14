<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/service')]
class ServiceController extends AbstractController
{
    #[Route('/{id}', name: 'get_service', methods: ['GET'])]
    public function getService(int $id, ServiceRepository $serviceRepository, SerializerInterface $serializer): JsonResponse
    {
        $service = $serviceRepository->find($id);

        if (!$service) {
            return new JsonResponse(['message' => 'Service not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $service->getId(),
            'serviceName' => $service->getServiceName(),
            'description' => $service->getDescription(),
            'startDate' => $service->getStartDate()?->format('Y-m-d'),
            'endDate' => $service->getEndDate()?->format('Y-m-d'),
            'price' => $service->getPrice(),
            'maxParticipants' => $service->getMaxParticipants(),
            'available' => $service->isAvailable(),
            'promotion' => $service->isPromotion(),
            'ownerId' => $service->getOwner()->getId(),
        ]);
    }

    #[Route('/', name: 'list_services', methods: ['GET'])]
    public function listServices(ServiceRepository $serviceRepository, SerializerInterface $serializer): JsonResponse
    {
        $services = $serviceRepository->findAll();

        $data = array_map(function (Service $service) {
            return [
                'id' => $service->getId(),
                'serviceName' => $service->getServiceName(),
                'description' => $service->getDescription(),
                'startDate' => $service->getStartDate()?->format('Y-m-d'),
                'endDate' => $service->getEndDate()?->format('Y-m-d'),
                'price' => $service->getPrice(),
                'maxParticipants' => $service->getMaxParticipants(),
                'available' => $service->isAvailable(),
                'promotion' => $service->isPromotion(),
                'ownerId' => $service->getOwner()->getId(),
            ];
        }, $services);

        return new JsonResponse($data);
    }


}
