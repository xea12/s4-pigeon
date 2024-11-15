<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CsvUploadController1 extends AbstractController
{

    #[Route('/upload-csv', name: 'upload_csv')]
    public function uploadCsv(Request $request, EntityManagerInterface $entityManager, CustomerRepository $customerRepository): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads', $filename);
                $filepath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

                $handle = fopen($filepath, "r");
                if ($handle !== FALSE) {
                    // Pomiń pierwszy wiersz (nagłówki)
                    fgetcsv($handle, 1000, ";");

                    $entityManager->beginTransaction();

                    try {
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                            $email = $data[0];
                            $customer = $customerRepository->findOneBy(['email' => $email]);

                            if (!$customer) {
                                $customer = new Customer();
                                $customer->setEmail($email);
                                $customer->setAddDate(new \DateTime());
                            }

                            // Aktualizuj dane klienta (wszystko oprócz email i add_Date)
                            $this->updateCustomerData($customer, $data);
                            $entityManager->persist($customer);

                        }

                        $entityManager->flush();
                        $entityManager->commit();

                        fclose($handle);
                        unlink($filepath);

                        $this->addFlash('success', 'Dane zostały zaimportowane/zaktualizowane pomyślnie.');
                    } catch (\Exception $e) {
                        $entityManager->rollback();
                        $this->addFlash('error', 'Wystąpił błąd podczas importu: ' . $e->getMessage());
                    }
                }
            }
        }

        return $this->render('csv_upload/index.html.twig');
    }

    private function updateCustomerData(Customer $customer, array $data): void
    {
        $customer->setImie($data[1] ?? '');
        $customer->setZakup($data[2] ?? '');
        $customer->setProductId($data[3] ?? '');
        $customer->setProductNazwa($data[4] ?? '');
        $customer->setProductObrazek($data[5] ?? '');
        $customer->setProductCena($data[6] ?? '');
        $customer->setProductCenalow($data[7] ?? '');
        $customer->setProductTyp($data[8] ?? '');
        $customer->setEmhash($data[9] ?? '');
        $customer->setRabat($data[10] ?? '');
        $customer->setDiscount($data[11] ?? '');
        $customer->setCustomersDaysFromOrder($data[12] ?? '');
        $customer->setCustomersOrdersCount($data[13] ?? '');
        $customer->setCustomersBalance($data[14] ?? '');
        $customer->setCustomersFirma($data[15] ?? '');
        $customer->setCustomersValueClass($data[16] ?? '');
        $customer->setCustomersTimeClass($data[17] ?? '');
        $customer->setPrinterId($data[18] ?? '');
        $customer->setPrinterName($data[19] ?? '');
        $customer->setPrinterImage($data[20] ?? '');
        $customer->setCity($data[21] ?? '');
        $customer->setLastReview($data[22] ?? '');
        $customer->setSubject($data[23] ?? '');
        $customer->setSubjectType($data[24] ?? '');
        $customer->setNazwaShort($data[25] ?? '');
        $customer->setLiczba($data[26] ?? '');
        $customer->setProducent($data[27] ?? '');
        $customer->setTechnologia($data[28] ?? '');
        $customer->setLokalny($data[29] ?? '');
        $customer->setPreheader($data[30] ?? '');
        $customer->setB2bB2c($data[31] ?? '');
        $customer->setProductRodzajNazwa($data[32] ?? '');
        $customer->setProductTypNazwa($data[33] ?? '');
        $customer->setOdroczony($data[34] ?? '');
        $customer->setPubliczny($data[35] ?? '');
        $customer->setPrzedszkole($data[36] ?? '');
        $customer->setSegmentProductType($data[37] ?? '');
        $customer->setSegmentOtwierane($data[38] ?? '');
        $customer->setSegmentFirma($data[39] ?? '');
        $customer->setSegmentCzas($data[40] ?? '');
        $customer->setSegment($data[41] ?? '');
        $customer->setABTest($data[42] ?? '');
    }
}