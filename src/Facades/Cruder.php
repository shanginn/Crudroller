<?php

namespace Shanginn\Crudroller\Facades;

use Illuminate\Support\Facades\Facade;

class Cruder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'crud.cruder';
    }
}
