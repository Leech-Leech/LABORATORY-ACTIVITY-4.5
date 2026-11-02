<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route is already behind the auth middleware
    }

    /** Drop blank rows so only real lines are validated. */
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($row) => is_array($row) && filled($row['product_id'] ?? null))
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    /** Note: no price fields are accepted; prices come from the database. */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'An order must contain at least one product.',
            'items.min' => 'An order must contain at least one product.',
        ];
    }
}
