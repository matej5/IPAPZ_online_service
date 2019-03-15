<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Worker;
use App\Form\WorkerFormType;
use App\Form\BossFormType;
use App\Form\OffWorFormType;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkerController extends AbstractController
{
    /**
     * @Route("/worker", name="worker_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, WorkerRepository $workerRepository)
    {
        if(!($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOSS'))){
            return $this->redirectToRoute('post_index');
        }
        $form = $this->createForm(WorkerFormType::class);
        $form->handleRequest($request);
        if (($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOSS')) && ($form->isSubmitted() && $form->isValid())) {
            /** @var Worker $worker */
            $worker = $form->getData();
            $a=['ROLE_WORKER'];
            $worker->setStartTime(0);
            $worker->getUser()->setRoles($a);
            $worker->setCategory($workerRepository->findOneBy(['user' => $this->getUser()])->getCategory());
            $entityManager->persist($worker);
            $entityManager->flush();
            $this->addFlash('success', 'New worker created!');
            return $this->redirectToRoute('worker_index');
        }

        if($this->isGranted('ROLE_ADMIN')){
            $workers = $workerRepository->findAll();
        }else{
            $workers = $workerRepository->findBy(['category' => $workerRepository->findOneBy(['user' => $this->getUser()])->getCategory()]);
        }

        return $this->render('worker/index.html.twig', [
            'form' => $form->createView(),
            'workers' => $workers
        ]);
    }

    /**
     * @Route("/boss", name="boss_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function boss(Request $request, EntityManagerInterface $entityManager, WorkerRepository $workerRepository, UserRepository $userRepository)
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('post_index');
        }
        $form = $this->createForm(BossFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
            /** @var Worker $worker */
            $worker = $form->getData();
            $a=['ROLE_BOSS'];
            $worker->setStartTime(0);
            $worker->getUser()->setRoles($a);
            $entityManager->persist($worker);
            $entityManager->flush();
            $this->addFlash('success', 'New boss created!');
            return $this->redirectToRoute('worker_index');
        }

        $workers = $workerRepository->findBy(['user' => $userRepository->findByRole('ROLE_BOSS')]);

        return $this->render('boss/index.html.twig', [
            'form' => $form->createView(),
            'workers' => $workers
        ]);
    }

    /**
     * @Route("/worker/{id}", name="app_worker")
     * @param Worker $worker
     * @param WorkerRepository $workerRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return null|Response
     */
    public function worker(Worker $worker, WorkerRepository $workerRepository, Request $request, EntityManagerInterface $entityManager) {

        if(!($this->isGranted('ROLE_BOSS') || $this->isGranted('ROLE_ADMIN'))){
            return $this->redirectToRoute('post_index');
        }
        /** @var Worker $worker */
        $worker = $workerRepository->findOneBy(['id' => $worker->getId()]);

        $form = $this->createForm(OffWorFormType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $worker->setOffice($form->get('office')->getData());
            $worker->setWorkDays($form->get('workDays')->getData());
            $entityManager->flush();
        }

        return $this->render('worker/view.html.twig', [
            'form' => $form->createView(),
            'worker' => $worker
        ]);
    }
}
