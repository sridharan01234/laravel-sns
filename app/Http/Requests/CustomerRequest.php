<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:customers,mobile,' . ($this->customer->id ?? ''),
            'email' => 'nullable|email',
            'status' => 'boolean',
            'group_id' => 'nullable|exists:groups,id',
            'additional_details' => 'nullable|array'
        ];

        return $rules;
    }
}
