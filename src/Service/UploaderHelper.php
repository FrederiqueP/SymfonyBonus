<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $slugger;
    private $postImageDirectory;
    private $filesystem;

    public function __construct(SluggerInterface $slugger, string $postImageDirectory, Filesystem $filesystem)
    {
        $this->slugger = $slugger;
        $this->postImageDirectory = $postImageDirectory;
        $this->filesystem = $filesystem;
    }

         
    public function uploadPostImage($post, ?UploadedFile $uploadedFile)
    {
        if ($uploadedFile) {

            // Suppression de l'image actuelle le cas échéant
            $this->removePostImageFile($post);

        
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $this->slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        
            $post->setImage($newFilename);
        
            $uploadedFile->move($this->postImageDirectory, $newFilename);
          }
        
    }

    public function removePostImageFile($post)
    {
        if ($currentFilename = $post->getImage()) {
            $currentPath = $this->postImageDirectory . '/' . $currentFilename;
            if ($this->filesystem->exists($currentPath)) {
                $this->filesystem->remove($currentPath);
            }
        }
    }   


}


