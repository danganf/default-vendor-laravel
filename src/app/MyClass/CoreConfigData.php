<?php

namespace Danganf\MyClass;

use Illuminate\Http\Request;
use Danganf\Facades\ThrowNewExceptionFacades;

class CoreConfigData
{
    private $data;

    CONST PATH_CATALOG_IMG         = 'config/cdn/media/base_path';
    CONST PATH_BANNER_PRD_IMG      = 'config/cdn/banners/product/base_path';
    CONST PATH_BANNER_CATE_IMG     = 'config/cdn/banners/category/base_path';
    CONST PATH_CATEGORY_IMG        = 'config/cdn/media/category/base_path';
    CONST PATH_URL_CDN             = 'config/cdn/url';
    CONST PATH_CATEGORY_PATH       = 'config/icon/category/base_path';
    CONST PATH_JSON_PRINT_TEMPLATE = 'config/json/print/template/options';
    CONST PATH_PRINT_IP            = 'config/print/ip';

    /**
     * CoreConfigData constructor.
     * Resultado da api PDV_URL/store/config
     * @param string $sessionScope
     */
    public function __construct($sessionScope = 'coreConfigData', Request $request)
    {
        try {
            $this->data = $request->session()->get($sessionScope);
            if( empty( $this->data ) ){
                ThrowNewExceptionFacades::Unauthorized(\Lang::get('default.action_error'));
            }
        } catch (\Exception $e) {

            $data = null;
            if( class_exists('\PDVApi\MyClass\PDVApi') ){
                $PDVApi    = \App::make('\PDVApi\MyClass\PDVApi');
                $pdvConfig = $PDVApi->CoreConfig();
                $data      = $pdvConfig->get();
            } else if( class_exists('\App\MyClass\FactoryApis') ){
                $factoryApis = \App::make('\App\MyClass\FactoryApis');
                $data        = $factoryApis->get('core_config');
            }

            if( !empty( $data ) ){
                $this->data = $data;
            } else {
                ThrowNewExceptionFacades::Unauthorized(\Lang::get('default.parameters_incorrets'));
            }
        }

    }

    public function __call($method, $args) {
        $return = null;
        if( substr($method,0,3) == 'get' ) {
            $data   = preg_split('/(?=[A-Z])/', $method);
            $method = str_replace(['/get/','get/'], '', strtolower(implode('/', $data)));
            $return = $this->get($method);
            if( strpos( $method, '/json/' ) !== false ){
                $return = $this->convertJson( $return );
            }
        }
        return $return;
    }

    public function getCatalogBasePath()       {return $this->get( $this::PATH_CATALOG_IMG );}
    public function getCategoryBasePath()      {return $this->get( $this::PATH_CATEGORY_IMG );}
    public function getCatalogBannersProdPath(){return $this->get( $this::PATH_BANNER_PRD_IMG );}
    public function getCatalogBannersCatePath(){return $this->get( $this::PATH_BANNER_CATE_IMG );}
    public function getUrlCdn()                {return $this->get( $this::PATH_URL_CDN );}
    public function getCategoryIconPath()      {return $this->get( $this::PATH_CATEGORY_PATH );}
    public function getPrintIP()               {return $this->get( $this::PATH_PRINT_IP );}

    private function convertJson($string){return !empty( $string ) ? json_decode( $string, true ) : null;}

    public function get($path){

        $return = null;
        if( is_array( $this->data ) ){
            $result = array_where( $this->data, function($row, $value) use ($path)
            {
                return $row['path'] === $path;
            });
            $return = !empty( $result ) ? current($result)['value'] : null;
        }

        return $return;
    }

}
