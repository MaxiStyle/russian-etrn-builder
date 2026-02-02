<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Базовый класс, содержащий общие методы
 */
abstract class BaseEntity
{
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException("Property {$name} does not exist");
        }
        return $this->$name;
    }

    public function __set(string $name, mixed $value): void
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException("Property {$name} does not exist");
        }
        $this->$name = $value;
    }
}
