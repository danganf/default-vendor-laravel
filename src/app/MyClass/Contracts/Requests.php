<?php

namespace IntercaseDefault\MyClass\Contracts;

use IntercaseDefault\MyClass\Curl;

abstract class Requests {

    protected $curl, $pathUrl, $table;
    private $header = [], $returnHeader = null;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
        $this->initHeader();
    }

    public function setPathUrl($url){
        $this->pathUrl = $url . '/' . $this->table;
    }

    public function setTable($table){
        $this->table = $table;
        return $this;
    }

    protected function setHeaderDF($apiKey=null){

        $apiKey = empty( $apiKey ) ? config('app.api_key') : $apiKey;

        $this->setHeader('X-DreamFactory-API-Key', $apiKey);
    }

    public function send($host,$options){

        $return = $this->curl->send($host, $options);//dd($return);
        $result = null;
        if (is_array($return)) {
            $result = json_decode( $return['RESULT'], true );
            $error = false;
            if( is_array( $result ) ) {
                if ( key_exists('error', $result) ) {
                    $error = true;
                } else if ( array_has( $result, 'resource' ) ) {
                    $result = current($result);
                }

            } else if( empty( $result ) ){
                $result = $return['RESULT'];
                $error  = true;
            }

            if($error){
                \LogDebug::error($return['RESULT']);
            } else if ( array_get( $return, 'HEADER_RETURN' ) ){
                $this->returnHeader = $return['HEADER_RETURN'];
            }
        }

        return $result;

    }

    public function getHeaderReturn(){
        $return = $this->returnHeader;
        $this->returnHeader = null;
        return $return;
    }

    private function initHeader(){
        /*$sessionValues = SessionOpen('get');
        $tokenLabel    = array_get( $sessionValues, 'token_label' );
        $tokenValue    = array_get( $sessionValues, 'token_value' );
        if( !empty( $tokenLabel ) && !empty( $tokenValue ) ){
            $this->setHeader($tokenLabel, $tokenValue);
        }*/
    }

    public function getHeader(){
        return $this->header;
    }

    public function setHeader($label, $value){

        foreach ( $this->header AS $key => $valueRow ){
            if( strpos( $valueRow, $label ) !== false ){
                unset( $this->header[$key] );
            }
        }

        $this->header[] = "$label: $value";
    }

}