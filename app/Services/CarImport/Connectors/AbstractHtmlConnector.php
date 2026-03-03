<?php

namespace App\Services\CarImport\Connectors;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

abstract class AbstractHtmlConnector implements CarConnector
{
    public function __construct(
        protected string $connectorKey,
        protected string $connectorLabel,
        protected string $domain,
        protected ?string $searchUrl,
    ) {}

    public function key(): string
    {
        return $this->connectorKey;
    }

    public function label(): string
    {
        return $this->connectorLabel;
    }

    public function isConfigured(): bool
    {
        return filled($this->searchUrl);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchListings(int $limit): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $searchHtml = $this->fetchContent((string) $this->searchUrl);

        if ($searchHtml === null) {
            return [];
        }

        $listingUrls = collect($this->extractListingUrls($searchHtml))
            ->filter()
            ->unique()
            ->take($limit)
            ->values()
            ->all();

        $listings = [];

        foreach ($listingUrls as $listingUrl) {
            $detailHtml = $this->fetchContent($listingUrl);

            if ($detailHtml === null) {
                continue;
            }

            $listing = $this->parseDetailPage($listingUrl, $detailHtml);

            if ($listing !== null) {
                $listings[] = $listing;
            }
        }

        return $listings;
    }

    /**
     * @return array<int, string>
     */
    protected function extractListingUrls(string $html): array
    {
        $decodedHtml = str_replace(['\\/', '\\u002F', '\\u002f'], '/', $html);

        $candidates = collect($this->extractHrefs($html))
            ->merge($this->extractPatternUrls($html))
            ->merge($this->extractPatternUrls($decodedHtml))
            ->filter()
            ->unique()
            ->values();

        return $candidates
            ->map(fn (string $href): string => $this->normalizeUrl($href))
            ->filter(fn (string $url): bool => $this->isListingUrl($url))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function extractHrefs(string $html): array
    {
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $matches);

        return $matches[1] ?? [];
    }

