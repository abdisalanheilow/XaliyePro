<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_no' => 'required|unique:goods_receipts,receipt_no',
            'vendor_id' => 'required|exists:vendors,id',
            'received_date' => 'required|date',
            'received_by' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
        ];
    }
}
