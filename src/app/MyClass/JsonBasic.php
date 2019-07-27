<?php

namespace Danganf\MyClass;

use Danganf\MyClass\Json\Contracts\JsonAbstract;
use Danganf\MyClass\Json\Contracts\JsonInterface;

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
