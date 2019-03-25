<?php

namespace App\Controller;

use App\Form\ReceiptFormType;
use App\Repository\ReceiptRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ReceiptController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/receipt", name="receipt_index")
     * @param             Request $request
     * @param             ReceiptRepository $receiptRepository
     * @return            \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ReceiptRepository $receiptRepository)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }

        $form = $this->createForm(ReceiptFormType::class);
        $form->handleRequest($request);

        $receipts = $receiptRepository->findBy(['buyer' => $this->getUser()]);

        return $this->render(
            'receipt/index.html.twig',
            [
                'form' => $form->createView(),
                'receipts' => $receipts
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/jobs", name="receipt_view")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          WorkerRepository $workerRepository
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function view(Request $request, ReceiptRepository $receiptRepository, WorkerRepository $workerRepository)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }

        $receipts = null;
        $form = $this->createForm(ReceiptFormType::class);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_ADMIN')) {
            $receipts = $receiptRepository->findAll();
        } elseif ($this->isGranted('ROLE_BOSS')) {
            $receipts = $receiptRepository->findBy(
                [
                'worker' => $workerRepository->findOneBy(
                    [
                    'category' => $workerRepository->findOneBy(
                        [
                        'user' => $this->getUser()
                        ]
                    )->getCategory()
                    ]
                )
                ]
            );
        } elseif ($this->isGranted('ROLE_WORKER')) {
            $receipts = $receiptRepository->findBy(
                [
                'worker' => $workerRepository->findOneBy(
                    [
                    'user' => $this->getUser()
                    ]
                )
                ]
            );
        }

        return $this->render(
            'receipt/index.html.twig',
            [
                'form' => $form->createView(),
                'receipts' => $receipts
            ]
        );
    }
}
