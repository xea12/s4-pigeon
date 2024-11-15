<?php

// src/Repository/CustomerRepository.php
namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class CustomerRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Customer::class);
        $this->paginator = $paginator;
    }
    public function getUniqueSegmentValues(string $segmentField): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT c.' . $segmentField)
            ->where('c.' . $segmentField . ' IS NOT NULL')
            ->orderBy('c.' . $segmentField, 'ASC')
            ->getQuery()
            ->getResult();

        // Przekształć wynik na prostą tablicę
        return array_column($result, $segmentField);
    }
    public function countCustomersBySegment(string $segment): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.segment = :segment')
            ->setParameter('segment', $segment)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRandomCustomers(int $count): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT k.* FROM klienci k
        ORDER BY RAND()
        LIMIT :count
    ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('count', $count, \PDO::PARAM_INT);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }


    public function getCustomerDataForTemplate(Customer $customer): array
    {
        return [
            'email' => $customer->getEmail() ?? '',
            'discount' => $customer->getDiscount() ?? '',
            'imie' => $customer->getImie() ?? '',
            'last_review' => $customer->getLastReview() ?? '',
            'nazwa_short' => $customer->getNazwaShort() ?? '',
            'preheader' => $customer->getPreheader() ?? '',
            'printer_id' => $customer->getPrinterId() ?? '',
            'printer_name' => $customer->getPrinterName() ?? '',
            'product_id' => $customer->getProductId() ?? '',
            'product_nazwa' => $customer->getProductNazwa() ?? '',
            'product_obrazek' => $customer->getProductObrazek() ?? '',
            'product_rodzaj_nazwa' => $customer->getProductRodzajNazwa() ?? '',
            'rabat' => $customer->getRabat() ?? '',
        ];
    }
    public function findCustomerDataForCampaign(int $customerId): array
    {
        $customer = $this->find($customerId);
        if (!$customer) {
            return [];
        }

        return [
            'product_id' => $customer->getProductId(),
            'product_rodzaj_nazwa' => $customer->getProductRodzajNazwa(),
            'nazwa_short' => $customer->getNazwaShort(),
            'printer_id' => $customer->getPrinterId(),
            'printer_name' => $customer->getPrinterName(),
            'discount' => $customer->getDiscount(),
            // Dodaj inne pola według potrzeb
        ];
    }
    public function findByEmailSearch(?string $search): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($search) {
            $qb->andWhere('c.email LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findBySegment(string $segment): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.segment = :segment')
            ->setParameter('segment', $segment)
            ->getQuery()
            ->getResult();
    }


    public function findPaginated(int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    // Możesz dodać tutaj niestandardowe metody zapytań
}