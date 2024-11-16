<?php
// src/Controller/CampaignController.php
namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignOpenRepository;
use App\Repository\CustomerRepository;
use App\Service\TemplateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CampaignRepository;
use App\Form\CampaignHtmlTemplateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CampaignSent;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;


class CampaignController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $templateProcessorLogger)
    {
        $this->logger = $templateProcessorLogger;
    }

    #[Route('/campaign/{id}/send', name: 'campaign_send')]
    public function sendCampaign(
        int                    $id,
        EntityManagerInterface $entityManager,
        MailerInterface        $mailer,
        CustomerRepository     $customerRepository,
        TemplateProcessor      $templateProcessor,
        UrlGeneratorInterface  $urlGenerator
    ): Response
    {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);
        if (!$campaign) {
            throw $this->createNotFoundException('Campaign not found');
        }

        $customers = $customerRepository->findBySegment($campaign->getSegment());

        $batchSize = 50; // Limit wysyłki w jednej partii
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        // Podziel klientów na mniejsze partie
        $customerBatches = array_chunk($customers, $batchSize);


        foreach ($customers as $customer) {

            $trackingId = Uuid::v4()->toRfc4122();
            try {

                // Przetwórz szablon HTML dla każdego klienta
                $processedTemplate = $templateProcessor->processTemplate(
                    $campaign->getHtmlTemplate(),
                    $customerRepository->getCustomerDataForTemplate($customer)
                );
                $processedSubject = $templateProcessor->processTemplate(
                    $campaign->getTemat(),
                    $customerRepository->getCustomerDataForTemplate($customer)
                );

                //$trackingPixel = '<img src="' . $urlGenerator->generate('track_email_open', ['id' => $trackingId], UrlGeneratorInterface::ABSOLUTE_URL) . '" alt="" width="1" height="1" />';

                // Generowanie pixela śledzącego
                $trackingPixel = sprintf(
                    '<img src="%s" alt="" width="1" height="1" style="display:block !important; width:1px !important; height:1px !important;" />',
                    $urlGenerator->generate('track_email_open',
                        ['id' => $trackingId],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                );

                $trackingUrl = $urlGenerator->generate('track_email_open',
                    ['id' => $trackingId],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $this->logger->info('Wygenerowano URL śledzenia', [
                    'url' => $trackingUrl,
                    'tracking_id' => $trackingId,
                    'customer_email' => $customer->getEmail()
                ]);

//                $processedSender = $templateProcessor->processTemplate(
//                    $campaign->getNazwaNadawcy(),
//                    $customerRepository->getCustomerDataForTemplate($customer)
//                );

                $finalHtml = $processedTemplate . $trackingPixel;

                // Utwórz i wyślij email
                $email = (new Email())
                    ->from(new Address('noreply@trial-jpzkmgqr0k24059v.mlsender.net', 'Newsletter'))
                    //->from($campaign->getEmailNadawcy())
                    ->to($customer->getEmail())
                    ->subject($processedSubject)
                    ->html($finalHtml);
                // Dodaj opóźnienie między wysyłkami
                if ($successCount > 0) {
                    sleep(2);
                }
                sleep(2);

                $mailer->send($email);

                // Zapisz informację o wysłanej kampanii
                $campaignSent = new CampaignSent();
                $campaignSent->setCampaign($campaign);
                $campaignSent->setCustomer($customer);
                $campaignSent->setTrackingId($trackingId);
                $campaignSent->setSentAt(new \DateTime());
                $campaignSent->setStatus('Sent');


                $entityManager->persist($campaignSent);
                $entityManager->flush();

                $successCount++;

                $this->logger->info('Tracking pixel sent', [
                    'trackingId' => $trackingId,
                    'campaignId' => $campaign->getId(),
                    'customerId' => $customer->getId(),
                    'sentAt' => $campaignSent->getSentAt()->format('Y-m-d H:i:s')
                ]);

            } catch (\Exception $e) {
                $errorCount++;
                $errorMessages[] = sprintf(
                    'Błąd wysyłki do %s: %s',
                    $customer->getEmail(),
                    $e->getMessage()
                );

                $this->logger->error('Błąd wysyłki emaila', [
                    'error' => $e->getMessage(),
                    'to' => $customer->getEmail()
                ]);
                // Obsługa błędu wysyłki
                $this->addFlash('error', 'Błąd wysyłki do ' . $customer->getEmail() . ': ' . $e->getMessage());
            }
        }

        // Po zakończeniu wysyłki, zaktualizuj status kampanii
        if ($errorCount === 0 && $successCount > 0) {
            $campaign->setStatus('Wysłana');
        } elseif ($successCount === 0) {
            $campaign->setStatus('Błąd wysyłki');
        } else {
            $campaign->setStatus('Wysłana częściowo');
        }

        // Dodaj informację o liczbie wysłanych emaili
        $campaign->setDataWysylki(new \DateTime());

        try {
            $entityManager->flush();

            // Dodaj odpowiednie komunikaty flash
            if ($successCount > 0) {
                $this->addFlash('success', sprintf('Wysłano pomyślnie %d emaili.', $successCount));
            }
            if ($errorCount > 0) {
                foreach ($errorMessages as $message) {
                    $this->addFlash('error', $message);
                }
            }

            $this->addFlash('success', sprintf(
                'Kampania została zakończona. Status: %s. Wysłano: %d, Błędów: %d',
                $campaign->getStatus(),
                $successCount,
                $errorCount
            ));

        } catch (\Exception $e) {
            $this->logger->error('Błąd podczas aktualizacji statusu kampanii', [
                'campaign_id' => $campaign->getId(),
                'error' => $e->getMessage()
            ]);
            $this->addFlash('error', 'Wystąpił błąd podczas aktualizacji statusu kampanii.');
        }

        return $this->redirectToRoute('campaign_list');
    }


    #[Route('/campaign/{id}/summary', name: 'campaign_summary')]
    public function summary(int $id, Request $request, EntityManagerInterface $entityManager, CustomerRepository $customerRepository): Response
    {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);
        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }

        $form = $this->createFormBuilder($campaign)
            ->add('data_wysylki', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Data wysyłki'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // Tutaj możesz dodać logikę wysyłania kampanii
            $this->addFlash('success', 'Kampania została zaktualizowana i zaplanowana do wysyłki.');
            return $this->redirectToRoute('campaign_list');
        }

        // Pobierz klientów pasujących do segmentu kampanii
        $customers = $customerRepository->findBySegment($campaign->getSegment());

        return $this->render('campaign/summary.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
            'customers' => $customers,
        ]);
    }

    #[Route('/get-customer-count', name: 'get_customer_count')]
    public function getCustomerCount(Request $request, CustomerRepository $customerRepository): JsonResponse
    {
        $segment = $request->query->get('segment');
        $count = $customerRepository->countCustomersBySegment($segment);

        return new JsonResponse(['count' => $count]);
    }

    #[Route('/campaign/new', name: 'campaign_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, CustomerRepository $customerRepository): Response
    {
        $campaign = new Campaign();
        $campaign->setDataUtworzenia(new \DateTime());
        $campaign->setStatus('Szkic');

// W metodzie new() w CampaignController.php
        $segmentProductTypeChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_product_type'));
        $segmentOtwieraneChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_otwierane'));
        $segmentFirmaChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_firma'));
        $segmentCzasChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_czas'));


        $form = $this->createForm(CampaignType::class, $campaign, [
            'segment_product_type_choices' => $segmentProductTypeChoices,
            'segment_otwierane_choices' => $segmentOtwieraneChoices,
            'segment_firma_choices' => $segmentFirmaChoices,
            'segment_czas_choices' => $segmentCzasChoices,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'campaign',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pobierz dane z formularza
            $segmentProductType = $form->get('segment_product_type')->getData();
            $segmentOtwierane = $form->get('segment_otwierane')->getData();
            $segmentFirma = $form->get('segment_firma')->getData();
            $segmentCzas = $form->get('segment_czas')->getData();

            // Połącz segmenty w jeden string
            $combinedSegment = implode('-', [$segmentProductType, $segmentOtwierane, $segmentFirma, $segmentCzas]);

            // Ustaw połączony segment dla kampanii
            $campaign->setSegment($combinedSegment);

            $entityManager->persist($campaign);
            $entityManager->flush();

            $this->addFlash('success', 'Kampania została utworzona pomyślnie.');
            return $this->redirectToRoute('campaign_upload_template', ['id' => $campaign->getId()]);
        }

        return $this->render('campaign/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function createChoicesArray(array $values): array
    {
        $choices = [];
        foreach ($values as $value) {
            $choices[$value] = $value;
        }
        return $choices;
    }


    #[Route('/campaign', name: 'campaign_list')]
    public function list(CampaignRepository $campaignRepository): Response
    {
        $campaigns = $campaignRepository->findAll();
        return $this->render('campaign/list.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }


    #[Route('/campaign/edit/{id}', name: 'campaign_edit')]
    public function edit(Request $request, int $id, CampaignRepository $campaignRepository, EntityManagerInterface $entityManager, CustomerRepository $customerRepository): Response
    {
        $campaign = $campaignRepository->find($id);

        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }

        // Pobierz aktualne segmenty kampanii
        $currentSegments = explode('-', $campaign->getSegment());

        // Pobierz dostępne opcje segmentów
        $segmentProductTypeChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_product_type'));
        $segmentOtwieraneChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_otwierane'));
        $segmentFirmaChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_firma'));
        $segmentCzasChoices = $this->createChoicesArray($customerRepository->getUniqueSegmentValues('segment_czas'));

        $form = $this->createForm(CampaignType::class, $campaign, [
            'segment_product_type_choices' => $segmentProductTypeChoices,
            'segment_otwierane_choices' => $segmentOtwieraneChoices,
            'segment_firma_choices' => $segmentFirmaChoices,
            'segment_czas_choices' => $segmentCzasChoices,
        ]);

        // Ustaw domyślne wartości dla pól segmentów
        $form->get('segment_product_type')->setData($currentSegments[0] ?? null);
        $form->get('segment_otwierane')->setData($currentSegments[1] ?? null);
        $form->get('segment_firma')->setData($currentSegments[2] ?? null);
        $form->get('segment_czas')->setData($currentSegments[3] ?? null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pobierz dane z formularza
            $segmentProductType = $form->get('segment_product_type')->getData();
            $segmentOtwierane = $form->get('segment_otwierane')->getData();
            $segmentFirma = $form->get('segment_firma')->getData();
            $segmentCzas = $form->get('segment_czas')->getData();

            // Połącz segmenty w jeden string
            $combinedSegment = implode('-', [$segmentProductType, $segmentOtwierane, $segmentFirma, $segmentCzas]);

            // Ustaw połączony segment dla kampanii
            $campaign->setSegment($combinedSegment);

            $entityManager->flush();

            $this->addFlash('success', 'Kampania została zaktualizowana.');
            return $this->redirectToRoute('campaign_edit', ['id' => $campaign->getId()]);
        }

        // Oblicz liczbę klientów w aktualnym segmencie
        $customerCount = $customerRepository->countCustomersBySegment($campaign->getSegment());

        return $this->render('campaign/edit.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
            'customerCount' => $customerCount,
        ]);
    }

    #[Route('/campaign/delete/{id}', name: 'campaign_delete')]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);

        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }
        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();

            $this->addFlash('success', 'Kampania została usunięta.');
        }

        return $this->redirectToRoute('campaign_list');
    }

    #[Route('/campaign/{id}/upload-template', name: 'campaign_upload_template')]
    public function uploadTemplate(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);
        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }

        $form = $this->createForm(CampaignHtmlTemplateType::class, ['htmlContent' => $campaign->getHtmlTemplate()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $htmlContent = $form->get('htmlContent')->getData();
            $campaign->setHtmlTemplate($htmlContent);

            $entityManager->flush();

            $this->addFlash('success', 'Szablon HTML został pomyślnie zaktualizowany.');
            return $this->render('campaign/upload_template.html.twig', [
                'form' => $form->createView(),
                'campaign' => $campaign,
            ]);
        }

        return $this->render('campaign/upload_template.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
        ]);
    }

    #[Route('/campaign/{id}/preview/{customerId}', name: 'campaign_preview')]
    public function previewCampaign(
        Campaign           $campaign,
        int                $customerId,
        CustomerRepository $customerRepository,
        TemplateProcessor  $templateProcessor
    ): Response
    {
        $customerData = $customerRepository->findCustomerDataForCampaign($customerId);
        $processedTemplate = $templateProcessor->processTemplate($campaign->getHtmlTemplate(), $customerData);

        return new Response($processedTemplate);
    }

    #[Route('/campaign/{id}/stats', name: 'campaign_stats')]
    public function campaignStats(
        int $id,
        EntityManagerInterface $entityManager,
        CampaignRepository $campaignRepository
    ): Response {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);

        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }

        $openRate = $campaignRepository->getOpenRateForCampaign($campaign);
        $opens = $entityManager->createQueryBuilder()
            ->select('co')
            ->from('App\Entity\CampaignOpen', 'co')
            ->join('co.campaignSent', 'cs')
            ->where('cs.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->orderBy('co.openedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('campaign/stats.html.twig', [
            'campaign' => $campaign,
            'openRate' => $openRate,
            'opens' => $opens
        ]);
    }

    #[Route('/campaign/{id}/open-stats', name: 'campaign_open_stats')]
    public function campaignOpenStats(
        int $id,
        EntityManagerInterface $entityManager,
        CampaignOpenRepository $campaignOpenRepository
    ): Response {
        $campaign = $entityManager->getRepository(Campaign::class)->find($id);

        if (!$campaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id ' . $id);
        }

        $openStats = $campaignOpenRepository->getOpenStatistics($campaign);
        $uniqueOpens = count($openStats);
        $totalOpens = array_sum(array_column($openStats, 'openCount'));

        return $this->render('campaign/open_stats.html.twig', [
            'campaign' => $campaign,
            'openStats' => $openStats,
            'uniqueOpens' => $uniqueOpens,
            'totalOpens' => $totalOpens,
        ]);
    }
    #[Route('/campaign/{id}/duplicate', name: 'campaign_duplicate')]
    public function duplicateCampaign(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $originalCampaign = $entityManager->getRepository(Campaign::class)->find($id);

        if (!$originalCampaign) {
            throw $this->createNotFoundException('Nie znaleziono kampanii o id '.$id);
        }

        $newCampaign = new Campaign();
        $newCampaign->setNazwa($originalCampaign->getNazwa() . ' (kopia)');
        $newCampaign->setTemat($originalCampaign->getTemat());
        $newCampaign->setNazwaNadawcy($originalCampaign->getNazwaNadawcy());
        $newCampaign->setEmailNadawcy($originalCampaign->getEmailNadawcy());
        $newCampaign->setDataUtworzenia(new \DateTime());
        $newCampaign->setStatus('Szkic');
        $newCampaign->setSegment($originalCampaign->getSegment());
        $newCampaign->setHtmlTemplate($originalCampaign->getHtmlTemplate());

        $entityManager->persist($newCampaign);
        $entityManager->flush();

        $this->addFlash('success', 'Kampania została skopiowana pomyślnie.');

        return $this->redirectToRoute('campaign_edit', ['id' => $newCampaign->getId()]);
    }
}