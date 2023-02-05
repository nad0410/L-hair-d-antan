<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError(); // On récupére la dernière error
        $lastUsername = $authenticationUtils->getLastUsername(); // On récupère le dernier Username

        return $this->render('login/index.html.twig', [
            "lastUsername" => $lastUsername,
            "error" => $error
        ]);
    }
}
