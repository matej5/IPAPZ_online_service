<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/pouzece/{worker}/{service}/{user}/{date}", name="pouzece")
     * @param                        CategoryRepository $categoryRepository
     * @param                        Request $request
     * @param                        WorkerRepository $workerRepository
     * @param                        EntityManagerInterface $entityManager
     * @param                        ServiceRepository $serviceRepository
     * @param                        null $worker
     * @param                        null $service
     * @param                        null $user
     * @param                        null $date
     * @return int
     */
    public function pouzece(
        CategoryRepository $categoryRepository,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $entityManager,
        ServiceRepository $serviceRepository,
        $worker = null,
        $service = null,
        $user = null,
        $date = null
    ) {
        return 0;
    }

    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/paypal/{worker}/{service}/{user}/{date}", name="paypal")
     * @param                        CategoryRepository $categoryRepository
     * @param                        Request $request
     * @param                        WorkerRepository $workerRepository
     * @param                        EntityManagerInterface $entityManager
     * @param                        ServiceRepository $serviceRepository
     * @param                        null $worker
     * @param                        null $service
     * @param                        null $user
     * @param                        null $date
     * @return int
     */
    public function paypal(
        CategoryRepository $categoryRepository,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $entityManager,
        ServiceRepository $serviceRepository,
        $worker = null,
        $service = null,
        $user = null,
        $date = null
    ) {
    }
}
