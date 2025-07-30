<?php

namespace admin\tags\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [                   
            'name' => 'required|string|min:3|max:100|unique:tags,name',
            'status' => 'required|in:0,1',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
