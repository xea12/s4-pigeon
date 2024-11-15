<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Entity\Customer;
use App\Entity\Campaign;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CustomerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class CustomerController extends AbstractController
{
    #[Route('/customer', name: 'app_customer')]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }
    #[Route('/customers', name: 'customer_list')]
    public function list(Request $request, CustomerRepository $customerRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);
        $limit = 20;

        if ($search) {
            $customers = $customerRepository->findByEmailSearch($search);
            $pagination = null;
        } else {
            $queryBuilder = $customerRepository->createQueryBuilder('c')
                ->orderBy('c.id', 'DESC');
            $pagination = $paginator->paginate($queryBuilder, $page, $limit);
            $customers = $pagination->getItems();
        }

        return $this->render('customer/list.html.twig', [
            'customers' => $customers,
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/customer/{id}', name: 'customer_details', methods: ['GET', 'POST'])]
    public function details(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $customer = $entityManager->getRepository(Customer::class)->find($id);

        if (!$customer) {
            throw $this->createNotFoundException('Nie znaleziono klienta o id '.$id);
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Dane klienta zostały zaktualizowane.');
            return $this->redirectToRoute('customer_details', ['id' => $customer->getId()]);
        }

        // Pobierz kampanie wysłane do klienta wraz z informacjami o otwarciu
        $campaignsSent = $entityManager->createQueryBuilder()
            ->select('cs, c, co')
            ->from('App\Entity\CampaignSent', 'cs')
            ->join('cs.campaign', 'c')
            ->leftJoin('cs.campaignOpens', 'co')
            ->where('cs.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('cs.sentAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('customer/details.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'campaignsSent' => $campaignsSent,
        ]);
    }

    #[Route('/customer/{customerId}/assign-campaign/{campaignId}', name: 'assign_campaign')]
    public function assignCampaign(int $customerId, int $campaignId, EntityManagerInterface $entityManager): Response
    {
        $customer = $entityManager->getRepository(Customer::class)->find($customerId);
        $campaign = $entityManager->getRepository(Campaign::class)->find($campaignId);

        if (!$customer || !$campaign) {
            throw $this->createNotFoundException('Nie znaleziono klienta lub kampanii');
        }

        // Tutaj możesz dodać logikę przypisywania kampanii do klienta
        // Na przykład:
        // $campaign->setCustomer($customer);
        // $entityManager->flush();

        $this->addFlash('success', 'Kampania została przypisana do klienta.');

        return $this->redirectToRoute('customer_details', ['id' => $customerId]);
    }


    #[Route('/customer/{id}/edit', name: 'customer_edit')]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Dane klienta zostały zaktualizowane.');
            return $this->redirectToRoute('customer_details', ['id' => $customer->getId()]);
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

}
