<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;
use App\Service\AvatarFactory;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    public function __construct(
        private UploaderHelper $uploaderHelper
      ){}

    #[Route('/signup', name: 'user.signup')]
   public function signup(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
   {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();
    
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setHash($hashedPassword);

            // ajout avatar    
            $size = 5;
            $nbColor = 3;
            $avatar = AvatarFactory::new($size, $nbColor);
            
            $this->uploaderHelper->uploadUserAvatar($user, $avatar);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte est créé, connectez-vous.');

            return $this->redirectToRoute('security.login');

        }

       //    return $this->render('user/signup.html.twig');
       return $this->render('user/signup.html.twig', [
        'form' => $form->createView()
    ]);
 
 
   }

}
