<?php

namespace App\Controller;

use App\Entity\CampaignOpen;
use App\Entity\CampaignSent;
use App\Repository\CampaignOpenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class TrackingController extends AbstractController
{
    private $logger;
    private $campaignOpenRepository;

    public function __construct(
        LoggerInterface $templateProcessorLogger,
        CampaignOpenRepository $campaignOpenRepository
    ) {
        $this->logger = $templateProcessorLogger;
        $this->campaignOpenRepository = $campaignOpenRepository;
    }

    #[Route('/track/email/{id}', name: 'track_email_open', methods: ['GET'])]
    public function trackEmailOpen(
        string $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $this->logger->info('Próba śledzenia otwarcia emaila', [
                'tracking_id' => $id,
                'ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('User-Agent')
            ]);

            $campaignSent = $entityManager->getRepository(CampaignSent::class)
                ->findOneBy(['trackingId' => $id]);

            if (!$campaignSent) {
                $this->logger->warning('Nie znaleziono wysłanej kampanii', [
                    'tracking_id' => $id
                ]);
                return $this->createTransparentPixelResponse();
            }

            $this->logger->info('Znaleziono kampanię', [
                'campaign_id' => $campaignSent->getCampaign()->getId(),
                'customer_email' => $campaignSent->getCustomer()->getEmail()
            ]);

            // Sprawdź czy już nie zarejestrowano otwarcia w ciągu ostatnich 5 minut
            $existingOpen = $entityManager->getRepository(CampaignOpen::class)
                ->findOneBy(
                    ['campaignSent' => $campaignSent],
                    ['openedAt' => 'DESC']
                );

            if ($existingOpen && $existingOpen->getOpenedAt() > new \DateTime('-5 minutes')) {
                $this->logger->info('Pomijam duplikat otwarcia', [
                    'tracking_id' => $id,
                    'last_open' => $existingOpen->getOpenedAt()->format('Y-m-d H:i:s')
                ]);
                return $this->createTransparentPixelResponse();
            }

            $campaignOpen = new CampaignOpen();
            $campaignOpen->setCampaignSent($campaignSent);
            // Ustaw campaign i customer z campaignSent
            $campaignOpen->setCampaign($campaignSent->getCampaign());
            $campaignOpen->setCustomer($campaignSent->getCustomer());
            $campaignOpen->setOpenedAt(new \DateTime());
            $campaignOpen->setIpAddress($request->getClientIp());
            $campaignOpen->setUserAgent($request->headers->get('User-Agent'));

            $entityManager->persist($campaignOpen);

            // Dodaj flush w bloku try-catch
            try {
                $entityManager->flush();
                $this->logger->info('Zapisano otwarcie emaila', [
                    'tracking_id' => $id,
                    'campaign_id' => $campaignSent->getCampaign()->getId(),
                    'customer_id' => $campaignSent->getCustomer()->getId(),
                    'opened_at' => $campaignOpen->getOpenedAt()->format('Y-m-d H:i:s')
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Błąd zapisu do bazy', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return $this->createTransparentPixelResponse();

        } catch (\Exception $e) {
            $this->logger->error('Błąd podczas śledzenia otwarcia emaila', [
                'tracking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->createTransparentPixelResponse();
        }
    }

    private function createTransparentPixelResponse(): Response
    {
        $response = new Response(
            base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==')
        );

        $response->headers->set('Content-Type', 'image/gif');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}