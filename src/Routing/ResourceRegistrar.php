<?php

namespace Shanginn\Crudroller\Routing;

class ResourceRegistrar extends \Illuminate\Routing\ResourceRegistrar
{
    /**
     * The default actions for a resourceful controller. Excludes 'create' and 'edit'.
     *
     * @var array
     */
    protected $resourceDefaults = ['index', 'store', 'show', 'update', 'destroy'];

    /**
     * The default endpoints for bulk actions.
     *
     * @var array
     */
    protected $bulkEndpoints = ['store', 'update', 'destroy'];

    /**
     * Get the applicable resource methods.
     *
     * @param  array  $defaults
     * @param  array  $options
     * @return array
     */
    protected function getResourceMethods($defaults, $options)
    {
        $defaults = parent::getResourceMethods($defaults, $options);

        return $defaults;
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    public function register($name, $controller, array $options = [])
    {
        if (!isset($options['middleware'])) {
            $options['middleware'] = 'crud.binding';
        }

        parent::register($name, $controller, $options);
    }

    /**
     * Get the action array for a resource route.
     *
     * @param  string  $resource
     * @param  string  $controller
     * @param  string  $method
     * @param  array   $options
     * @return array
     */
    protected function getResourceAction($resource, $controller, $method, $options)
    {
        $name = $this->getResourceRouteName($resource, $method, $options);

        $action = ['as' => $name, 'uses' => $controller . '@' . $method];

        if (isset($options['crud'])) {
            $action['crud'] = [
                $options['crud']['Model'],
                $options['crud']['parameter'] ?? $this->getResourceWildcard($resource),
                $options['crud']['Requests'],
                $options['crud']['Controller'],
            ];
        }

        if (isset($options['middleware'])) {
            $action['middleware'] = $options['middleware'];
        }

        return $action;
    }
}
