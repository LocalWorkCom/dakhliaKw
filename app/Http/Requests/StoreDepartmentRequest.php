<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'manger' => 'nullable|exists:users,id',
            'manger_assistance' => 'nullable|exists:users,id',
    
        ];
    }
}

