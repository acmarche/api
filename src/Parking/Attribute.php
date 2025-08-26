<?php

namespace AcMarche\Api\Parking;

class Attribute
{
    public readonly string $type;
    public readonly null|array|string $value;
    public readonly array $metadata;

    public function __construct($data)
    {
        $this->type = $data['type'];
        $this->value = $data['value'];
        $this->metadata = $data['metadata'];
    }
}