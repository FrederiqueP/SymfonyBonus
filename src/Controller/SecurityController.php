<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory
       ){}
       
    #[Route(path: '/login', name: 'security.login')]
    public function login(AuthenticationUtils $utils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // 
        // last email entered by the user
        $lastEmail = $utils->getLastUsername();
        // $form = $this->formFactory->createNamed('', LoginType::class, ['email' => $lastEmail]);
        $form = $this->formFactory->createNamed('', LoginType::class);
        // get the login error if there is one
        $error = $utils->getLastAuthenticationError();

        // return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $error
        ]);

    }

    #[Route(path: '/logout', name: 'security.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
