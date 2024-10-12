<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;


#[Route('/api')]
class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function sendEmail(MailerInterface $mailer, Request $request, LoggerInterface $logger)
    {
        $logger->info('Received email sending request.', [
            'request_body' => $request->getContent(),
        ]);
    
        $data = json_decode($request->getContent(), true);
        
        if ($data === null || !isset($data['email'], $data['password'])) {
            return new Response('Invalid JSON data', Response::HTTP_BAD_REQUEST);
        }
    
        $recipientEmail = $data['email']; 
        $password = $data['password']; 
    
        if (empty($recipientEmail)) {
            return new Response('Email address is required.', Response::HTTP_BAD_REQUEST);
        } 
    
        $email = (new Email())
            ->from('no-reply@demomailtrap.com')
            ->to($recipientEmail) 
            ->subject('Welcome to GreenQuest')
            ->html('<p>Welcome to GreenQuest! Here are your login details:</p>
                     <p>Email: ' . htmlspecialchars($recipientEmail) . '</p>
                     <p>Password: ' . htmlspecialchars($password) . '</p>');
    
        $mailer->send($email);
    
        return new Response('Email was sent to ' . htmlspecialchars($recipientEmail));
    }
}