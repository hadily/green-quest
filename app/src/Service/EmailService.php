<?php 

// src/Service/EmailService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeEmail($to, $plainPassword)
    {
        $email = (new Email())
            ->from('your-email@example.com')
            ->to($to)
            ->subject('Your Account Credentials')
            ->text("
                Welcome to our platform!

                Here are your login details:
                Email: $to
                Password: $plainPassword

                Please make sure to change your password after your first login.
            ");

        $this->mailer->send($email);
    }
}
