<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\PaymentFormType;
use App\Repository\PaymentRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/pouzece/{worker}/{service}/{date}",
     *     name="pouzece")
     * @param                        EntityManagerInterface $entityManager
     * @param                        WorkerRepository $workerRepository
     * @param                        ServiceRepository $serviceRepository
     * @param PaymentRepository $paymentRepository
     * @param                        null $worker
     * @param                        null $service
     * @param                        null $date
     * @return                       \Symfony\Component\HttpFoundation\Response
     */
    public function pouzece(
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        PaymentRepository $paymentRepository,
        $worker = null,
        $service = null,
        $date = null
    ) {
        if (!$paymentRepository->findOneBy(['id' => 1])->getPouzece()) {
            $this->addFlash('alert', 'Something went wrong!');

            return $this->redirectToRoute('post_index');
        } elseif ($worker != null and
                $service != null and
                $this->getUser() != null and
                $date != null
            ) {
                $worker = $workerRepository->findOneBy(['id' => $worker]);
                $service = $serviceRepository->findOneBy(['id' => $service]);
                $date = strtotime($date);

                $date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $date));
                $receipt = new Receipt();
                $receipt->setWorker($worker);
                $receipt->setService($service);
                $receipt->setStartOfService($date);
                $receipt->setBuyer($this->getUser());
                $receipt->setOffice($worker->getOffice());
                $receipt->setMethod('Pouzeće');
                $receipt->setActivity(1);

                $entityManager->persist($receipt);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'You ordered service: ' .
                    $service->getName() .
                    ' at ' .
                    $date->format('Y-m-d H:i:s') .
                    '!'
                );

                return $this->redirectToRoute('post_index');
        }

            $this->addFlash('alert', 'Something went wrong!');

            return $this->redirectToRoute('post_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/payment/{worker}/{service}/{date}", name="payment-pay")
     * @param WorkerRepository $workerRepository
     * @param ServiceRepository $serviceRepository
     * @param PaymentRepository $paymentRepository
     * @param null $worker
     * @param null $service
     * @param null $date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payPalShow(
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        PaymentRepository $paymentRepository,
        $worker = null,
        $service = null,
        $date = null
    ) {

        if (!$paymentRepository->findOneBy(['id' => 1])->getPouzece()) {
            $this->addFlash('alert', 'Something went wrong!');

            return $this->redirectToRoute('post_index');
        } else {
            $gateway = self::gateway();
            $worker = $workerRepository->findOneBy(['id' => $worker]);
            $service = $serviceRepository->findOneBy(['id' => $service]);

            return $this->render(
                'payment/paypal.html.twig',
                [
                    'gateway' => $gateway,
                    'service' => $service,
                    'worker' => $worker,
                    'date' => $date
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/transaction/payment-payment/{worker}/{service}/{date}", name="payment-payment")
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @param ServiceRepository $serviceRepository
     * @param Request $request
     * @param null $worker
     * @param null $service
     * @param null $date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payment(
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        Request $request,
        $worker = null,
        $service = null,
        $date = null
    ) {
        $gateway = self::gateway();
        $service = $serviceRepository->findOneBy(['id' => $service]);
        $worker = $workerRepository->findOneBy(['id' => $worker]);
        $amount = $service->getCost();
        $nonce = $request->get('payment_method_nonce');
        $result = $gateway->transaction()->sale(
            [
                'amount' => $amount,
                'paymentMethodNonce' => $nonce
            ]
        );
        $transaction = $result->transaction;
        if ($transaction == null) {
            $this->addFlash('warning', 'Payment nije prošao');
            return $this->redirectToRoute('service_index');
        }

        $receipt = new Receipt();
        $receipt->setService($service);
        if ($this->getUser()) {
            $receipt->setBuyer($this->getUser());
        }

        $receipt->setWorker($worker);
        $receipt->setOffice($worker->getOffice());
        $receipt->setMethod('Paypal');
        $receipt->setActivity(1);

        $date = strtotime($date);

        $date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $date));
        $receipt->setStartOfService($date);

        $entityManager->persist($receipt);
        $entityManager->flush();

        $this->addFlash('success', 'Uspiješno ste platili');
        return $this->redirectToRoute('service_index');
    }

    public function gateway()
    {
        $gateway = new \Braintree_Gateway(
            [
                'environment' => 'sandbox',
                'merchantId' => 'm2hf33ycfks9kky2',
                'publicKey' => '787g2vgdp5mqxz9j',
                'privateKey' => 'b6c1e90e8997ffc9ea01991f56504ef8'
            ]
        );
        return $gateway;
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/method", name="method_index")
     * @param EntityManagerInterface $entityManager
     * @param PaymentRepository $paymentRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paymentMethod(
        EntityManagerInterface $entityManager,
        PaymentRepository $paymentRepository,
        Request $request
    ) {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        }

        $payment = $paymentRepository->findOneBy(['id' => 1]);


        $form = $this->createForm(PaymentFormType::class, $payment);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payment);
            $entityManager->flush();
        }

        return $this->render(
            'payment/method.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
