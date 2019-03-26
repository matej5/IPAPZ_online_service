<?php

namespace App\Controller;

use App\Form\ReceiptFormType;
use App\Repository\ReceiptRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ReceiptController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/receipt", name="receipt_index")
     * @param             Request $request
     * @param             ReceiptRepository $receiptRepository
     * @param             PaginatorInterface $paginator
     * @return            \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }

        $form = $this->createForm(ReceiptFormType::class);
        $form->handleRequest($request);
        $receipts = $receiptRepository->all($this->getUser());

        $pagination = $paginator->paginate(
            $receipts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'receipt/index.html.twig',
            [
                'form' => $form->createView(),
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/jobs", name="receipt_view")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function view(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }


        $receipts = null;
        $form = $this->createForm(ReceiptFormType::class);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_ADMIN')) {
            $receipts = $receiptRepository->allJobs();
        } else if ($this->isGranted('ROLE_BOSS')) {
            $receipts = $receiptRepository->firmJobs($this->getUser()->getWorker()->getFirmName());
        } else if ($this->isGranted('ROLE_WORKER')) {
            $receipts = $receiptRepository->jobs($this->getUser()->getWorker());
        }

        $pagination = $paginator->paginate(
            $receipts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'receipt/jobs.html.twig',
            [
                'form' => $form->createView(),
                'pagination' => $pagination
            ]
        );
    }
}
