<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Services\CarImport\Connectors\AutoScout24Connector;
use App\Services\CarImport\Connectors\CarConnector;
use App\Services\CarImport\Connectors\CochesNetConnector;
use App\Services\CarImport\Connectors\MilanunciosConnector;
use App\Services\CarImport\Connectors\WallapopConnector;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImportCarsFromFeeds extends Command
{
    protected $signature = 'cars:import-feeds
                            {--connector=* : Import only specific connector keys}
                            {--limit=20 : Max listings per connector}
                            {--dry-run : Parse connectors without writing to DB}';

    protected $description = 'Importa anuncios reales desde conectores de portales de coches';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $selectedConnectorKeys = collect((array) $this->option('connector'))
            ->map(fn ($value): string => mb_strtolower(trim((string) $value)))
            ->filter()
            ->values();

        $connectors = collect($this->connectors())
            ->filter(fn (CarConnector $connector): bool => $connector->isConfigured())
            ->when($selectedConnectorKeys->isNotEmpty(), function ($collection) use ($selectedConnectorKeys) {
                return $collection->filter(function (CarConnector $connector) use ($selectedConnectorKeys): bool {
                    return $selectedConnectorKeys->contains(mb_strtolower($connector->key()));
                });
            })
            ->values();

        if ($connectors->isEmpty()) {
            $this->warn('No hay conectores disponibles con URL configurada.');

            return self::INVALID;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($connectors as $connector) {
            $this->components->task('Conector '.$connector->label(), function () use ($connector, $limit, $dryRun, &$created, &$updated, &$skipped): void {
                $listings = $connector->fetchListings($limit);

                foreach ($listings as $listing) {
                    $normalized = $this->normalizeListing($listing, $connector);

                    if ($normalized === null) {
                        $skipped++;

                        continue;
                    }

                    if ($dryRun) {
                        continue;
                    }

                    $result = $this->upsertCar($normalized);

                    if ($result === 'created') {
                        $created++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            });
        }

        $this->newLine();
        $this->table(
            ['Modo', 'Creados', 'Actualizados', 'Omitidos'],
            [[
                $dryRun ? 'Dry run' : 'Escritura',
                (string) $created,
                (string) $updated,
                (string) $skipped,
            ]]
        );

        if ($dryRun) {
            $this->info('Dry run completado. No se guardaron cambios.');
        } else {
            $this->info('Importación completada correctamente.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, CarConnector>
     */
    private function connectors(): array
    {
        return [
            new WallapopConnector,
            new CochesNetConnector,
            new AutoScout24Connector,
            new MilanunciosConnector,
        ];
    }

    /**
     * @param  array<string, mixed>  $listing
     * @return array<string, mixed>|null
     */
    private function normalizeListing(array $listing, CarConnector $connector): ?array
    {
        $sourceUrl = trim((string) ($listing['source_url'] ?? ''));

        if ($sourceUrl === '') {
            return null;
        }

        $brand = trim((string) ($listing['brand'] ?? ''));
        $model = trim((string) ($listing['model'] ?? ''));
        $title = trim((string) ($listing['title'] ?? ''));

        if ($brand === '' && $title !== '') {
            $brand = Str::of($title)->explode(' ')->first() ?? '';
        }

        if ($brand === '') {
            return null;
        }

        if ($model === '') {
            $model = trim((string) Str::of($title)->after($brand));
        }

        if ($model === '') {
            $model = 'Modelo';
        }

        $year = (int) ($listing['year'] ?? now()->year);
        $year = max(1990, min(now()->year + 1, $year));

        $price = (float) ($listing['price'] ?? 0);
        $mileage = (int) ($listing['mileage'] ?? 0);

        if ($price <= 0) {
            return null;
        }

        $sourceExternalId = trim((string) ($listing['external_id'] ?? md5($sourceUrl)));

        return [
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'price' => max(0, $price),
            'mileage' => max(0, $mileage),
            'fuel_type' => (string) ($listing['fuel_type'] ?? 'Gasolina'),
            'transmission' => (string) ($listing['transmission'] ?? 'Manual'),
            'color' => (string) ($listing['color'] ?? 'No especificado'),
            'city' => (string) ($listing['city'] ?? 'España'),
            'featured' => (bool) ($listing['featured'] ?? false),
            'status' => in_array(($listing['status'] ?? 'available'), ['available', 'reserved', 'sold'], true) ? (string) $listing['status'] : 'available',
            'source_name' => $connector->label(),
            'source_url' => $sourceUrl,
            'source_external_id' => $sourceExternalId,
            'thumbnail_url' => $this->uploadToCloudinary((string) ($listing['thumbnail_url'] ?? null)),
            'gallery' => is_array($listing['gallery'] ?? null) ? $listing['gallery'] : [],
            'description' => (string) ($listing['description'] ?? 'Anuncio importado automáticamente desde portal externo.'),
            'last_synced_at' => now(),
        ];
    }

    private function uploadToCloudinary(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        try {
            // Solo subimos si es una URL externa válida
            if (! str_starts_with($url, 'http')) {
                return $url;
            }

            $uploadedFileUrl = Cloudinary::upload($url, [
                'folder' => 'cars_aggregator',
            ])->getSecurePath();

            return $uploadedFileUrl;
        } catch (\Throwable $e) {
            // Si falla la subida, mantenemos la URL original para no perder la imagen
            report($e);
            return $url;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertCar(array $payload): string
    {
        $car = Car::query()
            ->where('source_name', $payload['source_name'])
            ->where('source_external_id', $payload['source_external_id'])
            ->first();

        if ($car === null) {
            $car = Car::query()
                ->where('source_url', $payload['source_url'])
                ->first();
        }

        $slugBase = Str::slug($payload['brand'].'-'.$payload['model'].'-'.$payload['year'].'-'.$payload['source_name'].'-'.$payload['source_external_id']);
        $payload['slug'] = $this->buildUniqueSlug($slugBase, $car?->id);

        if ($car !== null) {
            $car->update($payload);

            return 'updated';
        }

        Car::query()->create([
            ...$payload,
            'outbound_clicks' => 0,
        ]);

        return 'created';
    }

    private function buildUniqueSlug(string $baseSlug, ?int $ignoreCarId = null): string
    {
        $slug = Str::limit($baseSlug, 180, '');

        if ($slug === '') {
            $slug = 'coche-importado';
        }

        $candidate = $slug;
        $suffix = 1;

        while (
            Car::query()
                ->when($ignoreCarId !== null, fn ($query) => $query->where('id', '!=', $ignoreCarId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = Str::limit($slug, 175, '').'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
