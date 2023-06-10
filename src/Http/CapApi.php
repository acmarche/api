<?php

namespace AcMarche\Api\Http;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CapApi
{
    use ConnectionTrait;

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @throws \Exception
     */
    public function find(int $id): string
    {
        $this->connect();

        $url = $this->base_uri.'/bottin/'.$id;

        return $this->executeRequest($url);
    }

    public function commercant(int $commercantId): string
    {
        $this->connect();

        $url = $this->base_uri.'/shop/'.$commercantId;

        return $this->executeRequest($url);
    }

    public function images(int $commercantId): string
    {
        $this->connect();

        $url = $this->base_uri.'/images/'.$commercantId;

        return $this->executeRequest($url);
    }


}