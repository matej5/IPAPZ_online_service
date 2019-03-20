<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 7:27 AM
 */

namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
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

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="category_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('post_index');
        }
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);

        $data = $form->getExtraData();

        if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
            $category = new Category();
            $category->setName($form->get('name')->getData());
            $entityManager->persist($category);
            $entityManager->flush();

            foreach ($data as $f){
                $category = new Category();
                $category->setName($f['name']);
                $entityManager->persist($category);
                $entityManager->flush();
            }

            $this->addFlash('success', 'New category created!');
            return $this->redirectToRoute('category_index');
        }
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/categories/{id}", name="category_view")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function view(Category $category, Request $request, EntityManagerInterface $entityManager)
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('post_index');
        }
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
            $category->setName($form->get('name')->getData());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category edited!');
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/view.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    //todo navodno ne postoji ruta

    /**)
     * @Route("/category/{id}/delete", name="category_delete")
     * @param Category $category
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Category $category, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully deleted!');
        return $this->redirectToRoute('category_index');
    }
}