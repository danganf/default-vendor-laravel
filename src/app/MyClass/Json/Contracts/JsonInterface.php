<?php

namespace App\MyClass\Json\Contracts;

interface JsonInterface
{
    public function set($stringJson);
    public function validar($array);
}