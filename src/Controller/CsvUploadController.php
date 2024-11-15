<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\ImportHistory;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CsvUploadController extends AbstractController
{
    private $security;
    private $entityManager;
    private $connection;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }
    #[Route('/upload-csv', name: 'upload_csv')]
    public function uploadCsv(Request $request, Connection $connection): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $filepath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;
                $file->move(dirname($filepath), $filename);



                try {
                    $connection->beginTransaction();
                    $this->processCSV($filepath, $connection);
                    $connection->commit();

                    $this->saveImportHistory();

                    unlink($filepath);
                    $this->addFlash('success', 'Dane zostały zaimportowane/zaktualizowane pomyślnie.');
                } catch (\Exception $e) {
                    $connection->rollBack();
                    $this->addFlash('error', 'Wystąpił błąd podczas importu: ' . $e->getMessage());
                }
            }
        }

        return $this->render('csv_upload/index.html.twig');
    }

    private function processCSV(string $filepath, Connection $connection): void
    {
        $handle = fopen($filepath, "r");
        if ($handle === FALSE) {
            throw new \Exception("Nie można otworzyć pliku CSV.");
        }

        // Pomiń nagłówek
        fgetcsv($handle, 0, ";");

        $batchSize = 1000;
        $values = [];

        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $values[] = $this->prepareData($data);

            if (count($values) >= $batchSize) {
                $this->batchInsert($connection, $values);
                $values = [];
            }
        }

        if (!empty($values)) {
            $this->batchInsert($connection, $values);
        }

        fclose($handle);
    }

    private function prepareData(array $data): array
    {
        return [
            'email' => $data[0] ?? '',
            'imie' => $data[1] ?? '',
            'zakup' => $data[2] ?? '',
            'product_id' => $data[3] ?? '',
            'product_nazwa' => $data[4] ?? '',
            'product_obrazek' => $data[5] ?? '',
            'product_cena' => $data[6] ?? '',
            'product_cenalow' => $data[7] ?? '',
            'product_typ' => $data[8] ?? '',
            'emhash' => $data[9] ?? '',
            'rabat' => $data[10] ?? '',
            'discount' => $data[11] ?? '',
            'customers_days_from_order' => $data[12] ?? '',
            'customers_orders_count' => $data[13] ?? '',
            'customers_balance' => $data[14] ?? '',
            'customers_firma' => $data[15] ?? '',
            'customers_value_class' => $data[16] ?? '',
            'customers_time_class' => $data[17] ?? '',
            'printer_id' => $data[18] ?? '',
            'printer_name' => $data[19] ?? '',
            'printer_image' => $data[20] ?? '',
            'city' => $data[21] ?? '',
            'last_review' => $data[22] ?? '',
            'subject' => $data[23] ?? '',
            'subject_type' => $data[24] ?? '',
            'nazwa_short' => $data[25] ?? '',
            'liczba' => $data[26] ?? '',
            'producent' => $data[27] ?? '',
            'technologia' => $data[28] ?? '',
            'lokalny' => $data[29] ?? '',
            'preheader' => $data[30] ?? '',
            'b2b_b2c' => $data[31] ?? '',
            'product_rodzaj_nazwa' => $data[32] ?? '',
            'product_typ_nazwa' => $data[33] ?? '',
            'odroczony' => $data[34] ?? '',
            'publiczny' => $data[35] ?? '',
            'przedszkole' => $data[36] ?? '',
            'segment_product_type' => $data[37] ?? '',
            'segment_otwierane' => $data[38] ?? '',
            'segment_firma' => $data[39] ?? '',
            'segment_czas' => $data[40] ?? '',
            'segment' => $data[41] ?? '',
            'AB_test' => $data[42] ?? '',
        ];
    }

    private function batchInsert(Connection $connection, array $values): void
    {
        $sql = "INSERT INTO klienci (email, imie, zakup, product_id, product_nazwa, product_obrazek, product_cena, product_cenalow, product_typ, emhash, rabat, discount, customers_days_from_order, customers_orders_count, customers_balance, customers_firma, customers_value_class, customers_time_class, printer_id, printer_name, printer_image, city, last_review, subject, subject_type, nazwa_short, liczba, producent, technologia, lokalny, preheader, b2b_b2c, product_rodzaj_nazwa, product_typ_nazwa, odroczony, publiczny, przedszkole, segment_product_type, segment_otwierane, segment_firma, segment_czas, segment, AB_test) 
            VALUES (:email, :imie, :zakup, :product_id, :product_nazwa, :product_obrazek, :product_cena, :product_cenalow, :product_typ, :emhash, :rabat, :discount, :customers_days_from_order, :customers_orders_count, :customers_balance, :customers_firma, :customers_value_class, :customers_time_class, :printer_id, :printer_name, :printer_image, :city, :last_review, :subject, :subject_type, :nazwa_short, :liczba, :producent, :technologia, :lokalny, :preheader, :b2b_b2c, :product_rodzaj_nazwa, :product_typ_nazwa, :odroczony, :publiczny, :przedszkole, :segment_product_type, :segment_otwierane, :segment_firma, :segment_czas, :segment, :AB_test)
            ON DUPLICATE KEY UPDATE 
            imie = VALUES(imie),
            zakup = VALUES(zakup),
            product_id = VALUES(product_id),
            product_nazwa = VALUES(product_nazwa),
            product_obrazek = VALUES(product_obrazek),
            product_cena = VALUES(product_cena),
            product_cenalow = VALUES(product_cenalow),
            product_typ = VALUES(product_typ),
            emhash = VALUES(emhash),
            rabat = VALUES(rabat),
            discount = VALUES(discount),
            customers_days_from_order = VALUES(customers_days_from_order),
            customers_orders_count = VALUES(customers_orders_count),
            customers_balance = VALUES(customers_balance),
            customers_firma = VALUES(customers_firma),
            customers_value_class = VALUES(customers_value_class),
            customers_time_class = VALUES(customers_time_class),
            printer_id = VALUES(printer_id),
            printer_name = VALUES(printer_name),
            printer_image = VALUES(printer_image),
            city = VALUES(city),
            last_review = VALUES(last_review),
            subject = VALUES(subject),
            subject_type = VALUES(subject_type),
            nazwa_short = VALUES(nazwa_short),
            liczba = VALUES(liczba),
            producent = VALUES(producent),
            technologia = VALUES(technologia),
            lokalny = VALUES(lokalny),
            preheader = VALUES(preheader),
            b2b_b2c = VALUES(b2b_b2c),
            product_rodzaj_nazwa = VALUES(product_rodzaj_nazwa),
            product_typ_nazwa = VALUES(product_typ_nazwa),
            odroczony = VALUES(odroczony),
            publiczny = VALUES(publiczny),
            przedszkole = VALUES(przedszkole),
            segment_product_type = VALUES(segment_product_type),
            segment_otwierane = VALUES(segment_otwierane),
            segment_firma = VALUES(segment_firma),
            segment_czas = VALUES(segment_czas),
            segment = VALUES(segment),
            AB_test = VALUES(AB_test)";

        $stmt = $connection->prepare($sql);

        foreach ($values as $value) {
            $stmt->executeQuery($value);
        }
    }
    private function saveImportHistory(): void
    {
        try {
            $this->logger->info('Saving import history...');
            $importHistory = new ImportHistory();
            $importHistory->setImportDate(new \DateTime());

            $user = $this->security->getUser();
            if ($user) {
                $importHistory->setEmail($user->getUserIdentifier());
            } else {
                $this->logger->warning('No user found when saving import history');
            }

            $this->entityManager->persist($importHistory);
            $this->entityManager->flush();
            $this->logger->info('Import history saved successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error saving import history: ' . $e->getMessage());
        }
    }
}