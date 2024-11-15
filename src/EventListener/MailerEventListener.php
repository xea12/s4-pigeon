<?php

namespace App\EventListener;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class MailerEventListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        $this->logger->info('Próba wysłania wiadomości', [
            'subject' => $message->getSubject(),
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
        ]);
    }
}