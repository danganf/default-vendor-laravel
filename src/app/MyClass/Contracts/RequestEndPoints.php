<?php

namespace IntercaseDefault\MyClass\Contracts;

use App\MyClass\Json\JsonBasic;
use IntercaseDefault\Facades\ThrowNewExceptionFacades;
use IntercaseDefault\MyClass\AbstractDefaultCache;
use IntercaseDefault\MyClass\Curl;
use Validator;

abstract class RequestEndPoints
{
    private $host, $curl, $header, $method, $timeout, $objJson, $serviceName, $cache, $bodyJson=false, $bodyPost, $msgError;

    const RESPONSE_INVALID_ARQ = 'InvalidArgument';

    function __construct(Curl $curl, JsonBasic $jsonBasic, AbstractDefaultCache $objectCache)
    {
        $this->curl    = $curl;
        $this->objJson = $jsonBasic;
        $this->cache   = $objectCache;
    }

    public function setHeader($key,$value){
        $this->header[] = "$key: $value";
        return $this;
    }

    public function setMethod($value){
        $this->method = $value;
        return $this;
    }

    public function isBodyJson(){
        $this->bodyJson = true;
        return $this;
    }

    public function setBodyPost($arrayValues=[]){
        if( !empty( $arrayValues ) ){
            $this->bodyPost = !$this->bodyJson ? $arrayValues : json_encode( $arrayValues );
        }
        return $this;
    }

    public function setTimeout($value){
        $this->timeout = $value;
        return $this;
    }

    public function setServiceName($value){
        $this->serviceName = $value;
        $this->host       .= $this->serviceName;
        return $this;
    }

    public function setDefaultVariables(){
        $this->setHeader('x-pdv-token', \Request::header('x-pdv-token'));
        $this->setDFIn();
        return $this;
    }

    public function setDFOut(){
        foreach ( $this->header as $key=>$row ){if( strpos( $row, 'X-DreamFactory-API-Key' ) !== false ){unset( $this->header[$key] );break;}}
        $this->setHeader('X-DreamFactory-API-Key', config('app.api_key_out'));
        return $this;
    }

    public function setDFIn(){
        foreach ( $this->header as $key=>$row ){if( strpos( $row, 'X-DreamFactory-API-Key' ) !== false ){unset( $this->header[$key] );}}
        $this->setHeader('X-DreamFactory-API-Key', config('app.api_key'));
        return $this;
    }

    protected function setMsgError($msg){$this->msgError = $msg;}
    public function getHeader(){return $this->header;}
    public function getHost(){return $this->host;}
    public function getMsgError(){return $this->msgError;}

    public function request($params = [], $service=''){
        $options['method'] = $this->method;
        $options['header'] = $this->header;
        $options['data']   = ( !empty( $params ) ? $params : $this->bodyPost );

        if( $this->bodyJson )        {$options['json'] = TRUE;}
        if( $this->method == 'POST' ){$options['post'] = TRUE;}

        //dd( $this->host.$service, $options );

        $return = $this->curl->send($this->host.$service, $options);
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
                $result = $return['RESULT'];//dd($return);
                $this->setMsgError($result['message']);
                $error  = true;
            }

            if($error){
                \LogDebug::error($return['RESULT']);
            }
        }

        return $result;

    }

    public function response($return){

        $msg = '';
        switch ( $return ){
            case $this::RESPONSE_INVALID_ARQ:
                $msg = \Lang::get('default.parameters_incorrets');
                break;
        }

        ThrowNewExceptionFacades::{$return}($msg);
    }

    public function getUrlService($serviceName){

        $keyCache    = 'getUrl_' . $serviceName;
        if( !$this->cache->has( $keyCache ) ) {
            $baseUrl           = null;//config('smssapi.proxy_service_name');
            $baseUrl           = !empty( $baseUrl ) ? $baseUrl : config('pdvapi.proxy_df_url');

            $url               = $baseUrl."services_proxy/_table/services?fields=url&filter=slug='$serviceName'";
            $options['header'] = $this->header;//dd($url, $options);
            $return            = $this->curl->send($url, $options);
            $url               = '';
            if( is_array( $return ) ){
                $result = json_decode( $return['RESULT'], true );
                if( is_array( $result ) && key_exists('resource', $result) && !empty( $result['resource'] ) ){
                    $url = $result['resource'][0]['url'];
                    $this->cache->setTime(60)->create($keyCache,$url);
                } else {
                    \LogDebug::request('',$return);
                }
            }
        } else {
            $url = $this->cache->get($keyCache);
        }

        $this->host = $url;
    }
}