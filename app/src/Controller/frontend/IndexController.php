<?php

namespace App\Controller\frontend;

use App\Entity\Event;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/home', name: 'frontend_')]
class IndexController extends AbstractController 
{

    private $eventRepository;
    private $productRepository;
    private $articleRepository;

    public function __construct(
        EventRepository $eventRepository,
        ProductRepository $productRepository,
        ArticleRepository $articleRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->productRepository = $productRepository;
        $this->articleRepository = $articleRepository;
    }

    #[Route('', name: 'index')]
    public function index(): Response {

        return $this->render('frontend/index.html.twig');
    }

    public function banner(): Response {

        return $this->render('frontend/widget/banner.html.twig');
    }

    public function event(): Response {

        $events = $this->eventRepository->findAll();

        return $this->render('frontend/widget/event.html.twig', [ 'list' => $events ]);
    }

    public function product(): Response {

        $products = $this->productRepository->findAll();

        return $this->render('frontend/widget/product.html.twig', [ 'list' => $products ]);
    }

    public function blog(): Response {

        $articles = $this->articleRepository->findAll();

        return $this->render('frontend/widget/blog.html.twig', [ 'list' => $articles ]);
    }

    

}