<?php

namespace App\Services\CarImport\Connectors;

use Illuminate\Support\Str;

class AutoScout24Connector extends AbstractHtmlConnector
{
    public function __construct()
    {
        parent::__construct(
            connectorKey: 'autoscout24',
            connectorLabel: 'AutoScout24',
            domain: 'autoscout24',
            searchUrl: config('car_import.connectors.autoscout24.search_url'),
        );
    }

    protected function isListingUrl(string $url): bool
    {
        return Str::contains($url, ['/anuncios/', '/angebote/']) && Str::contains($url, 'autoscout24');
    }
}
