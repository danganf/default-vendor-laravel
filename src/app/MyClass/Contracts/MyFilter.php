<?php

namespace Danganf;

abstract class MyFilter
{
    private $session;
    private $prefxTagName = 'myFilter';

    function __construct( SessionOpen $sessionOpen )
    {
        $this->session = $sessionOpen;
        $this->session->setTag( $this->prefxTagName );
    }

    function setOrigem( $origem ){
        $this->prefxTagName = $this->session->getSessionName().ucfirst( camel_case( strtolower( $origem ) ) );
        $this->session->setTag( $this->prefxTagName );
        return $this;
    }

    public function set( $label, $value ){
        $this->session->create( [ $label => $value ] );
    }

    public function forget( $label ){

        $filter  = $this->getFilter();
        $ttAntes = count( $filter );
        array_forget( $filter, $label );
        $ttDepois = count( $filter );

        if( $ttAntes != $ttDepois ) {

            $this->destroy();

            if ( !empty($filter) ) {
                $this->session->create($filter);
            }

        }

    }

    public function destroy(){
        $this->session->forget( $this->prefxTagName );
    }

    public function getFilter(){
        $filter = $this->session->getTag( $this->prefxTagName );
        return ( !empty( $filter ) ? $filter : [] );
    }

    public function montaWhereSearch( $rulesArray ){

        $where = '';
        if( method_exists( $this, 'getFilter' ) ){

            foreach ( $this->getFilter() AS $campo => $value ){

                if( $value ){

                    $flag = null;dd($rulesArray);
                    switch ( $campo ){
                        case 'nome'            : $flag = "nome LIKE '%$value%'"; break;
                        case 'titulo'          : $flag = "titulo LIKE '%$value%'"; break;
                        case 'uf'              : $flag = "uf = '$value'"; break;
                        case 'status'          : $flag = "status = '$value'"; break;
                        case 'vigencia_pacote' : $flag = "vigencia_out < DATE( NOW() )"; break;
                    }

                    if( empty( $flag ) && strpos( $campo, '_id' ) !== FALSE ){$flag = "$campo = '$value'";}

                    if( !empty( $flag ) ) {
                        $this->trataWhere($where);
                        $where .= $flag;
                    }

                }

            }
        }

        return $where;

    }

    protected function trataWhere( &$where ){
        $where = ( $where != '' ? $where . ' AND ' : $where );
    }

}