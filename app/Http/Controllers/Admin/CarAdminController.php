<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CarAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $cars = Car::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('brand', 'LIKE', "%{$search}%")
                        ->orWhere('model', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%")
                        ->orWhere('source_name', 'LIKE', "%{$search}%")
                        ->orWhere('slug', 'LIKE', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.shop.cars', [
            'cars' => $cars,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function store(StoreCarRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->buildUniqueSlug(
            brand: $validated['brand'],
            model: $validated['model'],
            year: (int) $validated['year']
        );

        Car::query()->create([
            'slug' => $slug,
            ...$this->buildCarPayload($validated, $request),
        ]);

        return back()->with('success', 'Coche creado correctamente.');
    }

    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->buildUniqueSlug(
            brand: $validated['brand'],
            model: $validated['model'],
            year: (int) $validated['year'],
            ignoreCarId: $car->id,
        );

        $car->update([
            'slug' => $slug,
            ...$this->buildCarPayload($validated, $request),
        ]);

        return back()->with('success', 'Coche actualizado correctamente.');
    }

    public function destroy(Car $car): RedirectResponse
    {
        if ($car->orderItems()->exists()) {
            return back()->with('error', 'No puedes eliminar un coche que ya tiene pedidos asociados.');
        }

        $car->delete();

        return back()->with('success', 'Coche eliminado correctamente.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function buildCarPayload(array $validated, Request $request): array
    {
        $galleryLines = preg_split('/\r\n|\r|\n/', (string) ($validated['gallery_urls'] ?? '')) ?: [];

        $galleryUrls = collect($galleryLines)
            ->map(fn (string $url): string => trim($url))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $thumbnailUrl = $validated['thumbnail_url'] ?? null;

        if ($thumbnailUrl === null && count($galleryUrls) > 0) {
            $thumbnailUrl = $galleryUrls[0];
        }

        return [
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'year' => (int) $validated['year'],
            'price' => (float) $validated['price'],
            'mileage' => (int) $validated['mileage'],
            'fuel_type' => $validated['fuel_type'],
            'transmission' => $validated['transmission'],
            'color' => $validated['color'],
            'city' => $validated['city'],
            'status' => $validated['status'],
            'source_name' => $validated['source_name'],
            'source_url' => $validated['source_url'],
            'last_synced_at' => now(),
            'featured' => $request->boolean('featured'),
            'thumbnail_url' => $thumbnailUrl,
            'gallery' => $galleryUrls,
            'description' => $validated['description'] ?? null,
        ];
    }

    private function buildUniqueSlug(string $brand, string $model, int $year, ?int $ignoreCarId = null): string
    {
        $base = Str::slug($brand.'-'.$model.'-'.$year);
        $slug = $base;
        $suffix = 1;

        while (
            Car::query()
                ->when($ignoreCarId !== null, fn ($query) => $query->where('id', '!=', $ignoreCarId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
