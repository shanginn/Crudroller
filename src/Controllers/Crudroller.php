<?php

namespace Shanginn\Crudroller\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;

abstract class Crudroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    abstract public function index(Model $model);

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    abstract public function store(FormRequest $request, Model $model);

    /**
     * Display the specified resource.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    abstract public function show(Model $item);

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    abstract public function update(FormRequest $request, Model $item);

    /**
     * Remove the specified resource from storage.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    abstract public function destroy(Model $item);
}
