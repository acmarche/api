<?php

namespace AcMarche\Api\Mailer;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ApiMailer
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function sendError(string $message)
    {
        $email = (new Email())
            ->from('webmaster@marche.be')
            ->to('webmaster@marche.be')
            ->subject('Error api')
            ->text($message);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e);
        }
    }

}