<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $product = Product::find($item['product_id']);

                if (!$product || $item['qty'] > $product->stock) {
                    $name = $product?->name ?? 'Produk';

                    $validator->errors()->add(
                        "items.$index.qty",
                        "Stok {$name} tidak mencukupi. Stok tersedia: "
                        . ($product?->stock ?? 0) . '.'
                    );
                }
            }
        });
    }
}