    /**
     * @return array<int, string>
     */
    protected function extractPatternUrls(string $html): array
    {
        $patterns = array_merge([
            '/"url"\s*:\s*"([^\"]+)"/i',
            '/https?:\/\/[^"\'\s<>]+/i',
            '/\/(?:anuncios|item)\/[a-z0-9\-\/]+/i',
            '/\/[a-z0-9\-\/]*[0-9]{5,}\.htm/i',
        ], $this->listingPathPatterns());

        $matches = [];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $html, $found) === 0 || $found === []) {
                continue;
            }

            if (isset($found[1])) {
                $matches = array_merge($matches, $found[1]);

                continue;
            }

            $matches = array_merge($matches, $found[0] ?? []);
        }

        return array_values(array_unique($matches));
    }

    /**
     * @return array<int, string>
     */
    protected function listingPathPatterns(): array
    {
        return [];
    }

    protected function isListingUrl(string $url): bool
    {
        return Str::contains($url, $this->domain);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function parseDetailPage(string $url, string $html): ?array
    {
        $name = $this->extractMetaContent($html, 'og:title')
            ?? $this->extractJsonLdValue($html, ['name'])
            ?? '';

        $description = $this->extractMetaContent($html, 'og:description')
            ?? $this->extractJsonLdValue($html, ['description'])
            ?? 'Anuncio importado desde '.$this->connectorLabel;

        $image = $this->extractMetaContent($html, 'og:image')
            ?? $this->extractJsonLdValue($html, ['image'])
            ?? null;

        $price = $this->extractJsonLdNumeric($html, ['offers', 'price'])
            ?? $this->extractFirstPrice($html);

        if ($price === null || $price <= 0) {
            return null;
        }

        $year = $this->extractYear($name) ?? (int) now()->year;
        [$brand, $model] = $this->extractBrandAndModel($name, $year);

        if ($brand === '') {
            return null;
        }

        $city = $this->extractJsonLdValue($html, ['offers', 'availableAtOrFrom', 'address', 'addressLocality'])
            ?? $this->extractJsonLdValue($html, ['address', 'addressLocality'])
            ?? 'España';

        return [
            'external_id' => md5($url),
            'source_url' => $url,
            'title' => $name,
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'price' => $price,
            'mileage' => $this->extractMileage($html) ?? 0,
            'fuel_type' => $this->extractFuelType($html) ?? 'Gasolina',
            'transmission' => $this->extractTransmission($html) ?? 'Manual',
            'color' => $this->extractColor($html) ?? 'No especificado',
            'city' => is_string($city) && $city !== '' ? $city : 'España',
            'thumbnail_url' => is_string($image) ? $image : null,
            'gallery' => is_string($image) ? [$image] : [],
            'description' => is_string($description) && $description !== '' ? $description : null,
            'featured' => false,
            'status' => 'available',
        ];
    }

    protected function normalizeUrl(string $href): string
    {
        $href = html_entity_decode(trim($href));

        if ($href === '') {
            return '';
        }

        if (Str::startsWith($href, ['http://', 'https://'])) {
            return $href;
        }

        if (! $this->searchUrl) {
            return '';
        }

        if (Str::startsWith($href, '//')) {
            return 'https:'.$href;
        }

        $base = rtrim((string) $this->searchUrl, '/');

        if (Str::startsWith($href, '/')) {
            $parsed = parse_url((string) $this->searchUrl);
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';

            return $host !== '' ? $scheme.'://'.$host.$href : '';
        }

        return $base.'/'.$href;
    }

    protected function fetchContent(string $url): ?string
    {
        try {
            if (Str::startsWith($url, 'file://')) {
                $path = Str::after($url, 'file://');

                return is_file($path) ? (string) file_get_contents($path) : null;
            }

            if (is_file($url)) {
                return (string) file_get_contents($url);
            }

            $request = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; LaravelStudioBot/1.0)',
                'Accept-Language' => 'es-ES,es;q=0.9',
            ])
                ->timeout((int) config('car_import.request_timeout', 12));

            try {
                $response = $request->get($url)->throw();
            } catch (ConnectionException $exception) {
                if (! Str::contains($exception->getMessage(), 'cURL error 60')) {
                    throw $exception;
                }

                $response = $request
                    ->withoutVerifying()
                    ->get($url)
                    ->throw();
            }

            return $response->body();
        } catch (Throwable) {
            return null;
        }
    }

    protected function extractMetaContent(string $html, string $property): ?string
    {
        $escaped = preg_quote($property, '/');

        if (preg_match('/<meta[^>]+(?:property|name)=["\']'.$escaped.'["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $match) === 1) {
            return trim(html_entity_decode($match[1]));
        }

        return null;
    }

    protected function extractJsonLdValue(string $html, array $path): ?string
    {
        $nodes = $this->extractJsonLdNodes($html);

        foreach ($nodes as $node) {
            $value = data_get($node, implode('.', $path));

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }

            if (is_array($value) && count($value) > 0 && is_string($value[0])) {
                return trim($value[0]);
            }
        }

        return null;
    }

    protected function extractJsonLdNumeric(string $html, array $path): ?float
    {
        $value = $this->extractJsonLdValue($html, $path);

        return $value !== null ? $this->toFloat($value) : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractJsonLdNodes(string $html): array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);

        $scripts = $matches[1] ?? [];
        $nodes = [];

        foreach ($scripts as $script) {
            $json = trim($script);

            if ($json === '') {
                continue;
            }

            $decoded = json_decode($json, true);

            if (! is_array($decoded)) {
                continue;
            }

            if (array_is_list($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item)) {
                        $nodes[] = $item;
                    }
                }

                continue;
            }

            $nodes[] = $decoded;
        }

        return $nodes;
    }

    protected function extractFirstPrice(string $html): ?float
    {
        if (preg_match('/([0-9]{1,3}(?:[\.\s][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?\s?€/u', $html, $match) === 1) {
            return $this->toFloat($match[0]);
        }

        if (preg_match('/€\s?([0-9]{1,3}(?:[\.\s][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?/u', $html, $match) === 1) {
            return $this->toFloat($match[0]);
        }

        return null;
    }

    protected function extractMileage(string $html): ?int
    {
        if (preg_match('/([0-9]{1,3}(?:[\.\s][0-9]{3})+|[0-9]+)\s?km/u', $html, $match) === 1) {
            return (int) preg_replace('/[^0-9]/', '', $match[1]);
        }

        return null;
    }

    protected function extractFuelType(string $html): ?string
    {
        foreach (['Diésel', 'Diesel', 'Gasolina', 'Híbrido', 'Hibrido', 'Eléctrico', 'Electrico'] as $fuel) {
            if (Str::contains(Str::lower($html), Str::lower($fuel))) {
                return str_replace(['Diesel', 'Hibrido', 'Electrico'], ['Diésel', 'Híbrido', 'Eléctrico'], $fuel);
            }
        }

        return null;
    }

    protected function extractTransmission(string $html): ?string
    {
        if (Str::contains(Str::lower($html), 'autom')) {
            return 'Automática';
        }

        if (Str::contains(Str::lower($html), 'manual')) {
            return 'Manual';
        }

        return null;
    }

    protected function extractColor(string $html): ?string
    {
        foreach (['Negro', 'Blanco', 'Gris', 'Azul', 'Rojo', 'Plateado'] as $color) {
            if (Str::contains(Str::lower($html), Str::lower($color))) {
                return $color;
            }
        }

        return null;
    }

    protected function extractYear(string $text): ?int
    {
        if (preg_match('/\b(19|20)\d{2}\b/', $text, $match) === 1) {
            return (int) $match[0];
        }

        return null;
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function extractBrandAndModel(string $title, int $year): array
    {
        $cleanTitle = trim(str_replace((string) $year, '', $title));
        $parts = preg_split('/\s+/', $cleanTitle) ?: [];

        if (count($parts) === 0) {
            return ['', ''];
        }

        $brand = (string) array_shift($parts);
        $model = trim(implode(' ', $parts));

        return [$brand, $model];
    }

    protected function toFloat(string $value): ?float
    {
        $normalized = str_replace(['€', ' '], '', trim($value));
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        if (! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}
