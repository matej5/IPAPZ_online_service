<?php

namespace App\Controller;

use App\Entity\Office;
use App\Form\OfficeFormType;
use App\Repository\OfficeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfficeController extends AbstractController
{
    /**
     * @Route("/office", name="office_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param OfficeRepository $officeRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, OfficeRepository $officeRepository)
    {
        if(!$this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('post_index');
        }

        $form = $this->createForm(OfficeFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_BOSS') && $form->isSubmitted() && $form->isValid()) {
            /** @var Office $office */
            $office = $form->getData();
            $entityManager->persist($office);
            $entityManager->flush();
            $this->addFlash('success', 'New office created!');
            return $this->redirectToRoute('post_index');
        }
        $offices = $officeRepository->findAll();

        return $this->render('office/view.html.twig', [
            'form' => $form->createView(),
            'offices' => $offices
        ]);
    }
}
