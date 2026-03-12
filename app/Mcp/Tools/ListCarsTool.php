<?php

namespace App\Mcp\Tools;

use App\Models\Car;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Title('List Car Listings')]
#[Description('Lists car advertisements / listings in the platform. Supports filtering by brand, fuel type, city, status, price range, and pagination.')]
#[IsReadOnly]
#[IsIdempotent]
class ListCarsTool extends Tool
{
    public function shouldRegister(): bool
    {
        $user = Auth::user();

        // Local MCP server (stdio) runs without an authenticated user.
        if ($user === null) {
            return true;
        }

        // Admins always get full access.
        if ($user->can('manage-users')) {
            return true;
        }

        // Non-admin tokens can only list car listings.
        return method_exists($user, 'tokenCan') && $user->tokenCan('cars:list');
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $query = Car::query()
            ->select([
                'id', 'slug', 'brand', 'model', 'year', 'price',
                'mileage', 'fuel_type', 'transmission', 'color',
                'city', 'status', 'created_at',
            ]);

        if ($brand = $request->get('brand')) {
            $query->where('brand', 'LIKE', "%{$brand}%");
        }

        if ($fuelType = $request->get('fuel_type')) {
            $query->where('fuel_type', $fuelType);
        }

        if ($city = $request->get('city')) {
            $query->where('city', 'LIKE', "%{$city}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($minPrice = $request->get('min_price')) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        $total = $query->count();
        $cars = $query->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return Response::text(json_encode([
            'success' => true,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'cars' => $cars->map(fn (Car $car) => [
                'id' => $car->id,
                'slug' => $car->slug,
                'brand' => $car->brand,
                'model' => $car->model,
                'year' => $car->year,
                'price' => $car->price,
                'mileage' => $car->mileage,
                'fuel_type' => $car->fuel_type,
                'transmission' => $car->transmission,
                'color' => $car->color,
                'city' => $car->city,
                'status' => $car->status,
                'created_at' => $car->created_at?->toIso8601String(),
            ])->toArray(),
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
                ->description('Filter by car brand. Partial match. Example: "Toyota".'),

            'fuel_type' => $schema->string()
                ->enum(['gasolina', 'diesel', 'electrico', 'hibrido', 'gas'])
                ->description('Filter by fuel type.'),

            'city' => $schema->string()
                ->description('Filter by city. Partial match. Example: "Madrid".'),

            'status' => $schema->string()
                ->enum(['available', 'sold', 'reserved'])
                ->description('Filter by listing status.'),

            'min_price' => $schema->number()
                ->description('Minimum price filter in euros.'),

            'max_price' => $schema->number()
                ->description('Maximum price filter in euros.'),

            'limit' => $schema->integer()
                ->description('Maximum number of listings to return. Default: 20.')
                ->default(20),

            'offset' => $schema->integer()
                ->description('Number of listings to skip for pagination. Default: 0.')
                ->default(0),
        ];
    }
}
