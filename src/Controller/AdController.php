<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AddAutoType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
        $all = $repo->findBy([], ['year' => 'ASC']);

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'AdController',
            'all' => $all,
        ]);
    }

    #[Route('/all/add', name: 'add_auto')]
    public function create(Request $req, EntityManagerInterface $manager) : Response
    {
        $auto = new Ad();

        $image1 = new Image();

        $image1->setUrl("https://picsum.photos/400/200")
            ->setCaption("Titre de l'image");

        $auto->addImage($image1);

        $form = $this->createForm(AddAutoType::class, $auto);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($auto);
            $manager->flush();

            $this->addFlash('success', "Le véhicule <strong>".$auto->getModele()."</strong> a bien été enregistré.");

            return $this->redirectToRoute('chosen_auto', ['slug' => $auto->getSlug()]);
        }

        return $this->render('ad/add.html.twig', [
            'myForm' => $form->createView() 
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