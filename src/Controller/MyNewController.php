<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyNewController extends AbstractController
{
    #[Route('/my/new', name: 'app_my_new')]
    public function index(): Response
    {
        return $this->render('my_new/index.html.twig', [
            'controller_name' => 'MyNewController',
        ]);
    }
}
