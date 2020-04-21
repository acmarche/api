<?php


namespace AcMarche\Api\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;


trait EmailTrait
{
    /**
     * @var string|null
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

}
