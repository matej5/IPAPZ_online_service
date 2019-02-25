<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Entity\Service;
use App\Entity\Worker;
use App\Form\ServiceFormType;
use App\Form\WorkerFormType;
use App\Repository\OfficeRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service", name="service_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $form = $this->createForm(ServiceFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /** @var Service $service */
            $service = $form->getData();
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service created!');
            return $this->redirectToRoute('service_index');
        }

        $service = $serviceRepository->findAll();

        return $this->render('service/service.html.twig', [
            'form' => $form->createView(),
            'services' => $service
        ]);
    }


    /**
     * @Route("/service/{id}", name="service_add")
     * @param Service $service
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function add(Service $service,Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $form = $this->createForm(ServiceFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER')) {
            /** @var Service $service */
            $this->getUser()->addService($service);
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service added to cart!');
            return $this->redirectToRoute('service_index');
        }

        $service = $serviceRepository->findAll();

        return $this->render('service/service.html.twig', [
            'form' => $form->createView(),
            'services' => $service
        ]);
    }

    /**
     * @Route("/cart", name="service_view")
     * @param Request $request
     * @param WorkerRepository $workerRepository
     * @return Response
     */
    public function view(Request $request, WorkerRepository $workerRepository)
    {
        $form = $this->createForm(ServiceFormType::class);
        $form->handleRequest($request);
        $service = $this->getUser()->getServices();
        $workers = $workerRepository->findAll();
        return $this->render('service/cart.html.twig', [
            'form' => $form->createView(),
            'services' => $service,
            'workers' => $workers
        ]);
    }
    /**
     * @Route("/cart/{id}", name="service_buy")
     * @param Worker $worker
     * @param Request $request
     * @param OfficeRepository $officeRepository
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @return Response
     */
    public function buy(Worker $worker, Request $request, OfficeRepository $officeRepository, EntityManagerInterface $entityManager, WorkerRepository $workerRepository)
    {
        $form = $this->createForm(WorkerFormType::class);
        $form->handleRequest($request);
        /** @var Receipt $receipt */
        $receipt = new Receipt();
        if ($this->isGranted('ROLE_USER')) {
            foreach ($this->getUser()->getServices() as $service){
                $receipt->addService($service);
                $this->getUser()->removeService($service);
            }
            $receipt->setWorker($worker);
            $receipt->setOffice($officeRepository->findOneBy(['id' => 1]));
            $receipt->setBuyer($this->getUser());
            $entityManager->persist($receipt);
            $entityManager->flush();
            return $this->redirectToRoute('service_index');
        }
        $service = $this->getUser()->getServices();

        return $this->render('service/cart.html.twig', [
            'form' => $form->createView(),
            'services' => $service
        ]);
    }

    /**
     * @Security("user == post.getUser()")
     * @Route("/service/{id}/delete", name="service_delete")
     * @param Service $service
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteService(Service $service, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($service);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully deleted!');
        return $this->redirectToRoute('service_index');
    }
}
