<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/pouzece/{worker}/{service}/{date}",
     *     name="pouzece")
     * @param                        EntityManagerInterface $entityManager
     * @param                        WorkerRepository $workerRepository
     * @param                        ServiceRepository $serviceRepository
     * @param                        null $worker
     * @param                        null $service
     * @param                        null $date
     * @return                       \Symfony\Component\HttpFoundation\Response
     */
    public function pouzece(
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        $worker = null,
        $service = null,
        $date = null
    ) {
        if ($worker != null and
            $service != null and
            $this->getUser() != null and
            $date != null and
            $this->getUser() != null
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

            return $this->redirectToRoute('office_index');
        }

        $this->addFlash('alert', 'Something went wrong!');

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/paypal/{worker}/{service}/{date}", name="paypal-pay")
     * @param WorkerRepository $workerRepository
     * @param ServiceRepository $serviceRepository
     * @param null $worker
     * @param null $service
     * @param null $date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payPalShow(
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        $worker = null,
        $service = null,
        $date = null
    ) {
        $gateway = self::gateway();
        $worker = $workerRepository->findOneBy(['id' => $worker]);
        $service = $serviceRepository->findOneBy(['id' => $service]);

        return $this->render(
            'paypal/paypal.html.twig',
            [
                'gateway' => $gateway,
                'service' => $service,
                'worker' => $worker,
                'date' => $date
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/transaction/paypal-payment/{worker}/{service}/{date}", name="paypal-payment")
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @param ServiceRepository $serviceRepository
     * @param Request $request
     * @param null $worker
     * @param Service $service
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
}
