<?php

namespace Danganf\MyClass\Contracts;

abstract class DependencyFiles
{
    protected $routename;
    protected $js  = [];
    protected $css = [];

    public function __construct ( $routeName ) {
        $this->routename = ucfirst( camel_case( str_replace('.','_',$routeName ) ) );
    }

    final public function render(){//dd($this->routename);

        if( method_exists( $this, 'route' . $this->routename ) ){
            call_user_func_array(array($this,'route' . $this->routename),[]);
        }

        return [ 'css' => $this->css, 'js' => $this->js ];

    }

    protected function add( $section, $files ){
        $this->{$section}[] = $files;
    }
}