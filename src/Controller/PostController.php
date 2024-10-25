<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post', requirements: ['id' => '\d+'])]
    public function index(int $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
        ]);
    }

    #[Route('/post', name: 'create_post', methods: ['POST'])]
    public function createPost(EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setTitle('title test');
        $post->setDescriptiom('description test');
        $post->setDate(new \DateTime('now'));
        $post->setPicture('images/blog-concept-with-man-holding.jpg');

        $category = $entityManager->getRepository(Category::class)->find(1);
        $post->setCategory($category);
        
        $entityManager->persist($post);
        $entityManager->flush();

        return new Response('Saved new post with id '.$post->getId());
    }

    #[Route('/newpost', name: 'newpost')]
    public function newPost(EntityManagerInterface $entityManager, Request $request): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post); 

        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $post->setDate(new \DateTime('now'));
            $post->setPicture('images/blog-concept-with-man-holding.jpg');

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success","Le post '".$post->getTitle()."' a bien été créé.");
            return $this->redirectToRoute('home');
        }
        
        return $this->render('post/newpost.html.twig', [
            'controller_name' => 'Créer un nouveau post',
            'categories' => $categories,
            'form' => $form,
        ]);
    }

    #[Route('/editpost/{id}', name: 'post_edit')]
    public function edit(PostRepository $postRepository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $post = $postRepository->find($id);
        
        if(!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid()) {

            $post->setTitle($form->get('title')->getData());
            $post->setDescriptiom($form->get('descriptiom')->getData());
            $post->setCategory($form->get('category')->getData());
            $entityManager->flush();

            $this->addFlash("success","Le post '".$id." ".$post->getTitle()."' a bien été modifié.");
            return $this->redirectToRoute('home');
        }

        $category = $postRepository->find($id);
        return $this->render('category/edit.html.twig', [
            'controller_name' => 'Modifier une catégorie',
            'id' => $id,
            'form' => $form,
        ]);
    }

    #[Route('/deletepost/{id}', name: 'post_delete')]
    public function delete(PostRepository $postRepository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $post = $postRepository->find($id);
        
        if(!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash("success","Le post '".$id." ".$post->getTitle()."' a bien été supprimé.");
        return $this->redirectToRoute('home');
    }
}
