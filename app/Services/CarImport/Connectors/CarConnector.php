<?php

namespace App\Services\CarImport\Connectors;

interface CarConnector
{
    public function key(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchListings(int $limit): array;
}
