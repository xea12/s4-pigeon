<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;


class MailerController extends AbstractController
{

    #[Route('/send-email9', name: 'send_email9')]
    public function sendEmailMailGun(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('postmaster@sandboxef818e4d684248d5adc4681000dca609.mailgun.org')
            ->to('xeaxea90@gmail.com')
            ->subject('Testowy email')
            ->text('To jest testowa wiadomość wysłana przez Symfony Mailer z użyciem Mailgun.');

        $mailer->send($email);

        return new Response('Email został wysłany!');
    }
    #[Route('/send-email2', name: 'send_notification')]
    public function sendNotification(NotifierInterface $notifier)
    {
        $notification = (new Notification('Temat'))
            ->content('Treść wiadomości');

        $recipient = new Recipient('lukasz@drtusz.pl');

        $notifier->send($notification, $recipient);
    }
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('lukasz@drtusz.pl')
            ->to('lukasz@drtusz.pl')
            ->subject('Test email')
            ->text('This is a test email from Symfony.');

        $mailer->send($email);

        return new Response('E-mail został wysłany!');
    }
    #[Route('/send-email4', name: 'send_email4')]
    public function testTransport(MailerInterface $mailer) {
        $transport = Transport::fromDsn('smtp://lukasz@drtusz.pl:Wiesio09@smtp.emaillabs.net.pl:587');
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('test@example.com')
            ->to('lukasz@drtusz.pl')
            ->subject('Test email z Symfony')
            ->text('To jest testowa wiadomość wysłana przez Symfony Mailer.');

        $mailer->send($email);

        echo "Email został wysłany!";
    }
}








//KB46DJ1V96N8KPZ3CD4ER3ZZ
