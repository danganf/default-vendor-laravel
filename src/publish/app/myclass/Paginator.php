<?php

namespace App\MyClass;

class Paginator extends \Danganf\MyClass\Contracts\Paginator
{
    public function __construct($perPage=25){
        parent::__construct($perPage);
    }
}
