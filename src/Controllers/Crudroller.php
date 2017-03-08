<?php

namespace Shanginn\Crudroller\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface Crudroller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Model $model, Request $request);

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    public function store(FormRequest $request, Model $model);

    /**
     * Display the specified resource.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Model $item);

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function update(FormRequest $request, Model $item);

    /**
     * Remove the specified resource from storage.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $item);
}
