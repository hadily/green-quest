<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PartnerRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UploadFileService;

#[Route('/api/product')]
class ProductController extends AbstractController
{
    private $productRepository;
    private $partnerRepository;
    private $entityManager;
    private $ufService;

    public function __construct(
        ProductRepository $productRepository,
        PartnerRepository $partnerRepository,
        EntityManagerInterface $entityManager,
        UploadFileService $ufService
    ) {
        $this->productRepository = $productRepository;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->ufService = $ufService;
    }
    

    #[Route('/', name: 'list_products', methods: ['GET'])]
    public function listProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'owner' => $product->getOwner()->getId(),
                'imageFilename' => $product->getImageFilename()

            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'getProductById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getProductById(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'owner' => $product->getOwner()->getId(),
            'imageFilename' => $product->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    

    #[Route('/', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        // Create a new Product entity
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'allow_extra_fields' => true,  // Allow extra fields
        ]);
        // Bind request data to the form
        $form->handleRequest($request);
        // Handle file upload
        $file = $request->files->get('imageFilename');
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $product->setImageFilename($fileName);
        }

        $form->submit($request->request->all());
    
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Event created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/product/{id}', name: 'update_product', methods: ['POST'])]
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $product = $form->getData();

        $file = $form->get('imageFilename')->getData();
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $product->setImageFilename($fileName);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Product updated successfully']);
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
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'owner' => $product->getOwner()->getId(),
                'imageFilename' => $product->getImageFilename()
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

}
