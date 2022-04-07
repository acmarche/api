<?php


namespace AcMarche\Api\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;

trait NomTrait
{
    #[ORM\Column(type: 'string', length: 150)]
    private string $nom;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}
