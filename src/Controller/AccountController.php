<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\ImgUpdateType;
use App\Entity\UserImgUpdate;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

final class AccountController extends AbstractController
{
    /**
     * Permet à l'utilisateur de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        $loginError = null;

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            $loginError = "Trop de tentatives de connexion, réessayer plus tard.";
        }

        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }

    /**
     * Permet à l'utilisateur de se déconnecter
     *
     * @return void
     */
    #[Route('/logout', name:'account_logout')]
    public function logout(): void
    {}
    
    /**
     * Permet à un utilisateur de s'inscrire et d'insérer l'utilisateur dans la bdd
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response|string
     */
    #[Route('/register', name: 'account_register')]
    public function register(Request $req, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response|string
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            //gestion image de profil
            $file = $form['picture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate("Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()", $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                "success",
                "Votre compte a bien été créé"
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render("account/registration.html.twig", [
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier les données de son profil d'utilisateurs (sans le mot de passe)
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/account/update', name: 'account_update')]
    #[IsGranted("ROLE_USER")]
    public function update(Request $req, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        /**
         * Récupère l'utilisateur connecté
         * @var User $user
         */
        $user = $this->getUser();
        //gestion de l'image
        $fileName = $user->getPicture();
        if(!empty($fileName))
        {
            $user->setPicture(new File($this->getParameter('uploads_directory')."/".$fileName));
        }
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPicture($fileName);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                "success",
                "Les données ont bien été modifiées"
            );
        }

        return $this->render("account/update.html.twig", [
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet à un utilisateur de modifier son mot de passe
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/account/password-update', name: 'account_password')]
    #[IsGranted("ROLE_USER")]
    public function updatePassword(Request $req, UserPasswordHasherInterface $hasher,EntityManagerInterface $manager): Response
    {
        /**
         * Récupère l'utilisateyr connecté
         * @var User $user
         */
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$hasher->isPasswordValid($user, $passwordUpdate->getOldPassword()))
            {
                $form->get('oldPassword')->addError(
                    new \Symfony\Component\Form\FormError("Le mot de passe actuel est incorrect")
                );
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render("account/password.html.twig", [
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet de modifier l'image de profil
     *
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response|string
     */
    #[Route('/account/img-update', name:'account_imgupdate')]
    #[IsGranted("ROLE_USER")]
    public function imgUpdate(Request $req, EntityManagerInterface $manager): Response|string
    {
        $imgUpdate = new UserImgUpdate();
        /**
         * Permet de récupérer l'utilisateur connecté
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(ImgUpdateType::class, $imgUpdate);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['newPicture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate("Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()", $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    //supprimer l'ancienne image dans le dossier
                    if(!empty($user->getPicture()))
                    {
                        unlink($this->getParameter('uploads_directory')."/".$user->getPicture());
                    }
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre avatar a bien été modifié"
            );
            return $this->redirectToRoute('account_index');
        }
        return $this->render("account/imgUpdate.html.twig", [
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer son image de profil
     *
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/account/delete-img', name: 'account_delimg')]
    #[IsGranted("ROLE_USER")]
    public function deleteImg(EntityManagerInterface $manager): Response
    {
        /**
         * Permet de récupérer l'utilisateur connecté
         * @var User $user
         */
        $user = $this->getUser();
        if(!empty($user->getPicture()))
        {
            unlink($this->getParameter('uploads_directory')."/".$user->getPicture());
            $user->setPicture(null);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre image a bien été supprimée"
            );
        }
        return $this->redirectToRoute('account_index');
    }
}
