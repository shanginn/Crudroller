<?php

namespace Shanginn\Crudroller\Routing\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CrudrollerBindings
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __construct(Registrar $router, Application $app)
    {
        $this->router = $router;
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        if (isset($route->action['crud'])) {
            [
                $modelClass,
                $itemParam,
                $requestsClasses,
                $controllerClass
            ] = $route->action['crud'];

            [$class, $method] = Str::parseCallback($route->action['uses']);

            // If we have concrete controller for this endpoint
            if ($controllerClass !== '\Crudroller') {
                // If endpoint method exists in the concrete controller
                // let the controller handle everything else
                if (method_exists($controllerClass, $method)) {
                    $route->uses($controllerClass . '@' . $method);
                    $route->controller = (new $controllerClass);

                    return $next($request);
                }
            }

            $this->insertModelInstance($modelClass, $route, $itemParam);

            $this->app->bind(Model::class, $modelClass);
            $this->app->bind(FormRequest::class, $requestsClasses[$method] ?? $requestsClasses['default']);
        }

        return $next($request);
    }

    /**
     * @param string $modelClass
     * @param \Illuminate\Routing\Route $route
     * @param string $itemParam
     */
    protected function insertModelInstance($modelClass, $route, $itemParam)
    {
        if ($param = $route->parameter($itemParam)) {
            $model = $this->app->make($modelClass);

            $route->setParameter(
                'item',
                $model->where(
                    $model->getRouteKeyName(),
                    $param
                )->firstOrFail()
            );

            $route->forgetParameter($itemParam);
        }
    }
}
