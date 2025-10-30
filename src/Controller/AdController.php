<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdController extends AbstractController
{
    /**
     * Affiche toutes les autos
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/all', name: 'all_auto')]
    public function index(AdRepository $repo): Response
    {
        $all = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'AdController',
            'all' => $all,
        ]);
    }

    #[Route('/all/{slug}', name: 'chosen_auto')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Ad $auto
    ): Response
    {
        return $this->render("ad/show.html.twig",[
            "auto" => $auto
        ]);
    }
}
