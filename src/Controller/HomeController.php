<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    // public function index(PostRepository $postRepository): Response
    // {
    // $posts = $postRepository->findAll();

    // // dd($posts);

    //     return $this->render('home/index.html.twig', [
    //     'posts' => $posts
    //     ]);
            
    // }

    // les plus récents en premier , findBy
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }

}
