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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    /**
     * Permet à l'admin d'ajouter un véhicule à la bdd
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/all/add', name: 'add_auto')]
    #[IsGranted("ROLE_ADMIN")]
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

    /**
     * Permet à l'admin d'éditer un véhicule
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param Ad $auto
     * @return Response
     */
    #[Route('/all/{slug}/edit', name:'auto_edit')]
    #[IsGranted("ROLE_ADMIN")]
    public function editAuto(Request $req, EntityManagerInterface $manager, Ad $auto): Response
    {
        $form = $this->createForm(AddAutoType::class, $auto);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($auto->getImages() as $img)
            {
                $img->setAd($auto);
                $manager->persist($img);
            }

            $manager->persist($auto);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'auto <strong>".$auto->getModele()."</strong> a bien été modifiée"
            );

            return $this->redirectToRoute("chosen_auto",[
                'slug' => $auto->getSlug()
            ]);
        }

        return $this->render("ad/edit.html.twig",[
            'auto' => $auto,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un véhicule de la bdd
     * @param Ad $auto
     * @param EntityManagerInterface $manager
     * @return @Response
     */
    #[Route('/all/{slug}/delete', name:"auto_delete")]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteAuto(Ad $auto, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            'success',
            "La voiture <strong>".$auto->getModele()."</strong> a bien été retirée"
        );
        $manager->remove($auto);
        $manager->flush();
        return $this->redirectToRoute('all_auto');
    }

    /**
     * Permet d'afficher le véhicule choisi
     * @param Ad $auto
     * @return Response
     */
    #[Route('/all/{slug}', name: 'chosen_auto')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Ad $auto
    ): Response
    {
        return $this->render("ad/show.html.twig",[
            "auto" => $auto,
        ]);
    }
}