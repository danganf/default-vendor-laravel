<?php

namespace Danganf\MyClass;

class DependencyFiles
{
    const JS_FUNCTION         = 'js/functions.js';
    const JS_SEARCH_CEP       = 'js/search-cep.js';
    const JS_SEARCH_ENDERECOS = 'js/search-enderecos.js';
    const JS_FILTER           = 'js/myFilters.js';

    protected $routename;
    protected $js  = [];
    protected $css = [];

    public function __construct ( $routeName ) {
        $this->routename = ucfirst( camel_case( str_replace('.','_',$routeName ) ) );
    }

    public function render(){

        if( method_exists( $this, 'route' . $this->routename ) ){
            call_user_func_array(array($this,'route' . $this->routename),[]);
        }

        return [ 'css' => $this->css, 'js' => $this->js ];

    }

    protected function add( $section, $files ){
        $this->{$section}[] = $files;
    }

}
