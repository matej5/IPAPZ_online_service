<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ReceiptFormType;
use App\Form\ServiceEditFormType;
use App\Form\ServiceFormType;
use App\Repository\CategoryRepository;
use App\Repository\ReceiptRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface as PaginatorInterfaceAlias;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/service/{category}", name="service_index")
     * @param                        CategoryRepository $categoryRepository
     * @param                        Request $request
     * @param                        WorkerRepository $workerRepository
     * @param                        EntityManagerInterface $entityManager
     * @param                        ServiceRepository $serviceRepository
     * @param                        PaginatorInterfaceAlias $paginator
     * @param null $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        CategoryRepository $categoryRepository,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $entityManager,
        ServiceRepository $serviceRepository,
        PaginatorInterfaceAlias $paginator,
        $category = null
    ) {
        $categories = $categoryRepository->findAllASC();

        $form = $this->createForm(ServiceFormType::class, null, ['categories' => $categories]);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Service $service
             */
            $data = $form->getData();

            $service = new Service();
            if (!empty($form->get('image')->getData())) {
                $file = $form->get('image')->getData();

                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // moves the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('service_directory'),
                    $fileName
                );
            } else {
                $fileName = 'service.png';
            }

            $service->setName($data['name']);
            $service->setCost($data['cost']);
            $service->setDuration($data['duration']);
            $service->setDescription($data['description']);
            $service->setImage($fileName);
            $service->setStatus('queued');
            $service->setCatalog('inactive');
            foreach ($data['category'] as $c) {
                $service->addCategory($c);
            }

            $service->setBoss($workerRepository->findOneBy(['user' => $this->getUser()]));

            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service created!');
            return $this->redirectToRoute('service_index');
        }


        if (strtolower($request->get('category')) == 'queue' && $this->isGranted('ROLE_ADMIN')) {
            $service = $serviceRepository->findBy(['status' => 'queued']);
        } else if (strtolower($request->get('category')) == 'allowed' && $this->isGranted('ROLE_BOSS')) {
            $service = $serviceRepository->findBy(['status' => 'allowed', 'boss' => $this->getUser()->getWorker()]);
        } else {
            $service = $serviceRepository->findByService($category, ['catalog' => 'active']);
        }

        $services = $paginator->paginate(
            $service,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'service/service.html.twig',
            [
                'form' => $form->createView(),
                'categories' => $categories,
                'services' =>$services,
                'title' => $category
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/serviceView/{id}", name="service_view")
     * @param                      Service $service
     * @param                      CategoryRepository $categoryRepository
     * @param                      Request $request
     * @param                      EntityManagerInterface $entityManager
     * @return                     \Symfony\Component\HttpFoundation\Response
     */
    public function viewService(
        Service $service,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        if (!($this->isGranted('ROLE_BOSS') && $this->getUser() == $service->getBoss()->getUser())) {
            return $this->redirectToRoute('service_index');
        } else {
            $categories = $categoryRepository->findAllASC();

            $form = $this->createForm(ServiceFormType::class, ['categories' => $categories, 'service' => $service]);
            $form->handleRequest($request);
            if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
                /**
                 * @var Service $service
                 */
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

            $form = $this->createForm(ServiceEditFormType::class, $service);
            $form->handleRequest($request);
            if ($this->isGranted('ROLE_BOSS') &&
                $form->isSubmitted() && $form->isValid() &&
                $this->getUser() == $service->getBoss()->getUser()
            ) {
                /**
                 * @var Service $service
                 */
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

            return $this->render(
                'service/view.html.twig',
                [
                    'form' => $form->createView(),
                    'categories' => $categories,
                    'service' => $service,
                    'title' => 'Edit service'
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/allow/{id}", name="service_allow")
     * @param                Service $service
     * @param                EntityManagerInterface $entityManager
     * @return               \Symfony\Component\HttpFoundation\Response
     */
    public function allow(Service $service, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            /**
             * @var Service $service
             */
            $service->setStatus('allowed');
            $service->setCatalog('inactive');
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_index', ['category' => 'Queue']);
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/activate/{id}", name="service_activate")
     * @param                   Service $service
     * @param                   EntityManagerInterface $entityManager
     * @return                  \Symfony\Component\HttpFoundation\Response
     */
    public function activate(Service $service, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_BOSS') && $this->getUser()->getWorker() === $service->getBoss()) {
            /**
             * @var Service $service
             */
            if ($service->getCatalog() === 'inactive') {
                $service->setCatalog('active');
            } else {
                $service->setCatalog('inactive');
            }

            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_index', ['category' => 'Allowed']);
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/add/{id}", name="service_add")
     * @param              Service $service
     * @param              EntityManagerInterface $entityManager
     * @return             \Symfony\Component\HttpFoundation\Response
     */
    public function add(Service $service, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_USER')) {
            /**
             * @var Service $service
             */
            $this->getUser()->addService($service);
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'New service added to cart!');
            return $this->redirectToRoute('service_index');
        } else {
            $count = 0;
            if (isset($_COOKIE['services'][0])) {
                $count = count($_COOKIE['services']);
            }

            setcookie("services[$count]", $service->getId(), time()+86400, '/');
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/cart", name="cart_view")
     * @param          ServiceRepository $serviceRepository
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function view(ServiceRepository $serviceRepository)
    {
        $service = null;
        if (!$this->isGranted('ROLE_USER') && isset($_COOKIE['services'])) {
            foreach ($_COOKIE['services'] as $s) {
                $service[] = $serviceRepository->findOneBy(['id' => $s]);
            }
        } else if ($this->isGranted('ROLE_USER')) {
            $service = $this->getUser()->getServices();
        }

        return $this->render(
            'service/basket.html.twig',
            [
                'services' => $service
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/bought", name="bought_view")
     * @param          Request $request
     * @param          ReceiptRepository $receiptRepository
     * @param          PaginatorInterface $paginator
     * @return         \Symfony\Component\HttpFoundation\Response
     */
    public function bought(Request $request, ReceiptRepository $receiptRepository, PaginatorInterface $paginator)
    {
        $receipt = null;
        $service = null;
        if (!$this->isGranted('ROLE_USER') && isset($_COOKIE['Buy'])) {
            foreach ($_COOKIE['Buy'] as $r) {
                $receipt[] = $receiptRepository->findOneBy(['id' => $r]);
            }
        } else if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('post_index');
        }

        $pagination = $paginator->paginate(
            $receipt,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'receipt/index.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/cart/{id}", name="service_buy")
     * @param               Service $service
     * @param               Request $request
     * @return              \Symfony\Component\HttpFoundation\Response
     */
    public function buy(Service $service, Request $request)
    {
        $offices = $service->getBoss()->getOfficesCreated();

        $form = $this->createForm(ReceiptFormType::class, null, ['offices' => $offices]);
        $form->handleRequest($request);

        return $this->render(
            'service/cart.html.twig',
            [
                'form' => $form->createView(),
                'service' => $service
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/events/{id}", name="events")
     * @param                 ReceiptRepository $receiptRepository
     * @param                 null $id
     * @return                \Symfony\Component\HttpFoundation\Response
     * @throws                \Exception
     */
    public function events(
        ReceiptRepository $receiptRepository,
        $id = null
    ) {
        $data = [];
        $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));
        $receipts = $receiptRepository->findInTwoWeeks($id, $date);
        foreach ($receipts as $receipt) {
            $dateTime = new DateTime($receipt->getStartOfService()->format('Y-m-d H:i'));
            $minutesToAdd = $receipt->getService()->getDuration();
            $data[] = [
                'title' => $receipt->getService()->getName(),
                'start' => $dateTime->format('Y-m-d H:i:s'),
                'end' => (clone $dateTime)->add(new DateInterval('PT' . $minutesToAdd . 'M'))->format('Y-m-d H:i:s')
            ];
        }

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Security("user.getWorker() == service.getBoss()")
     * @Symfony\Component\Routing\Annotation\Route("/service/{id}/delete", name="service_delete")
     * @param                         Service $service
     * @param                         EntityManagerInterface $entityManager
     * @return                        \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteService(Service $service, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()->getWorker() == $service->getBoss() &&
            ($this->isGranted('ROLE_BOSS') ||
                $this->isGranted('ROLE_ADMIN'))
        ) {
            $service->setStatus('removed');
            $entityManager->flush();
            $this->addFlash('success', 'Successfully deleted!');
        }

        return $this->redirectToRoute('service_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
