<?php

namespace App\Controller;

use App\Entity\Office;
use App\Entity\Receipt;
use App\Entity\Service;
use App\Entity\Worker;
use App\Form\ReceiptFormType;
use App\Form\ServiceAddFormType;
use App\Form\ServiceFormType;
use App\Form\WorkerFormType;
use App\Repository\CategoryRepository;
use App\Repository\OfficeRepository;
use App\Repository\ReceiptRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PaymentController extends AbstractController
{
    /**
     * @Route("/pouzece/{worker}/{service}/{user}/{date}", name="service_index")
     * @param                        Request $request
     * @param                        CategoryRepository $categoryRepository
     * @param                        WorkerRepository $workerRepository
     * @param                        EntityManagerInterface $entityManager
     * @param                        ServiceRepository $serviceRepository
     * @return                       Response
     */
    public function pouzece(
        CategoryRepository $categoryRepository,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $entityManager,
        ServiceRepository $serviceRepository,
        $category = null
    ) {
    }
}
