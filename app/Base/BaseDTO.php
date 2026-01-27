<?php

namespace App\Base;

abstract class BaseDTO
{
    /**
     * create DTO from request
     */
    abstract public static function fromRequest(array $request): static;

    /**
     * create DTO from Array
     */
    public static function fromArray(array $data): static
    {
        return new static(
            ...$data
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
