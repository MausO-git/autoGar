<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfig('Prénom', "Votre prénom..."))
            ->add('lastName', TextType::class, $this->getConfig('Nom', "Votre nom de famille..."))
            ->add('email', EmailType::class, $this->getConfig('Email', "Votre adresse e-mail..."))
            ->add('password', PasswordType::class, $this->getConfig('Mot de passe', "Votre mot de passe..."))
            ->add('passwordConfirm', PasswordType::class, $this->getConfig('Confirmation du mot de passe', "Confirmez votre mot de passe..."))
            ->add('picture', FileType::class, [
                "label" => "Photo de profil (jpg,png,gif)",
                "required" => false
            ])
            ->add('introduction', TextType::class, $this->getConfig('Introduction', "Veuillez vous présenter brièvement"))
            ->add('description', TextareaType::class, $this->getConfig('Description détaillée', "Veuillez vous décrire en déatils"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
