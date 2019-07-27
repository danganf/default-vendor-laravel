<?php
namespace Danganf\Middlewar;

use Illuminate\Support\Facades\App;

class ValidaContentBodyJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ( $request->isJson() ) {
            $action = $request->route()->getAction()['as'];
            if( !empty( $action ) ) {
                $json = $request->getContent();
                if( !empty( $json ) ) {
                    $action = ucfirst(camel_case(str_replace('.', '_', $action)));
                    $instacia = App::make('\App\MyClass\Json\Json' . $action);
                    if (!empty($json)) {
                        $instacia->set($json);
                    }
                }
                return $next($request);
            }
        }
        return msgErroJson('Falha nos parametros!');
    }
}
