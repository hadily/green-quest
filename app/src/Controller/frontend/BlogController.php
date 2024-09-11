<?php

namespace App\Controller\frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Article;
use App\Repository\ArticleRepository;

#[Route('/blog', name: 'frontend_blog')]
class BlogController extends AbstractController
{
    private $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    ) { 
        $this->articleRepository = $articleRepository;
    }

    #[Route('', name: '_list')]
    public function index()
    {
        $articles = $this->articleRepository->getApprovedArticles();
        return $this->render('frontend/blog/list.html.twig', ['list'=>$articles]);
    }

    #[Route('/{id}/detail', name: '_detail')]
    public function detail(int $id, Article $article)
    {
        $article = $this->articleRepository->getDetailsById($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        } 
        
        return $this->render('frontend/blog/detail.html.twig', ['blog'=>$article]);
    }
}