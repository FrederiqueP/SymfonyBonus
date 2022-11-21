<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    // #[Route('/post', name: 'app_post')]
    // #[Route('/post/{id<\d+>}', name: 'post.index')]
    // public function index(): Response
    // {
    //     return $this->render('post/index.html.twig', [
    //         'controller_name' => 'PostController',
    //     ]);
    // }
    // public function index(int $id): Response
    // {
    //     dd($id);
    // }
    // public function index(int $id, PostRepository $postRepository): Response
    // {
    //     dd($postRepository->find($id));
    // }

    // framework extra bundle 
    //#[Route('/post/{id<\d+>}', name: 'post.index')]
    #[Route('/post/{slug}', name: 'post.index')]
    public function index(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            $user = $this->getUser();
            if (!$user) {
                // si pas de user on retourne à la page de login
                return $this->redirectToRoute('security.login');
            }
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setUser($user);
            // $comment->setCreatedAt(new DateTimeImmutable());
            $manager->persist($comment);
            $manager->flush();
        
            $this->addFlash('success', 'Votre commentaire est créé.');

            return $this->redirect($request->headers->get('referer'));
       }
       
    

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

}
