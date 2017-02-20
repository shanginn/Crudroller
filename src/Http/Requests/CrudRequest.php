<?php

namespace Shanginn\Crudroller\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrudRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }
}
