<?php
// src/Service/EmailService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function sendEmail(
        string $to,
        string $subject,
        string $htmlContent,
        ?string $textContent = null,
        array $attachments = []
    ): void {
        $email = (new Email())
            ->from(new Address('noreply@twoja-domena.pl', 'Nazwa Nadawcy'))
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        if ($textContent) {
            $email->text($textContent);
        }

        foreach ($attachments as $attachment) {
            $email->attachFromPath($attachment);
        }

        $this->mailer->send($email);
    }
}
