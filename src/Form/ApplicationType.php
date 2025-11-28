<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
     /**
     * Facilite la création de formulaire
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    protected function getConfig(string $label, string $placeholder, array $options = []): array
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
}