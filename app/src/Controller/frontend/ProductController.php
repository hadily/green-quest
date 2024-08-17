<?php

namespace App\Controller\frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Repository\ProductRepository;

#[Route('/product', name: 'frontend_product')]
class ProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('', name: '_list')]
    public function index()
    {
        $products = $this->productRepository->getAll();
        return $this->render('frontend/product/list.html.twig', ['list'=>$products]);
    }

    #[Route('/{id}/detail', name: '_detail')]
    public function detail(int $id, Product $product)
    {
        $product = $this->productRepository->getDetailsById($id);
        if ($product) {
            $product['startDate'] = $product['startDate'] ? $product['startDate']->format('Y-m-d') : null;
            $product['endDate'] = $product['endDate'] ? $product['endDate']->format('Y-m-d') : null;
        }
        return $this->render('frontend/product/detail.html.twig', ['product'=>$product]);
    }
}