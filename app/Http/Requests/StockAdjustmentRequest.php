<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adjustment_no' => 'required|unique:stock_adjustments,adjustment_no,'.$this->route('adjustment'),
            'adjustment_date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric',
            'notes' => 'nullable|string',
        ];
    }
}
