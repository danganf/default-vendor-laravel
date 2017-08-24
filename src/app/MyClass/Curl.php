<?php

namespace Danganf\MyClass;

class Curl{

    private $timeout           = 5;
    private $connectionTimeout = 2;

    public function __construct () {
        //
    }

    public function send( $url, array $options = [] ){

        $ch = curl_init();

        if (count($options) > 0) {
            if (!empty($options['timeout']))
                $this->timeout = $options['timeout'];

            if (!empty($options['connectionTimeout']))
                $this->connectionTimeout = $options['connectionTimeout'];

            if (!empty($options['header'])) {
                if (is_array($options['header'])) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
                } else {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array($options['header']));
                }
            }

            if (!empty($options['method']))
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);

            if (!empty($options['post']))
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

            if (!empty($options['data']))
                curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);

            if (!empty($options['json'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($options['data']))
                );
            }
        }

        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        #curl_setopt($ch, CURLOPT_INTERFACE, rand(111,999).".168.1.".rand(111,999));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
        curl_setopt($ch, CURLOPT_REFERER, ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null ) );

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

    }

}