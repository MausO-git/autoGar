<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    /**
     * Permet d'afficher la page d'un utilisateur
     * @param User $user
     * @return Responde
     */
    #[Route('/user/{slug}', name: 'show_user')]
    public function index(User $user): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Permet d'afficher la page de son profil d'utilisateur
     *
     * @return Response
     */
    #[Route('/account', name:'account_index')]
    public function myAccount():Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }


}
