<?php


namespace AcMarche\Api\Entity\Traits;


trait NomTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}
