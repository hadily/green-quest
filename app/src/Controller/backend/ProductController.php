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
use App\Service\UploadFileService;
use Symfony\Component\HttpFoundation\File\UploadedFile; 

#[Route('/api/product')]
class ProductController extends AbstractController
{
    private $productRepository;
    private $partnerRepository;
    private $entityManager;
    private $validator;
    private $ufService;

    public function __construct(
        ProductRepository $productRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UploadFileService $ufService
    ) {
        $this->productRepository = $productRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->ufService = $ufService;
    }

    #[Route('/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'serviceName' => $product->getServiceName(),
            'description' => $product->getDescription(),
            'startDate' => $product->getStartDate()?->format('Y-m-d'),
            'endDate' => $product->getEndDate()?->format('Y-m-d'),
            'price' => $product->getPrice(),
            'ownerId' => $product->getOwner()->getId(),
            'available' => $product->isAvailable(),
            'imageFilename' => $product->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'getProductById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getProductById(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'serviceName' => $product->getServiceName(),
            'description' => $product->getDescription(),
            'startDate' => $product->getStartDate()?->format('Y-m-d'),
            'endDate' => $product->getEndDate()?->format('Y-m-d'),
            'price' => $product->getPrice(),
            'ownerId' => $product->getOwner()->getId(),
            'available' => $product->isAvailable(),
            'imageFilename' => $product->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'list_products', methods: ['GET'])]
    public function listProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'serviceName' => $product->getServiceName(),
                'description' => $product->getDescription(),
                'startDate' => $product->getStartDate()?->format('Y-m-d'),
                'endDate' => $product->getEndDate()?->format('Y-m-d'),
                'price' => $product->getPrice(),
                'ownerId' => $product->getOwner()->getId(),
                'available' => $product->isAvailable(),
                'imageFilename' => $product->getImageFilename()

            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = $request->request->all();

        $product = new Product();

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $this->ufService->uploadFile($imageFilename);
            $product->setImageFilename($imageName);
        }

        $product->setServiceName($data['serviceName']);
        $product->setDescription($data['description'] ?? '');
        $product->setStartDate(isset($data['startDate']) ? new \DateTime($data['startDate']) : null);
        $product->setEndDate(isset($data['endDate']) ? new \DateTime($data['endDate']) : null);
        $product->setPrice($data['price'] ?? null);
        $product->setAvailable($data['available'] ?? false);

        $owner = $this->partnerRepository->find($data['ownerId']);
        if (!$owner) {
            return new JsonResponse(['message' => 'Owner not found'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $product->setOwner($owner);
    

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Event created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_product', methods: ['POST'])]
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Event not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $this->ufService->uploadFile($imageFilename);
            $product->setImageFilename($imageName);
        } else {
            return new JsonResponse(['error' => 'Image is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Extract data from the request
        $serviceName = $request->request->get('serviceName');
        $description = $request->request->get('description');
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $price = $request->request->get('price');
        $available = $request->request->get('available');

        if ($serviceName !== null) {
          $product->setServiceName($serviceName);
        }
        if ($description !== null) {
          $product->setDescription($description);
        }
        if ($startDate !== null) {
          $product->setStartDate($startDate);
        }
        if ($endDate !== null) {
          $product->setEndDate($endDate);
        }
        if ($price !== null) {
          $product->setPrice($price);
        }
        if ($available !== null) {
          $product->setAvailable($available);
        }

        $this->entityManager->flush();

        return new JsonResponse(['event created']);
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

    #[Route('/owner/{id}', name:'getProductByOwner', methods:['GET'])]
    public function getProductsByOwner(int $id): JsonResponse
    {
        $partner = $this->partnerRepository->find($id);

        if (!$partner) {
            return new JsonResponse(['message' => 'Partner not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $products = $this->productRepository->findBy(['owner' => $partner]);

        if (!$products) {
            return new JsonResponse(['message' => 'No products found for this partner'], JsonResponse::HTTP_NOT_FOUND);
        }

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'serviceName' => $product->getServiceName(),
                'description' => $product->getDescription(),
                'startDate' => $product->getStartDate()?->format('Y-m-d'),
                'endDate' => $product->getEndDate()?->format('Y-m-d'),
                'price' => $product->getPrice(),
                'ownerId' => $product->getOwner()->getId(),
                'available' => $product->isAvailable(),
                'imageFilename' => $product->getImageFilename()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

}
