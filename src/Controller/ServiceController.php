<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Entity\Service;
use App\Entity\Worker;
use App\Form\ServiceAddFormType;
use App\Form\ServiceFormType;
use App\Form\WorkerFormType;
use App\Repository\CategoryRepository;
use App\Repository\OfficeRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service/{category}", name="service_index")
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param WorkerRepository $workerRepository
     * @param EntityManagerInterface $entityManager
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index($category = null, CategoryRepository $categoryRepository, Request $request, WorkerRepository $workerRepository, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $categories = $categoryRepository->findAll();

        $form = $this->createForm(ServiceFormType::class, ['categories' => $categories]);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /** @var Service $service */
            $data = $form->getData();

            $service = new Service();

            $service->setName($data['name']);
            $service->setCost($data['cost']);
            $service->setDuration($data['duration']);
            $service->setDescription($data['description']);
            $service->setImage($data['image']);
            $service->setStatus('queued');
            foreach ($data['category'] as $c){
                $service->addCategory($c);
            }
            $service->setBoss($workerRepository->findOneBy(['user' => $this->getUser()]));

            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service created!');
            return $this->redirectToRoute('service_index');
        }


        if(strtolower($request->get('category')) == 'queue' && $this->isGranted('ROLE_ADMIN')){
            $service = $serviceRepository->findBy(['status' => 'queued']);
        } else {
            $service = $serviceRepository->findByService($category, ['status' => 'allowed']);
        }

        return $this->render('service/service.html.twig', [
            'form' => $form->createView(),
            'category' => $categories,
            'services' => $service,
            'title' => $category
        ]);
    }

    /**
     * @Route("/serviceEdit/{id}", name="service_edit")
     * @param Service $service
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function viewService(Service $service, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        if (!($this->isGranted('ROLE_BOSS') && $this->getUser() == $service->getBoss()->getUser())){
            return $this->redirectToRoute('service_index');
        }

        $categories = $categoryRepository->findAll();

        $form = $this->createForm(ServiceFormType::class, ['categories' => $categories, 'service' => $service]);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /** @var Service $service */
            $service = $form->getData();
            $service->setStatus('queued');
            $service->setDuration($form->get('duration')->getData());
            $service->setName($form->get('name')->getData());
            $service->setCost($form->get('cost')->getData());
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Edited service!');
            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/view.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'services' => $service,
            'title' => 'Edit service'
        ]);
    }

    /**
     * @Route("/allow/{id}", name="service_allow")
     * @param Service $service
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function allow(Service $service, EntityManagerInterface $entityManager)
    {
        if($this->isGranted('ROLE_ADMIN')){
            /** @var Service $service */
            $service->setStatus('allowed');
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_index');
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/add/{id}", name="service_add")
     * @param Service $service
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function add(Service $service, Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $form = $this->createForm(ServiceAddFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER')) {
            /** @var Service $service */
            $this->getUser()->addService($service);
            $form->get('image')->setData('');
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service added to cart!');
            return $this->redirectToRoute('service_index');
        }else {
            $response = new Response();
            $count = 0;
            if(isset($_COOKIE['services'][0])) {
                $count = count($_COOKIE['services']);
            }
            $cookieServices = array(
                'name' => "services[$count]",
                'service' => $service->getId()
            );
            $cookie = new Cookie($cookieServices['name'],$cookieServices['service']);
            $response->headers->setCookie($cookie, '', 1);
            $response->send();

        }

        $service = $serviceRepository->findAll();
        $cart = [];
        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/cart", name="service_view")
     * @param Request $request
     * @param WorkerRepository $workerRepository
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function view(Request $request, WorkerRepository $workerRepository, ServiceRepository $serviceRepository)
    {
        $form = $this->createForm(ServiceAddFormType::class);
        $form->handleRequest($request);
        $service = null;
        if(!$this->isGranted('ROLE_USER') && isset($_COOKIE['services'])){
            foreach ($_COOKIE['services'] as $s){
                $service[] = $serviceRepository->findOneBy(['id' => $s]);
            }
        }elseif($this->isGranted('ROLE_USER')) {
            $service = $this->getUser()->getServices();
        }
            $workers = $workerRepository->findAll();
        return $this->render('service/cart.html.twig', [
            'form' => $form->createView(),
            'services' => $service,
            'workers' => $workers
        ]);
    }
    /**
     * @Route("/cart/{id}", name="service_buy")
     * @param Service $service
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param WorkerRepository $workerRepository
     * @return Response
     */
    public function buy(Service $service, Request $request, EntityManagerInterface $entityManager, WorkerRepository $workerRepository)
    {

        $form = $this->createForm(WorkerFormType::class);
        $form->handleRequest($request);
        /** @var Receipt $receipt */
        $receipt = new Receipt();

        if ($this->isGranted('ROLE_USER')) {

        }

        $service = $this->getUser()->getServices();

        return $this->render('service/cart.html.twig', [
            'form' => $form->createView(),
            'service' => $service
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
