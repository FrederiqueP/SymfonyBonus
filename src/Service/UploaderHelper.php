<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $slugger;
    private $postImageDirectory;
    private $filesystem;
    private $avatarDirectory;

    public function __construct(SluggerInterface $slugger, string $postImageDirectory, Filesystem $filesystem, string $avatarDirectory)
    {
        $this->slugger = $slugger;
        $this->postImageDirectory = $postImageDirectory;
        $this->filesystem = $filesystem;
        $this->avatarDirectory = $avatarDirectory;
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
    
    
    public function uploadUserAvatar(User $user, string $avatar)
    {
            $newFilename = 'avatar-' . uniqid() . '.svg';
            $user->setAvatar($newFilename);

            // le file_put_contents a besoin que le directory existe
            file_put_contents($this->avatarDirectory.'/'.$newFilename, $avatar); 
    }

}


