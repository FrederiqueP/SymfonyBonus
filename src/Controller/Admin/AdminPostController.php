<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPostController extends AbstractController
{

    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $manager,
        private UploaderHelper $uploaderHelper
      ){}
      
   #[Route('/admin/post/new', name: 'admin.post.new')]
   public function new(Request $request): Response
   {
    $post = new Post();
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $post->setUser($this->getUser());
        $post->setSlug($this->slugger->slug($post->getTitle()));
        // $uploadedFile = $form->get('imageFile')->getData();

        // if ($uploadedFile) {
        //     $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        //     $newFilename = $slugger->slug($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
        //     $newFilename = $this->slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        //     $post->setImage($newFilename);
        //     $uploadedFile->move('upload/post/image', $newFilename);

        //     $manager->persist($post);
        //     $manager->flush();

        //     $this->addFlash('success', 'Article ajouté avec succès.');

        //     return $this->redirectToRoute('admin.index');           
        // }

        $this->uploaderHelper->uploadPostImage($post, $form->get('imageFile')->getData());
        
        // On persiste en BDD
        $this->manager->persist($post);
        $this->manager->flush();

    }
    
    return $this->render('admin/post/new.html.twig', [
        'form' => $form->createView()
    ]);
 
   }

   #[Route('/admin/post/{id}/edit', name: 'admin.post.edit')]
   public function edit(
        Post $post,
        Request $request
        ): Response
   {
   
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $post->setSlug($this->slugger->slug($post->getTitle()));

        $this->uploaderHelper->uploadPostImage($post, $form->get('imageFile')->getData());
        
        // On persiste en BDD
        $this->manager->flush();

    }
    
    return $this->render('admin/post/edit.html.twig', [
        'form' => $form->createView()
    ]);
 
   }

   #[Route('/admin/post/{id}/removeImage', name: 'admin.post.removeImage')]
   public function removeImage(Post $post)
   {
      // Suppression du fichier image
      $this->uploaderHelper->removePostImageFile($post);
   
      // Vider le champ image de l'entité
      $post->setImage(null);
   
      // Mise à jour de l'entité en base de données
      $this->manager->flush();

      // Message flash
      $this->addFlash('success', 'Image supprimée.');
   
      // Redirection vers le dashboard admin
      return $this->redirectToRoute('admin.index');

   }

   #[Route('/admin/post/{id<\d+>}/remove/{token}', name: 'admin.post.remove')]
   public function remove(string $token, Post $post)
   {
      if (!$this->isCsrfTokenValid('delete-post-' . $post->getId(), $token)) {
        throw new \Exception('Invalid token');
      }
     
      // Suppression du fichier image
      $this->uploaderHelper->removePostImageFile($post);
   
      // Suppression de l'entité
      $this->manager->remove($post);
      $this->manager->flush();
   
      // Message flash
      $this->addFlash('success', 'Article supprimé.');
   
      // Redirection vers le dashboard admin
      return $this->redirectToRoute('admin.index');
   }
   
   

}    


