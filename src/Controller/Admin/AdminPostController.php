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

        // Redimensionner la taille de l'image à une largeur de 750 pixels
        // Utilisation des fonctions de la librairie GD pour créer des images
        
        $imagepath = "upload/post/image/". $post->getImage();
        //Ceci est le nouveau fichier que vous enregistrez , plutot écrasé
        $save = "upload/post/image/". $post->getImage(); 
        $info = getimagesize("upload/post/image/". $post->getImage());
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                break;
            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                break;
            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                break;
            default: 
                throw new Exception('Unknown image type.');
        }
                
        // list($width, $height) = getimagesize("upload/post/image/". $post->getImage());
        // $modwidth = 700;  //target width
        // $diff = $width / $modwidth;
        // $modheight = $height / $diff;
        // $tn = imagecreatetruecolor($modwidth, $modheight) ;
        // $image = $image_create_func("upload/post/image/". $post->getImage()) ;
        // imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
        // $image_save_func($tn, $save) ;



        $image = $image_create_func("upload/post/image/". $post->getImage()) ;
        list($width, $height) = getimagesize("upload/post/image/". $post->getImage());
        // $tn = imagecreatetruecolor(700, 300) ;
        // $image2 = imagecrop($image, ['x' => 0, 'y' => 0 , 'width' => '700px', 'height' => '300px']);
        // imagecropauto($tn , IMG_CROP_BLACK);

        $max_width = 750;
        $max_height = 300;
        $new_width = 0;
        $new_height = 0;
        $dst_x = 0;
        $dst_y = 0;
        
        // dd($max_width . ' w ' .$width . ' ;' .$max_height . ' h ' .$height);
        

        // if($width >= $height ) {
        //     if($max_width >= $width):
        //         return 'no_need_to_resize';
        //     endif;
            
        //     if($width = $height){
        //         $new_width = $max_height;
        //         $new_height = $max_height;
        //     } else {
        
        //         $new_width = $max_width;
        //         $reduction = ( ($new_width * 100) / $width );
        //         $new_height = round(( ($height * $reduction )/100 ),0);

        //     }
            
        // } else {
        //     if($max_height >= $height):
        //         return 'no_need_to_resize';
        //     endif;
    
        //     $reduction = ( ($new_height * 100) / $height );
        //     $new_width = round(( ($width * $reduction )/100 ),0);

        // }

        if($width >= $height ) {
            // en paysage
            $echelle = $max_width / $width; // exemple : 0.39
            // si la hauteur par echelle est superieure à la hauteur max on diminue sur echelle hauteur
            if( ($height * $echelle) > $max_height) {
                $echelle = $max_height / $height;
            }
         }
        else {
            $echelle = $max_height / $height;
            if( ($width * $echelle) > $max_width) {
                $echelle = $max_width / $width;
            }
        }

        $new_width = round(($width * $echelle), 0);
        $new_height = round(($height * $echelle), 0);
        
        if (($new_width != 0) && ($new_height != 0)) {
            $tn = imagecreatetruecolor($new_width, $new_height) ;

            imagecopyresampled($tn, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height) ;
            $image_save_func($tn, $save);
        }

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


