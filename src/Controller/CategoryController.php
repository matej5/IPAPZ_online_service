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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/categories", name="category_index")
     * @param                Request $request
     * @param                EntityManagerInterface $entityManager
     * @param                CategoryRepository $categoryRepository
     * @return               \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ) {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        } else {
            $categories = $categoryRepository->findAll();

            return $this->render(
                'category/index.html.twig',
                [
                    'categories' => $categories,
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/addCategory", name="category_add")
     * @param                Request $request
     * @param                EntityManagerInterface $entityManager
     * @param                CategoryRepository $categoryRepository
     * @return               \Symfony\Component\HttpFoundation\Response
     */
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ) {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        } else {
            $form = $this->createForm(CategoryFormType::class);
            $form->handleRequest($request);

            $data = $form->getExtraData();

            if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
                $category = new Category();
                $category->setName($form->get('name')->getData());
                $entityManager->persist($category);
                $entityManager->flush();

                foreach ($data as $f) {
                    $category = new Category();
                    $category->setName($f['name']);
                    $entityManager->persist($category);
                    $entityManager->flush();
                }

                $this->addFlash('success', 'New category created!');
                return $this->redirectToRoute('category_index');
            }

            return $this->render(
                'category/create.html.twig',
                [
                    'form' => $form->createView()
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/category/{id}", name="category_view")
     * @param                     Category $category
     * @param                     Request $request
     * @param                     EntityManagerInterface $entityManager
     * @return                      \Symfony\Component\HttpFoundation\Response
     */
    public function view(Category $category, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        } else {
            $form = $this->createForm(CategoryFormType::class, $category);
            $form->handleRequest($request);

            if ($this->isGranted('ROLE_ADMIN') && $form->isSubmitted() && $form->isValid()) {
                $category->setName($form->get('name')->getData());
                $entityManager->persist($category);
                $entityManager->flush();

                $this->addFlash('success', 'Category edited!');
                return $this->redirectToRoute('category_index');
            }

            return $this->render(
                'category/view.html.twig',
                [
                    'form' => $form->createView(),
                    'category' => $category,
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/category/{id}/delete", name="category_delete")
     * @param                          Category $category
     * @param                          EntityManagerInterface $entityManager
     * @return                         \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Category $category, EntityManagerInterface $entityManager)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('post_index');
        } else {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully deleted!');
            return $this->redirectToRoute('category_index');
        }
    }
}
