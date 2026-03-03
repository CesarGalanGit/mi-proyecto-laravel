<?php

namespace App\Services\CarImport\Connectors;

use Illuminate\Support\Str;

class CochesNetConnector extends AbstractHtmlConnector
{
    public function __construct()
    {
        parent::__construct(
            connectorKey: 'cochesnet',
            connectorLabel: 'Coches.net',
            domain: 'coches.net',
            searchUrl: config('car_import.connectors.cochesnet.search_url'),
        );
    }

    protected function isListingUrl(string $url): bool
    {
        if (! Str::contains($url, 'coches.net')) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';

        return preg_match('/-[0-9]{5,}\.htm$/i', $path) === 1;
    }
}
