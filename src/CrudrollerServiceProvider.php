<?php

namespace Shanginn\Crudroller;

use Illuminate\Support\ServiceProvider;
use Psy\Exception\RuntimeException;
use Shanginn\Crudroller\Facades\Cruder as CruderFacade;
use Shanginn\Crudroller\Routing\Cruder;
use Shanginn\Crudroller\Routing\Middleware\CrudrollerBindings;
use Shanginn\Crudroller\Routing\ResourceRegistrar;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Foundation\AliasLoader;
use Shanginn\Crudroller\Http\Requests\CrudRequest;
use Shanginn\Crudroller\Controllers\Crudroller;

class CrudrollerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //protected $defer = false;

    /**
     * Register all
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/crudroller.php',
            'crudroller'
        );

        $this->registerClassAliases();

        $this->registerMiddleware();

        $this->bindClasses();

        $this->registerFacades();

        $this->prependCrudrollerMiddleware($this->app->make(Kernel::class));
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/crudroller.php' => config_path('crudroller.php'),
        ], 'config');
    }

    protected function registerClassAliases()
    {
        $classAliases = [
            'crud.resource' => [
                \Dingo\Api\Routing\ResourceRegistrar::class,
                \Illuminate\Routing\ResourceRegistrar::class
            ],
            'crud.binding' => CrudrollerBindings::class,
            'crud.cruder' => Cruder::class,
        ];

        foreach ($classAliases as $key => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

    protected function registerMiddleware()
    {
        $this->aliasMiddleware('crud.binding', CrudrollerBindings::class);

        $router = $this->app['router'];

        if (($offset = 1)){//array_search(SubstituteBindings::class, $router->middlewarePriority)) !== false) {
            $router->middlewarePriority = array_merge(
                array_slice($router->middlewarePriority, 0, $offset),
                (array) CrudrollerBindings::class,
                array_slice($router->middlewarePriority, $offset)
            );
        };

    }

    protected function bindClasses()
    {
        $this->app->singleton('crud.resource', function ($app) {
            return new ResourceRegistrar($app->make('router'));
        });

        $this->app->singleton('crud.binding', function ($app) {
            return new CrudrollerBindings($app->router, $app);
        });

        $this->app->singleton('crud.cruder', function ($app) {
            return new Cruder($app->router);
        });
    }

    protected function registerFacades()
    {
        $crudController = config('crudroller.crud_controller_class');

        if (! (in_array(Crudroller::class, class_implements($crudController)))) {
            throw new RuntimeException($crudController . ' must implement ' . Crudroller::class . ' interface');
        }

        $facades = [
            'Cruder' => CruderFacade::class,
            'Crudroller' => $crudController,
            'CrudRequest' => CrudRequest::class
        ];

        AliasLoader::getInstance($facades)->register();
    }

    protected function prependCrudrollerMiddleware(\Illuminate\Foundation\Http\Kernel $kernel)
    {
        //kernel->prependMiddleware('crud.binding');
    }

    /**
     * Register a short-hand name for a middleware. For Compatability
     * with Laravel < 5.4 check if aliasMiddleware exists since this
     * method has been renamed.
     *
     * @param string $name
     * @param string $class
     *
     * @return mixed
     */
    protected function aliasMiddleware($name, $class)
    {
        /** @var \Dingo\Api\Routing\Router|\Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }
}
