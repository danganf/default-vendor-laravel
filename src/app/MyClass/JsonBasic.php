<?php

namespace IntercaseDefault\MyClass;

use IntercaseDefault\MyClass\Json\Contracts\JsonAbstract;
use IntercaseDefault\MyClass\Json\Contracts\JsonInterface;

class JsonBasic extends JsonAbstract implements JsonInterface
{
    public function set( $stringJson ) {

        $this->setReturnPadrao();
        $this->setJson( json_decode( $stringJson ) );
        $this->trataDados();

    }

    private function trataDados() {
        //
    }

    public function validRequiredFields( $array ) {
        return TRUE;
    }
}