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
        $routeName = getRouteName();
        $request->merge(['routeName' => $routeName]);

        $render = ( new \Danganf\DependencyFiles( $routeName ) )->render();

        if( array_has( $render, 'css' ) ){ $request->merge( [ 'cssFiles' => $render['css'] ] ); }
        if( array_has( $render, 'js' ) ) { $request->merge( [ 'jsFiles'  => $render['js'] ] ); }

        return $next($request);
    }
}