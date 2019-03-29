<?php

namespace App\Controller;

use App\Form\OfficeFormType;
use App\Repository\OfficeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class OfficeController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/office", name="office_index")
     * @param            Request $request
     * @param            EntityManagerInterface $entityManager
     * @param            OfficeRepository $officeRepository
     * @return           \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, OfficeRepository $officeRepository)
    {
        if (!($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOSS')) ) {
            return $this->redirectToRoute('post_index');
        } else {
            $form = $this->createForm(OfficeFormType::class);
            $form->handleRequest($request);
            if (($this->isGranted('ROLE_ADMIN')
                    || $this->isGranted('ROLE_BOSS'))
                && ($form->isSubmitted() && $form->isValid())) {
                $office = $form->getData();
                $office->setOwner($this->getUser()->getWorker());
                $entityManager->persist($office);
                $entityManager->flush();
                $this->addFlash('success', 'New office created!');
                return $this->redirectToRoute('office_index');
            }

            $offices = $officeRepository->findAll();

            return $this->render(
                'office/view.html.twig',
                [
                    'form' => $form->createView(),
                    'offices' => $offices
                ]
            );
        }
    }
}
