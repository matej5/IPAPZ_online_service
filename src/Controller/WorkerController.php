<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Worker;
use App\Form\WorkerFormType;
use App\Form\BossFormType;
use App\Form\OffWorFormType;
use App\Repository\JobRepository;
use App\Repository\OfficeRepository;
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
             * @var Job $job
             */
            $job = new Job;
            $job->setUser($this->getUser());
            $job->setWorker($form->get('user')->getData());
            $job->setFirmName($this->getUser()->getWorker()->getFirmName());
            $entityManager->persist($job);
            $entityManager->flush();
            $this->addFlash('success', 'New work offer sent!');
            return $this->redirectToRoute('worker_index');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $workers = $workerRepository->findAll();
        } else {
            $workers = $workerRepository->findByFirm($this->getUser()->getWorker()->getFirmName());
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
                 * @var Job $job
                 */
                $job = new Job;
                $job->setUser($this->getUser());
                $job->setWorker($form->get('user')->getData());
                $job->setFirmName($form->get('firmName')->getData());
                $entityManager->persist($job);
                $entityManager->flush();
                $this->addFlash('success', 'New boss offer sent!');
                return $this->redirectToRoute('boss_index');
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
     * @Symfony\Component\Routing\Annotation\Route("/invite", name="job_invite")
     * @param          JobRepository $jobRepository
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function invite(
        JobRepository $jobRepository
    ) {
        if (empty($this->getUser()->getJobsRequest())) {
            return $this->redirectToRoute('post_index');
        } else {
            $jobs = $jobRepository->findBy(['worker' => $this->getUser()]);

            return $this->render(
                'worker/invite.html.twig',
                [
                    'jobs' => $jobs
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/acceptJob/{id}", name="job_index")
     * @param          Job $job
     * @param          EntityManagerInterface $entityManager
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function acceptJob(
        Job $job,
        EntityManagerInterface $entityManager
    ) {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        } else {
            if (!$this->getUser()->getWorker()) {
                $worker = new Worker;
                $worker->setFirmName($job->getFirmName());
                $worker->setUser($this->getUser());
                $worker->setStartTime(0);
                $this->getUser()->setWorker($worker);
                if ($job->getUser()->getId() == 1) {
                    $a = ['ROLE_BOSS'];
                } else {
                    $a = ['ROLE_WORKER'];
                }

                $worker->getUser()->setRoles($a);
                $entityManager->persist($worker);
                foreach ($this->getUser()->getJobsRequest() as $j) {
                    $entityManager->remove($j);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Job accepted!');
                return $this->redirectToRoute('worker_index');
            }

            return $this->redirectToRoute('worker_index');
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/worker/{id}", name="app_worker")
     * @param                 Worker $worker
     * @param                 WorkerRepository $workerRepository
     * @param                 OfficeRepository $officeRepository
     * @param                 Request $request
     * @param                 EntityManagerInterface $entityManager
     * @return                null|  \Symfony\Component\HttpFoundation\Response
     */
    public function worker(
        Worker $worker,
        WorkerRepository $workerRepository,
        OfficeRepository $officeRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        if (!($this->isGranted('ROLE_BOSS')
            && ($this->getUser()->getWorker()->getFirmName() == $worker->getFirmName()))
        ) {
            return $this->redirectToRoute('app_user', ['id' => $worker->getUser()->getId()]);
        } else {
            /**
             * @var Worker $worker
             */
            $worker = $workerRepository->findOneBy(['id' => $worker->getId()]);

            $offices = $officeRepository->findBy(['owner' => $this->getUser()->getWorker()]);

            $form = $this->createForm(OffWorFormType::class, $worker, ['offices' => $offices]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $worker->setOffice($form->get('office')->getData());
                $worker->setWorkDays($form->get('workDays')->getData());
                $worker->setWorkTime($form->get('workTime')->getData());
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

            $avaliable = true;
            $receipts = $receiptRepository->findBy(['worker' => $worker]);

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
                } elseif ($startOfServ <= $chosenTime &&
                    $startOfServ + $servDur >= $chosenTime + $chosenServDur) {
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
