<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'category_post_list', methods:['GET'])]
    public function index(int $id, CategoryRepository $categoryRepository, PostRepository $postRepository): Response
    {   
        $category = $categoryRepository->find($id);

        $posts = $postRepository->findBy(
            ['category' => $category],
        );

        return $this->render('category/index.html.twig', [
            'controller_name' => 'Liste des posts',
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    #[Route('/newcategory', name: 'newcategory')]
    public function newCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category); 

        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid()) {
            $request = $form->getData();
            
            $entityManager->persist($request);
            $entityManager->flush();

            $this->addFlash("success","La catégorie '".$request->getName()."' a bien été ajouté.");
            return $this->redirectToRoute('category_list');
        }
        
        return $this->render('category/newcategory.html.twig', [
            'controller_name' => 'Créer une nouvelle catégorie',
            'form' => $form,
        ]);
    }

    #[Route('/categories', name: 'category_list', methods:['GET'])]
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/list.html.twig', [
            'controller_name' => 'Liste des catégories',
            'categories' => $categories,
        ]);
    }

    #[Route('/editcategory/{id}', name: 'category_edit')]
    public function edit(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $category = $categoryRepository->find($id);
        
        if(!$category) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $form = $this->createForm(CategoryFormType::class, $category);
        

        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid()) {

            $category->setName($form->get('name')->getData());
            $entityManager->flush();

            $this->addFlash("success","La catégorie ".$id." '".$category->getName()."' a bien été modifié.");
            return $this->redirectToRoute('category_list');
        }

        $category = $categoryRepository->find($id);
        return $this->render('category/edit.html.twig', [
            'controller_name' => 'Modifier une catégorie',
            'id' => $id,
            'form' => $form,
        ]);
    }

    #[Route('/deletecategory/{id}', name: 'category_delete')]
    public function delete(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $category = $categoryRepository->find($id);
        if(!$category) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash("success","La catégorie '".$id." ".$category->getName()."' a bien été supprimé.");
        return $this->redirectToRoute('category_list');
    }
}
