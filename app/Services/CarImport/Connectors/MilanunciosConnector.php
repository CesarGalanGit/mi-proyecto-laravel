<?php

namespace App\Services\CarImport\Connectors;

use Illuminate\Support\Str;

class MilanunciosConnector extends AbstractHtmlConnector
{
    public function __construct()
    {
        parent::__construct(
            connectorKey: 'milanuncios',
            connectorLabel: 'Milanuncios',
            domain: 'milanuncios.com',
            searchUrl: config('car_import.connectors.milanuncios.search_url'),
        );
    }

    protected function isListingUrl(string $url): bool
    {
        return Str::contains($url, 'milanuncios.com') && Str::contains($url, '.htm');
    }
}
