<?php

namespace App\Data;

class Person
{
    public string $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}