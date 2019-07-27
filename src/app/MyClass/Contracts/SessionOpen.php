<?php

namespace Danganf\MyClass\Contracts;

use Illuminate\Support\Facades\Session;

abstract class SessionOpen
{
    private $nameSession='userData';

    public function __construct()
    {

    }

    public function setTag($nome=null){
        if( !empty( $nome ) ) {
            $this->nameSession = $nome;
        }

        return $this;
    }

    public function create( $dados ){

        $dadosSession = $this->get();
        if( empty( $dadosSession ) ){$dadosSession = [];}

        if( is_array( $dados ) ){
            foreach ( $dados AS $label => $row ){
                $dadosSession[ $label ] = $row;
            }
        } else {
            $dadosSession = $dados;
        }

        Session::put( $this->nameSession, $dadosSession );
    }

    public function has(){
        return ( !empty( $this->getTag() ) ? TRUE : FALSE );
    }

    function get( $campo = null ){
        $dados = \Session::get( $this->nameSession );
        return ( empty( $campo ) ? $dados : array_get( $dados, $campo, null ) );
    }

    function getTag( $tag=null ){

        $tag  = ( empty( $tag ) ? $this->nameSession : $tag );
        $dados = Session::get( $tag );
        return ( !empty( $dados ) ? $dados : null );
    }

    function getSessionName(){return $this->nameSession;}

    function forget( $tag=null ){
        $tag = ( empty( $tag ) ? $this->nameSession : $tag );
        Session::forget( $tag );
        return $this;
    }

}
