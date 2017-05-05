<?php

namespace Shanginn\Crudroller\Routing;
use \Illuminate\Routing\Router;
use Intervention\Image\Exception\RuntimeException;

class Cruder
{

    protected $modelsNamespace = '\App';
    protected $requestsNamespace = '\App\Api\V1\Requests';
    protected $controllersNamespace = '\App\Api\V1\Controllers';

    //TODO: get methods from resource registrar
    protected $methods = ['store', 'show', 'update', 'destroy'];

    /**
     * Dingo router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * Register endpoints
     *
     * @param array $endpoints
     * @param array $options
     *
     * @return void
     */
    public function endpoints($endpoints, $options = [])
    {
        foreach ($endpoints as $endpoint) {
            if (!is_array($endpoint)) {
                $endpoint = [$endpoint, $options];
            } else {
                $endpoint[1] = array_merge(($endpoint[1] ?? []), $options);
            }

            call_user_func_array([$this, 'endpoint'], array_merge(
                (array) $endpoint
            ));
        }
    }

    /**
     * Register a resource controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options
     *
     * @return void
     */
    public function endpoint($name, $controller = 'Crudroller', array $options = [])
    {
        $basename = ucfirst(str_singular($name));

        // We can call this method with 2 or 3 arguments
        if (is_array($controller)) {
            $options = $controller;
            $controller = 'Crudroller';
        }

        if (!isset($options['crud']) || !is_array($options['crud'])) {
            $options['crud'] = [];
        }

        $options['crud']['Model'] = $options['crud']['Model'] ?? $this->getModelClass($basename);

        $options['crud']['Requests'] = $this->getRequestsClasses($basename, $options['crud']['Requests'] ?? []);

        $options['crud']['Controller'] = $options['crud']['Controller'] ?? $this->getControllerClass($basename);

        //dump($name, $options);
        $this->router->resource($name, $controller, $options);
    }

    protected function getControllerClass($basename)
    {
        $controllerClass = $this->controllersNamespace . '\\' . $basename . 'Controller';
        return class_exists($controllerClass) ? $controllerClass : 'Crudroller';
    }

    protected function getModelClass($basename)
    {
        $modelClass = $this->modelsNamespace . '\\'. $basename;

        if (!class_exists($modelClass)) {
            return false;
            throw new RuntimeException('Model ' . $modelClass . ' doesn\'t exits');
        }

        return $modelClass;
    }

    protected function getRequestsClasses($basename, $requestsFromOptions)
    {
        $requestBasename = $this->requestsNamespace . '\\' . $basename;

        $requests = array_filter(array_reduce($this->methods, function ($result, $method) use ($requestBasename) {
            $requestClass = $requestBasename . ucfirst($method) . 'Request';
            $fallbackClass = $this->requestsNamespace . '\\Crud' . ucfirst($method) . 'Request';

            $result[$method] = $result[$method] ??
                class_exists($requestClass) ? $requestClass :
                    (class_exists($fallbackClass) ? $fallbackClass : null);

            return $result;
        }, $requestsFromOptions), function ($e) {
            return !is_null($e);
        });

        $requestClass = $requestBasename . 'Request';
        $requests['default'] = class_exists($requestClass) ? $requestClass : 'CrudRequest';

        return $requests;
    }
}