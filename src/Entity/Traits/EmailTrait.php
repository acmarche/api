<?php


namespace AcMarche\Api\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


trait EmailTrait
{
    #[Assert\Email]
    #[ORM\Column(name: 'email', type: 'string', length: 50, unique: true)]
    private string $email;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

}
