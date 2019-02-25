<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptFormType;
use App\Repository\ReceiptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceiptController extends AbstractController
{
    /**
     * @Route("/receipt", name="receipt_index")
     * @param Request $request
     * @param ReceiptRepository $receiptRepository
     * @return Response
     */
    public function index(Request $request, ReceiptRepository $receiptRepository)
    {
        $form = $this->createForm(ReceiptFormType::class);
        $form->handleRequest($request);

        if($this->isGranted('ROLE_BOSS')){
            $receipts = $receiptRepository->findAll();
        }elseif ($this->isGranted('ROLE_WORKER')){
            $receipts = $receiptRepository->findBy(['worker' => $this->getUser()]);
        }else{
            $receipts = $receiptRepository->findBy(['buyer' => $this->getUser()]);
        }

        return $this->render('receipt/index.html.twig', [
            'form' => $form->createView(),
            'receipts' => $receipts
        ]);
    }
}
