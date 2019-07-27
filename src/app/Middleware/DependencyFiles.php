<?php
namespace Danganf\Middleware;

use Closure;

class DependencyFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName   = getRouteName();
        $routeExplod = explode( '.', $routeName );
        $request->merge( [
            'group'      => $routeExplod[0],
            'routeName'  => $routeName,
            'prefixName' => getRoutePrefixName()
        ] );

        $render = ( new \App\MyClass\DependencyFiles( $routeName ) )->render();

        if( array_has( $render, 'css' ) ){ $request->merge( [ 'cssFiles' => $render['css'] ] ); }
        if( array_has( $render, 'js' ) ) { $request->merge( [ 'jsFiles'  => $render['js'] ] ); }

        return $next($request);
    }
}
