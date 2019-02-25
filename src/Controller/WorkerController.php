<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Worker;
use App\Form\WorkerFormType;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, WorkerRepository $workerRepository, UserRepository $userRepository)
    {
        $form = $this->createForm(WorkerFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /** @var Worker $worker */
            $worker = new Worker();
            $rep = $this->getDoctrine()->getRepository(User::class);
            $user = $rep->findOneBy(['email' => $form->getData()->getEmail()]);
            $worker->setUser($user);
            $worker->setName($user->getFirstname() . ' ' . $user->getLastname());
            $a=['ROLE_WORKER'];
            $user->setRoles($a);
            $entityManager->persist($worker);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'New worker created!');
            return $this->redirectToRoute('worker_index');
        }

        $workers = $workerRepository->findAll();

        return $this->render('worker/index.html.twig', [
            'form' => $form->createView(),
            'workers' => $workers
        ]);
    }
}
