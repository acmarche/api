<?php


namespace AcMarche\Api;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarcheApiBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
