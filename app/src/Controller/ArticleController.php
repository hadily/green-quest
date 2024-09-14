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
            $data[] = [
                'id' => $article->getId(),
                'writer' => $article->getWriterFullName(),
                'writerId' =>$article->getWriterId(),
                'title' => $article->getTitle(),
                'subTitle' => $article->getSubTitle(),
                'summary' => $article->getSummary(),
                'text' => $article->getText(),
                'date' => $article->getDate()->format('Y-m-d'),
                'status' => $article->getStatus(),
                'review' => $article->getReview(),
                'imageFilename' => $article->getImageFilename()
            ];
        }

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
            'status' => $article->getStatus(),
            'review' => $article->getReview(),
            'imageFilename' => $article->getImageFilename()
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/', name: 'createArticle', methods: ['POST'])]
    public function createArticle(Request $request, EntityManagerInterface $em, UploadFileService $ufService): JsonResponse
    {
        $article = new Article();
        $article->setDate(new \DateTime());
        
        // Handle file upload
        $file = $request->files->get('imageFilename');
        if ($file) {
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $article->setImageFilename($fileName);
        }
        
        // Handle form submission
        $form = $this->createForm(ArticleType::class, $article, [
            'allow_extra_fields' => true,  // Allow extra fields
            'csrf_protection' => false
        ]);
        $form->submit($request->request->all());
        
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        // Persist and flush
        $em->persist($article);
        $em->flush();
        
        return new JsonResponse(['message' => 'Article created'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_article_update', methods: ['PUT'])]
    public function updateArticle(int $id, Request $request, ArticleRepository $articleRepository, EntityManagerInterface $em, UploadFileService $ufService) 
    {
        $article = $articleRepository->getDetailsById($id);

        if (!$article) {
            return new JsonResponse(['message' => 'Article not found']);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        $article->setDate(new \DateTime());

        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }

        if (isset($data['subTitle'])) {
            $article->setSubTitle($data['subTitle']);
        }

        if (isset($data['summary'])) {
            $article->setSummary($data['summary']);
        }

        if (isset($data['text'])) {
            $article->setText($data['text']);
        }

        if (isset($data['status'])) {
            $article->setStatus($data['status']);
        }

        if (isset($data['review'])) {
            $article->setReview($data['review']);
        }

        $imageFile = $request->files->get('imageFilename');
        if ($imageFile) {
            $imageName = $ufService->uploadFile($imageFile);
            $article->setImageFilename($imageName);
        }

        $em->persist($article);
        $em->flush();

        return new JsonResponse(['message' => 'Article updated successfully'], 200);
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
                'status' => $article->getStatus(),
                'review' => $article->getReview(),
                'imageFilename' => $article->getImageFilename()
            ];
        }

    return new JsonResponse($data);
    }

    #[Route('/writer/{id}', name: 'getArticlesByWriter', methods:['GET'])]
    public function getArticlesByWriter(int $id, ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAllByOwner($id);
        if (empty($articles)) {
            return new JsonResponse(['message' => 'No articles found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'writer' => $article->getWriterFullName(),
                'title' => $article->getTitle(),
                'subTitle' => $article->getSubTitle(),
                'summary' => $article->getSummary(),
                'text' => $article->getText(),
                'date' => $article->getDate()->format('Y-m-d'),
                'status' => $article->getStatus(),
                'review' => $article->getReview(),
                'imageFilename' => $article->getImageFilename()
            ];
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

}
