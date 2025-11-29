<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    /**
     * Affiche les 3 véhicules les plus récent sue la page d'acceuil
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(AdRepository $repo): Response
    {
        $autos = $repo->findBy(
            [],
            ['year' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'autos' => $autos
        ]);
    }
}
