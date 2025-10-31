<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount'       => 'required|numeric',
            'description'  => 'nullable|string',
            'status'       => 'nullable|string|in:pending,paid,cancelled',
            'deb'          => 'nullable|numeric',
            'due_date'     => 'nullable|date',
            'type_id'      => 'nullable|integer',
            'installments' => 'nullable|integer',
        ];
    }
}
