<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories")
 */
class CategoryAdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_category_index", methods="GET")
     */
    public function indexAction(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', ['categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/add", name="admin_category_add", methods="GET|POST")
     */
    public function addAction(Request $request): Response
    {
        $category = new Category();

        return $this->getEditForm($request, $category);
    }

    /**
     * @Route("/{id}", name="admin_category_view", methods="GET")
     */
    public function viewAction(Category $category): Response
    {
        return $this->render('admin/category/view.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Category $category): Response
    {
        return $this->getEditForm($request, $category);
    }

    protected function getEditForm(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
}
