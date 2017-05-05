<?php

namespace Shanginn\Crudroller\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

interface Crudroller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\Response
     */
    public function index(Model $model);

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
     * @param  Model $item
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
     * @param  Model $item
     * @param  FormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $item, FormRequest $request);
}
