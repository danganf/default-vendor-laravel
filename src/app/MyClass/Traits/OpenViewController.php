<?php

namespace Danganf\MyClass\Traits;

trait OpenViewController
{
    protected function openView( $subTitle, $dados = [], $view = 'criar' ){

        $dados['showBreadCrumbs'] = ['title'=>$this->title,'bread'=>$this->title.'|'.$subTitle];

        if( isset( $dados['routeCreate'] ) ){$dados['showBreadCrumbs']['routeCreate'] = $dados['routeCreate'];}

        return view("pages.".$this->pathView.".$view", $dados);

    }
}