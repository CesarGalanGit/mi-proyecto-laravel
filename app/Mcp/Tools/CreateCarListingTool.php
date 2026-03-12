<?php

namespace App\Mcp\Tools;

use App\Models\Car;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;

#[Title('Create Car Listing')]
#[Description('Creates a new car listing / advertisement in the platform. Requires brand, model, year, price, url, and image_url. Returns the created listing data as JSON.')]
class CreateCarListingTool extends Tool
{
    public function shouldRegister(): bool
    {
        $user = Auth::user();

        // Local MCP server (stdio) runs without an authenticated user.
        if ($user === null) {
            return true;
        }

        return $user->can('manage-users');
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:2030',
            'price' => 'required|numeric|min:0',
            'url' => 'required|url|max:2000',
            'image_url' => 'required|url|max:2000',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|in:gasolina,diesel,electrico,hibrido,gas',
            'transmission' => 'nullable|string|in:manual,automatico',
            'color' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'status' => 'nullable|string|in:available,sold,reserved',
            'source_name' => 'nullable|string|max:100',
            'source_external_id' => 'nullable|string|max:255',
        ], [
            'brand.required' => 'You must provide the car brand. Example: "Toyota", "BMW", "Seat".',
            'model.required' => 'You must provide the car model. Example: "Corolla", "X3", "Ibiza".',
            'year.required' => 'You must provide the manufacturing year. Example: 2023.',
            'year.min' => 'The year must be 1900 or later.',
            'price.required' => 'You must provide a price in euros. Example: 15000.',
            'price.min' => 'The price must be zero or greater.',
            'url.required' => 'You must provide the URL of the listing.',
            'image_url.required' => 'You must provide the image URL of the listing.',
            'fuel_type.in' => 'Fuel type must be one of: gasolina, diesel, electrico, hibrido, gas.',
            'transmission.in' => 'Transmission must be either: manual, automatico.',
            'status.in' => 'Status must be one of: available, sold, reserved.',
        ]);

        $slug = Str::slug($validated['brand'].'-'.$validated['model'].'-'.$validated['year'].'-'.Str::random(5));

        $car = Car::create([
            'slug' => $slug,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'year' => $validated['year'],
            'price' => $validated['price'],
            'mileage' => $validated['mileage'] ?? null,
            'fuel_type' => $validated['fuel_type'] ?? null,
            'transmission' => $validated['transmission'] ?? null,
            'color' => $validated['color'] ?? null,
            'city' => $validated['city'] ?? null,
            'description' => $validated['description'] ?? null,
            'source_url' => $validated['url'],
            'source_name' => $validated['source_name'] ?? null,
            'source_external_id' => $validated['source_external_id'] ?? null,
            'thumbnail_url' => $validated['image_url'],
            'status' => $validated['status'] ?? 'available',
            'featured' => false,
        ]);

        return Response::text(json_encode([
            'success' => true,
            'message' => "Car listing '{$car->brand} {$car->model} ({$car->year})' created successfully.",
            'car' => [
                'id' => $car->id,
                'slug' => $car->slug,
                'brand' => $car->brand,
                'model' => $car->model,
                'year' => $car->year,
                'price' => $car->price,
                'url' => $car->source_url,
                'image_url' => $car->thumbnail_url,
                'fuel_type' => $car->fuel_type,
                'transmission' => $car->transmission,
                'color' => $car->color,
                'city' => $car->city,
                'status' => $car->status,
                'source_name' => $car->source_name,
                'source_external_id' => $car->source_external_id,
                'description' => $car->description,
                'created_at' => $car->created_at->toIso8601String(),
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'brand' => $schema->string()
                ->description('Car manufacturer / brand. Examples: "Toyota", "BMW", "Seat", "Volkswagen".')
                ->required(),

            'model' => $schema->string()
                ->description('Car model name. Examples: "Corolla", "X3", "Ibiza", "Golf".')
                ->required(),

            'year' => $schema->integer()
                ->description('Manufacturing year of the car (1900-2030). Example: 2023.')
                ->required(),

            'price' => $schema->number()
                ->description('Price in euros. Example: 15000.00.')
                ->required(),

            'url' => $schema->string()
                ->description('URL of the external listing.')
                ->required(),

            'image_url' => $schema->string()
                ->description('URL of the listing image/thumbnail.')
                ->required(),

            'mileage' => $schema->integer()
                ->description('Mileage in kilometers. Example: 45000.'),

            'fuel_type' => $schema->string()
                ->enum(['gasolina', 'diesel', 'electrico', 'hibrido', 'gas'])
                ->description('Fuel type of the car.')
                ->default('gasolina'),

            'transmission' => $schema->string()
                ->enum(['manual', 'automatico'])
                ->description('Transmission type of the car.')
                ->default('manual'),

            'color' => $schema->string()
                ->description('Color of the car. Example: "Rojo", "Negro", "Blanco".'),

            'city' => $schema->string()
                ->description('City where the car is located. Example: "Madrid", "Barcelona".'),

            'description' => $schema->string()
                ->description('Optional detailed description of the car listing.'),

            'status' => $schema->string()
                ->enum(['available', 'sold', 'reserved'])
                ->description('Listing status.')
                ->default('available'),

            'source_name' => $schema->string()
                ->description('Name of the source/portal (e.g., "coches.net", "milanuncios.com").'),

            'source_external_id' => $schema->string()
                ->description('External ID from the source portal or MD5 hash of the URL.'),
        ];
    }
}
