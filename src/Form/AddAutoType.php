<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Marque;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddAutoType extends AbstractType
{
    /**
     * Facilite la création de formulaire
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfig(string $label, string $placeholder, array $options = []): array
    {
        return array_merge_recursive(
            [
                'label' => $label,
                'attr' => [
                    'placeholder' => $placeholder
                ]
            ], $options
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele', TextType::class, $this->getConfig('Modele', 'Modèle du véhicule'))
            ->add('cover', UrlType::class, $this->getConfig('Image de couverture', "Donnez l'addresse Url de l'image"))
            ->add('km', IntegerType::class, $this->getConfig('Nombre de kilomètres', 'Entrez le nombre de kilomètres parcouru par le véhicule'))
            ->add('price', MoneyType::class, $this->getConfig('Prix du véhicule', 'Entrez le prix du véhicule'))
            ->add('nbOwner', IntegerType::class, $this->getConfig('Nombre de propriétaires', "Entrez le nombre de propriétaires"))
            ->add('cylindree', IntegerType::class, $this->getConfig('Cylindrée du moteur', "Entrez la cylindrée du moteur du véhicule (cm³)"))
            ->add('power', IntegerType::class, $this->getConfig('Puissance du moteur', "Entrez la puissance du moteur du véhicule (kW)"))
            ->add('carbu', ChoiceType::class, [
                'choices' => [
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Électrique' => 'électrique'
                ],
                'label' => 'Type de carburant',
                'attr' => [
                    'placeholder' => 'Choisissez le type de carburant du véhicule'
                ]
            ])
            ->add('year', DateType::class, [
                'label' => 'Année de mise en circulation',
                'widget' => 'choice',
                'years' => range(date('Y'), 1900), // Vous pouvez inverser l'ordre si vous voulez l'année la plus récente en premier
                'months' => [01], // Masque le champ de sélection du mois
                'days' => [01],   // Masque le champ de sélection du jour
            ])
            ->add('transmission', ChoiceType::class, [
                'choices' => [
                    'Manuelle' => 'manuelle',
                    'Automatique' => 'automatique',
                ],
                'label' => 'Type de transmission',
                'attr' => [
                    'placeholder' => 'Choisissez le type de transmission du véhicule'
                ]
            ])
            ->add('descri', TextareaType::class, $this->getConfig('Description du véhicule', "Décrivez le véhicule"))
            ->add('opt', TextareaType::class, $this->getConfig('Options du véhicule', "Donnez les option du véhicule"))
            // ->add('slug', TextType::class, $this->getConfig('slug', 'Adresse Web (automatique)',[
            //     'required' => false
            // ]))
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'name',
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' =>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
