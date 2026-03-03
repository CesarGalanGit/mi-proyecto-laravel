<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'price' => ['required', 'numeric', 'min:1000'],
            'mileage' => ['required', 'integer', 'min:0', 'max:500000'],
            'fuel_type' => ['required', 'string', 'max:40'],
            'transmission' => ['required', 'string', 'max:40'],
            'color' => ['required', 'string', 'max:60'],
            'city' => ['required', 'string', 'max:80'],
            'status' => ['required', 'in:available,reserved,sold'],
            'source_name' => ['required', 'string', 'max:80'],
            'source_url' => ['required', 'url', 'max:2048'],
            'featured' => ['nullable', 'boolean'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'gallery_urls' => ['nullable', 'string', 'max:5000'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand.required' => 'La marca es obligatoria.',
            'model.required' => 'El modelo es obligatorio.',
            'year.required' => 'El año es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'mileage.required' => 'El kilometraje es obligatorio.',
            'status.in' => 'El estado debe ser disponible, reservado o vendido.',
            'source_name.required' => 'Debes indicar el portal de origen del anuncio.',
            'source_url.required' => 'Debes indicar la URL oficial del anuncio.',
            'source_url.url' => 'La URL oficial del anuncio no es válida.',
            'thumbnail_url.url' => 'La imagen principal debe ser una URL válida.',
        ];
    }
}
