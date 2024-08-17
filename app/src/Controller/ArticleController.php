<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UploadFileService;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/api/article')]
class ArticleController extends AbstractController
{
    //#[Route('/', name: 'app_article_index', methods: ['GET'])]
    //public function index(ArticleRepository $articleRepository): Response
    //{
    //    return $this->render('article/index.html.twig', [
    //        'articles' => $articleRepository->findAll(),
    //    ]);
    //}

    #[Route('/', name: 'getAllArticles', methods: ['GET'])]
    public function getAllArticles(EntityManagerInterface $em, ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAll();
        $data = [];

        foreach($articles as $article) {
            $writer = $article->getWriter(); // Get the writer (User object)
            $writerName = $writer ? $writer->getFullName() : null; // Extract the ID from the User object


            $data[] = [
                'id' => $article->getId(),
                'writer' => $writerName,
                'title' => $article->getTitle(),
                'subTitle' => $article->getSubTitle(),
                'summary' => $article->getSummary(),
                'text' => $article->getText(),
                'date' => $article->getDate()->format('Y-m-d'),
                'likes' => $article->getLikes(),
                'imageFilename' => $article->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getArticleByID', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getPartnerByID(int $id, ArticleRepository $articleRepository): JsonResponse
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            return new JsonResponse(['message' => 'article not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'title' => $article->getTitle(),
            'subTitle' => $article->getSubTitle(),
            'summary' => $article->getSummary(),
            'text' => $article->getText(),
            'date' => $article->getDate(),
            'imageFilename' => $article->getImageFilename()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getArticleById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getArticleById(int $id, ArticleRepository $articleRepository): JsonResponse
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'title' => $article->getTitle(),
            'subTitle' => $article->getSubTitle(),
            'summary' => $article->getSummary(),
            'text' => $article->getText(),
            'date' => $article->getDate(),
            'imageFilename' => $article->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'createArticle', methods: ['POST'])]
    public function createArticle(Request $request, EntityManagerInterface $em, UploadFileService $ufService): JsonResponse
    {
        $data = $request->request->all();

        $article = new Article();

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $ufService->uploadFile($imageFilename);
            $article->setImageFilename($imageName);
        }

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setSubTitle($data['subTitle']);
        $article->setSummary($data['summary']);
        $article->setText($data['text']);
        $article->setDate(new \DateTime());
        $article->setWriter($data['writerId']);
        

        $em->persist($article);
        $em->flush();

        return new JsonResponse(['message' => 'Article created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}', name: 'app_article_update', methods: ['POST'])]
    public function updateArticle(int $id, Request $request, EntityManagerInterface $em, ArticleRepository $articleRepository, UploadFileService $ufService): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            return new JsonResponse(['message' => 'article not found'], Response::HTTP_NOT_FOUND);
        }

        $imageFilename = $request->files->get('imageFilename');

        if ($imageFilename) {
            $imageName = $ufService->uploadFile($imageFilename);
            $article->setImageFilename($imageName);
        } else {
            return new JsonResponse(['error' => 'Image is required'], Response::HTTP_BAD_REQUEST);
        }

        // Extract data from the request
        $title = $request->request->get('title');
        $subTitle = $request->request->get('subTitle');
        $text = $request->request->get('text');
        $date = $request->request->get('date');


        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }
        if (isset($data['subTitle'])) {
            $article->setSubTitle($data['subTitle']);
        }
        if (isset($data['text'])) {
            $article->setText($data['text']);
        }
        if (isset($data['date'])) {
            $article->setDate(new \DateTime());
        }

        $em->persist($article);
        $em->flush();

        return new JsonResponse(['message' => 'article updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            return new JsonResponse(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($article);
        $em->flush();

        return new JsonResponse(['message' => 'Article deleted'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'searchArticles', methods: ['GET'])]
    public function searchArticles(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        $query = $request->query->get('query', '');

        // Perform the search based on the query
        $articles = $articleRepository->searchArticles($query);

        // Convert entities to array
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'subTitle' => $article->getSubTitle(),
                'writer' => $article->getWriter(),
                'text' => $article->getText(),
                'date' => $article->getDate() ? $article->getDate()->format('Y-m-d') :new \DateTime(),
                'imageFilename' => $article->getImageFilename()
            ];
        }

    return new JsonResponse($data);
    }

    // Symfony Route
    #[Route('/writer/{id}', name: 'getArticlesByWriter', methods:['GET'])]
    public function getArticlesByWriter(int $ownerId, ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAllByOwner($ownerId);
        $data = [];

        foreach($articles as $article) {
            $writer = $article->getWriter(); // Get the writer (User object)
            $writerName = $writer ? $writer->getFullName() : null; // Extract the ID from the User object


            $data[] = [
                'id' => $article->getId(),
                'writer' => $writerName,
                'title' => $article->getTitle(),
                'subTitle' => $article->getSubTitle(),
                'summary' => $article->getSummary(),
                'text' => $article->getText(),
                'date' => $article->getDate()->format('Y-m-d'),
                'likes' => $article->getLikes(),
                'imageFilename' => $article->getImageFilename()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

}
