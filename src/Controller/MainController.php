<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController {

    #[Route("/", name: "home")]
    public function home(EntityManagerInterface $entityManager): Response {
        $posts = $entityManager->getRepository(Post::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'controller_name' => "Page d'accueil",
            'posts' => $posts,
        ]);
    }
}