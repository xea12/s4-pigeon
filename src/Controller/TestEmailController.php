<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Repository\CustomerRepository;
use App\Service\TemplateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Address;

class TestEmailController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $templateProcessorLogger)
    {
        $this->logger = $templateProcessorLogger;
    }

    #[Route('/sendMail', name: 'send_mail')]
    public function sendMail(MailerInterface $mailer) {
        $email = (new Email())
            ->from('lukasz@drtusz.pl')  // Użyj swojego adresu Proton Mail
            ->to('lukasz@drtusz.pl')
            ->subject('Witaj')
            ->html("<b>Cześć</b> <br> To jest testowy e-mail wysłany z Symfony przez Postmark.");

        try {
            $mailer->send($email);
            $this->logger->info('E-mail wysłany pomyślnie');
            return new Response("Wysłano e-maila. Sprawdź logi i panel Postmark.");
        } catch (\Exception $e) {
            $this->logger->error('Błąd wysyłania e-maila: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return new Response("Błąd wysyłania e-maila. Sprawdź logi aplikacji.");
        }
    }

    #[Route('/campaign/{id}/test-email', name: 'campaign_test_email', methods: ['GET', 'POST'])]
    public function testEmail(
        ?string $id,
        Request $request,
        CustomerRepository $customerRepository,
        TemplateProcessor $templateProcessor,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            if ($id === null) {
                throw new NotFoundHttpException('Brak ID kampanii');
            }

            $campaignId = (int) $id;
            $campaign = $entityManager->getRepository(Campaign::class)->find($campaignId);

            if (!$campaign) {
                throw new NotFoundHttpException('Nie znaleziono kampanii o id '.$id);
            }

            $randomCustomers = $customerRepository->getRandomCustomers(3);
            $previewHtml = null;
            $previewSubject = null;
            if ($request->isMethod('POST') || $request->query->has('preview')){
                $selectedCustomerId = $request->request->get('selectedCustomer') ?? $request->query->get('selectedCustomer');
                $testEmailAddress = $request->request->get('testEmail');

                $selectedCustomer = $customerRepository->find($selectedCustomerId);
                error_log("Customer data for template: " . json_encode($customerRepository->getCustomerDataForTemplate($selectedCustomer)));
                if (!$selectedCustomer) {
                    return new JsonResponse(['success' => false, 'message' => 'Wybrany klient nie istnieje.'], 400);
                } else {
                    $previewHtml = $templateProcessor->processTemplate(
                        $campaign->getHtmlTemplate(),
                        $customerRepository->getCustomerDataForTemplate($selectedCustomer)
                    );
                    $previewSubject = $templateProcessor->processTemplate(
                        $campaign->getTemat(),
                        $customerRepository->getCustomerDataForTemplate($selectedCustomer)
                    );
                }

                // Sprawdź, czy adres e-mail jest dostępny przed wysłaniem
                if (!empty($testEmailAddress)) {
                    try {
                        $processedTemplate = $templateProcessor->processTemplate(
                            $campaign->getHtmlTemplate(),
                            $customerRepository->getCustomerDataForTemplate($selectedCustomer)
                        );
                        $processedSubject = $templateProcessor->processTemplate(
                            $campaign->getTemat(),
                            $customerRepository->getCustomerDataForTemplate($selectedCustomer)
                        );
                        //$this->logger->info('Przetworzony szablon e-maila', ['template' => $processedTemplate]);

                        $email = (new Email())
                            ->from(new Address('noreply@trial-jpzkmgqr0k24059v.mlsender.net', 'NazwaTenTego'))
                            ->to($testEmailAddress)
                            ->subject($processedSubject)
                            ->html($processedTemplate);

                        $this->logger->info('Próba wysłania e-maila', [
                            'to' => $testEmailAddress,
                            'subject' => 'Test kampanii: ' . $processedSubject,
                        ]);
                        $this->logger->info('Konfiguracja email przed wysyłką', [
                            'from' => $email->getFrom(),
                            'to' => $email->getTo(),
                            'subject' => $email->getSubject(),
                            'headers' => $email->getHeaders()->toString(),
                        ]);

                        $mailer->send($email);

                        $this->logger->info('E-mail wysłany pomyślnie');

                        return new JsonResponse(['success' => true, 'message' => 'Testowy e-mail został wysłany.']);
                    } catch (\Exception $e) {
                        $this->logger->error('Błąd podczas wysyłania e-maila', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        return new JsonResponse(['success' => false, 'message' => 'Błąd podczas wysyłania e-maila: ' . $e->getMessage()], 500);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Błąd wysyłki email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Błąd wysyłki: ' . $e->getMessage()
            ], 500);
        }

        return $this->render('test_email/index.html.twig', [
            'campaign' => $campaign,
            'randomCustomers' => $randomCustomers,
            'previewHtml' => $previewHtml,
            'previewSubject' => $previewSubject,
        ]);
    }


}