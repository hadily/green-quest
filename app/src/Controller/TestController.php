<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends AbstractController
{
    #[Route('/hello')]
    public function hello() : Response
    {
        return new JsonResponse('hello');
    }
}
