<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PartnerRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/product')]
class ProductController extends AbstractController
{
    private $productRepository;
    private $partnerRepository;
    private $entityManager;
    private $validator;

    public function __construct(
        ProductRepository $productRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->productRepository = $productRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $product->getId(),
            'serviceName' => $product->getServiceName(),
            'description' => $product->getDescription(),
            'startDate' => $product->getStartDate()?->format('Y-m-d'),
            'endDate' => $product->getEndDate()?->format('Y-m-d'),
            'price' => $product->getPrice(),
            'maxParticipants' => $product->getMaxParticipants(),
            'available' => $product->isAvailable(),
            'promotion' => $product->isPromotion(),
            'ownerId' => $product->getOwner()->getId(),
        ]);
    }

    #[Route('/', name: 'list_products', methods: ['GET'])]
    public function listProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();

        $data = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'serviceName' => $product->getServiceName(),
                'description' => $product->getDescription(),
                'startDate' => $product->getStartDate()?->format('Y-m-d'),
                'endDate' => $product->getEndDate()?->format('Y-m-d'),
                'price' => $product->getPrice(),
                'maxParticipants' => $product->getMaxParticipants(),
                'available' => $product->isAvailable(),
                'promotion' => $product->isPromotion(),
                'ownerId' => $product->getOwner()->getId(),
            ];
        }, $products);

        return new JsonResponse($data);
    }

    #[Route('/', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['serviceName'], $data['ownerId'])) {
            return new JsonResponse(['message' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setServiceName($data['serviceName']);
        $product->setDescription($data['description'] ?? '');
        $product->setStartDate(isset($data['startDate']) ? new \DateTime($data['startDate']) : null);
        $product->setEndDate(isset($data['endDate']) ? new \DateTime($data['endDate']) : null);
        $product->setPrice($data['price'] ?? null);
        $product->setMaxParticipants($data['maxParticipants'] ?? null);
        $product->setAvailable($data['available'] ?? false);
        $product->setPromotion($data['promotion'] ?? null);

        $owner = $this->partnerRepository->find($data['ownerId']);
        if (!$owner) {
            return new JsonResponse(['message' => 'Owner not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $product->setOwner($owner);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'serviceName' => $product->getServiceName(),
            'description' => $product->getDescription(),
            'startDate' => $product->getStartDate()?->format('Y-m-d'),
            'endDate' => $product->getEndDate()?->format('Y-m-d'),
            'price' => $product->getPrice(),
            'maxParticipants' => $product->getMaxParticipants(),
            'available' => $product->isAvailable(),
            'promotion' => $product->isPromotion(),
            'ownerId' => $product->getOwner()->getId(),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $product->setServiceName($data['serviceName'] ?? $product->getServiceName());
        $product->setDescription($data['description'] ?? $product->getDescription());
        $product->setStartDate(isset($data['startDate']) ? new \DateTime($data['startDate']) : $product->getStartDate());
        $product->setEndDate(isset($data['endDate']) ? new \DateTime($data['endDate']) : $product->getEndDate());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setMaxParticipants($data['maxParticipants'] ?? $product->getMaxParticipants());
        $product->setAvailable($data['available'] ?? $product->isAvailable());
        $product->setPromotion($data['promotion'] ?? $product->isPromotion());

        $owner = $this->partnerRepository->find($data['ownerId'] ?? $product->getOwner()->getId());
        if (!$owner) {
            return new JsonResponse(['message' => 'Owner not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $product->setOwner($owner);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'serviceName' => $product->getServiceName(),
            'description' => $product->getDescription(),
            'startDate' => $product->getStartDate()?->format('Y-m-d'),
            'endDate' => $product->getEndDate()?->format('Y-m-d'),
            'price' => $product->getPrice(),
            'maxParticipants' => $product->getMaxParticipants(),
            'available' => $product->isAvailable(),
            'promotion' => $product->isPromotion(),
            'ownerId' => $product->getOwner()->getId(),
        ]);
    }

    #[Route('/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Product deleted successfully']);
    }

}
