<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class UserImgUpdate
{
    #[Assert\NotBlank(message:"Veuillez ajouter uneimage")]
    #[Assert\File(maxSize:'2M', mimeTypes:['image/jpeg', 'image/png', 'image/jpg', 'image/gif'], mimeTypesMessage:"Vous devez upload un fichier jpg, png ou gif", maxSizeMessage:"La taille du fichier est trop grande")]
    private ?string $newPicture = null;

    public function getNewPicture(): ?string
    {
        return $this->newPicture;
    }

    public function setNewPicture(string $newPicture): static
    {
        $this->newPicture = $newPicture;

        return $this;
    }
}
