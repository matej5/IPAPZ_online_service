<?php

namespace App\Controller;

use App\Entity\Worker;
use App\Form\WorkerFormType;
use App\Form\BossFormType;
use App\Form\OffWorFormType;
use App\Repository\ReceiptRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WorkerController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/worker", name="worker_index")
     * @param            Request $request
     * @param            EntityManagerInterface $entityManager
     * @param            WorkerRepository $workerRepository
     * @param UserRepository $userRepository
     * @param            PaginatorInterface $paginator
     * @return           \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ) {
        if (!($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOSS'))) {
            return $this->redirectToRoute('post_index');
        }

        $users = $userRepository->findWithoutRole();

        $form = $this->createForm(WorkerFormType::class, null, ['users' => $users]);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_BOSS')
            && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Worker $worker
             */
            $worker = $form->getData();
            $a = ['ROLE_WORKER'];
            $worker->setStartTime(0);
            $worker->getUser()->setRoles($a);
            $worker->getUser()->setWorker($worker);
            $worker->setFirmName($workerRepository->findOneBy(['user' => $this->getUser()])->getFirmName());
            $entityManager->persist($worker);
            $entityManager->flush();
            $this->addFlash('success', 'New worker created!');
            return $this->redirectToRoute('worker_index');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $workers = $workerRepository->findAll();
        } else {
            $workers = $workerRepository->findBy(
                [
                'firmName' => $workerRepository->findOneBy(
                    [
                    'user' => $this->getUser()
                    ]
                )->getFirmName()
                ]
            );
        }

        $pagination = $paginator->paginate(
            $workers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'worker/index.html.twig',
            [
                'form' => $form->createView(),
                'workers' => $pagination
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/boss", name="boss_index")
     * @param          Request $request
     * @param          EntityManagerInterface $entityManager
     * @param          WorkerRepository $workerRepository
     * @param          UserRepository $userRepository
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function boss(
        Request $request,
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        UserRepository $userRepository
    ) {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        } else {
            $users = $userRepository->findWithoutRole();

            $form = $this->createForm(BossFormType::class, null, ['users' => $users]);
            $form->handleRequest($request);

            if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
                /**
                 * @var Worker $worker
                 */
                $worker = $form->getData();
                $a = ['ROLE_BOSS'];
                $worker->getUser()->setRoles($a);
                $worker->getUser()->setWorker($worker);
                $worker->setStartTime(0);
                $entityManager->persist($worker);
                $entityManager->flush();
                $this->addFlash('success', 'New boss created!');
                return $this->redirectToRoute('worker_index');
            }

            $workers = $workerRepository->findBy(['user' => $userRepository->findByRole('ROLE_BOSS')]);

            return $this->render(
                'boss/index.html.twig',
                [
                    'form' => $form->createView(),
                    'workers' => $workers
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/worker/{id}", name="app_worker")
     * @param                 Worker $worker
     * @param                 WorkerRepository $workerRepository
     * @param                 Request $request
     * @param                 EntityManagerInterface $entityManager
     * @return                null|  \Symfony\Component\HttpFoundation\Response
     */
    public function worker(
        Worker $worker,
        WorkerRepository $workerRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        if (!($this->isGranted('ROLE_BOSS') || $this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('post_index');
        } else {
            /**
             * @var Worker $worker
             */
            $worker = $workerRepository->findOneBy(['id' => $worker->getId()]);

            $form = $this->createForm(OffWorFormType::class, $worker);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $worker->setOffice($form->get('office')->getData());
                $worker->setWorkDays($form->get('workDays')->getData());
                $entityManager->flush();
            }

            return $this->render(
                'worker/view.html.twig',
                [
                    'form' => $form->createView(),
                    'worker' => $worker
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/check/{worker}/{service}/{date}", name="check_for_reservation")
     * @param                 ReceiptRepository $receiptRepository
     * @param                 WorkerRepository $workerRepository
     * @param                 ServiceRepository $serviceRepository
     * @param null $worker
     * @param null $service
     * @param null $date
     * @return                String
     */
    public function check(
        ReceiptRepository $receiptRepository,
        WorkerRepository $workerRepository,
        ServiceRepository $serviceRepository,
        $worker = null,
        $service = null,
        $date = null
    ) {
        if ($worker != null) {
            $worker = $workerRepository->findOneBy(['id' => $worker]);
        }

        //vrijeme u sekundama od odabranog vremena
        $chosenTime = strtotime($date);

        $avaliable = false;
        $radniDan = false;
        $correctTime = false;

        if ($chosenTime > strtotime("now") && $chosenTime < strtotime("now") + 14*24*3600) {
            $correctTime = true;
            $day = pow(2, date('N', strtotime($date)) - 1);

            $time = date('H:i', $chosenTime);
            list($hours, $minutes) = explode(':', $time, 2);
            $sec = $hours * 3600 + $minutes * 60;

            if ($service != null) {
                $service = $serviceRepository->findOneBy(['id' => $service]);
            }

            //duzina trajanja servisa u sekundama
            $chosenServDur = $service->getDuration() * 60;

            if ($worker->getWorkDays() & $day &&
                $worker->getStartTime() * 3600 <= $sec &&
                ($worker->getStartTime() + $worker->getWorkTime()) * 3600 >=
                $sec + $chosenServDur) {
                $radniDan = true;
            } else {
                $radniDan = false;
            }

            $receipts = $receiptRepository->findAll(['worker' => $worker]);

            foreach ($receipts as $receipt) {
                //svaka narudzba (pocetak i kraj) u sekundama
                $startOfServ = date_timestamp_get($receipt->getStartOfService());
                $servDur = $receipt->getService()->getDuration() * 60;

                if ($startOfServ > $chosenTime &&
                    $startOfServ < $chosenTime + $chosenServDur) {
                    $avaliable = false;
                    break;
                } elseif ($startOfServ + $servDur > $chosenTime &&
                    $startOfServ + $servDur < $chosenTime + $chosenServDur) {
                    $avaliable = false;
                    break;
                } elseif ($startOfServ < $chosenTime &&
                    $startOfServ + $servDur > $chosenTime + $chosenServDur) {
                    $avaliable = false;
                    break;
                } else {
                    $avaliable = true;
                }
            }
        }

        $data = ['works' => $radniDan, 'avaliable' => $avaliable, 'correctTime' => $correctTime];
        $response = new JsonResponse($data);
        return $response;
    }
}
