<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(AdRepository $repo): Response
    {
        $autos = $repo->findBy(
            [],
            ['id' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'autos' => $autos
        ]);
    }
}
