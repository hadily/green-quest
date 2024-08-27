<?php

// src/Controller/MailTestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailTestController extends AbstractController
{
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('greenquest.sqit@gmail.com')
            ->to('hadilyahiaoui02@gmail.com')
            ->subject('Test Email')
            ->text('This is a test email using sendmail.');

        try {
            $mailer->send($email);
            return new Response('Email sent successfully.');
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage());
        }
    }
}
