<?php

namespace AcMarche\Api\Parking;

class Location
{
    public readonly string $type;
    public readonly array $value;

    public function __construct($data)
    {
        $this->type = $data['type'];
        $this->value = $data['value'];
    }
}