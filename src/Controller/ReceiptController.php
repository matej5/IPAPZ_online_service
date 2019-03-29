<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptFormType;
use App\Repository\ReceiptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
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
        } else {
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
        } else {
            $receipts = null;
            $form = $this->createForm(ReceiptFormType::class);
            $form->handleRequest($request);

            if ($this->isGranted('ROLE_ADMIN')) {
                $receipts = $receiptRepository->allJobs();
            } elseif ($this->isGranted('ROLE_BOSS')) {
                $receipts = $receiptRepository->firmJobs($this->getUser()->getWorker()->getFirmName());
            } elseif ($this->isGranted('ROLE_WORKER')) {
                $receipts = $receiptRepository->jobs($this->getUser()->getWorker());
            }

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
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/incoming", name="receipt_incoming")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function incoming(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        } else {
            $receipts = null;
            $form = $this->createForm(ReceiptFormType::class);
            $form->handleRequest($request);

            $receipts = $receiptRepository->incoming($this->getUser());

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
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/incomingJobs", name="receipt_incoming_jobs")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function incomingJobs(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        } else {
            $receipts = null;
            $form = $this->createForm(ReceiptFormType::class);
            $form->handleRequest($request);

            if ($this->isGranted('ROLE_ADMIN')) {
                $receipts = $receiptRepository->allIncomingJobs();
            } else if ($this->isGranted('ROLE_BOSS')) {
                $receipts = $receiptRepository->firmIncomingJobs($this->getUser()->getWorker()->getFirmName());
            } else if ($this->isGranted('ROLE_WORKER')) {
                $receipts = $receiptRepository->incomingJobs($this->getUser()->getWorker());
            }

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
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/missed", name="receipt_missed")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function missed(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        } else {
            $receipts = null;
            $form = $this->createForm(ReceiptFormType::class);
            $form->handleRequest($request);

            $receipts = $receiptRepository->missed($this->getUser());

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
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/missedJobs", name="receipt_missed_jobs")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function missedJobs(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        } else {
            $receipts = null;
            $form = $this->createForm(ReceiptFormType::class);
            $form->handleRequest($request);

            if ($this->isGranted('ROLE_ADMIN')) {
                $receipts = $receiptRepository->allMissedJobs();
            } else if ($this->isGranted('ROLE_BOSS')) {
                $receipts = $receiptRepository->missedFirmJobs($this->getUser()->getWorker()->getFirmName());
            } else if ($this->isGranted('ROLE_WORKER')) {
                $receipts = $receiptRepository->missedJobs($this->getUser()->getWorker());
            }

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
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/print/{id}", name="print_receipt")
     * @param          Receipt $receipt
     * @param          EntityManagerInterface $entityManager
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function print(
        Receipt $receipt,
        EntityManagerInterface $entityManager
    ) {
        if ($this->isGranted('ROLE_WORKER')) {
            $receipt->setActivity(0);

            $entityManager->persist($receipt);
            $entityManager->flush();
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $view = $this->renderView(
            'invoice/index.html.twig',
            [
                'receipt' => $receipt
            ]
        );

        $dompdf->loadHtml($view);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream(
            "receipt.pdf",
            [
                "Attachment" => false
            ]
        );

        return $this->redirectToRoute('post_index');
    }
}
