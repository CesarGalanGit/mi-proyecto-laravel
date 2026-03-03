<?php

namespace App\Services\CarImport\Connectors;

use Illuminate\Support\Str;

class WallapopConnector extends AbstractHtmlConnector
{
    public function __construct()
    {
        parent::__construct(
            connectorKey: 'wallapop',
            connectorLabel: 'Wallapop',
            domain: 'wallapop.com',
            searchUrl: config('car_import.connectors.wallapop.search_url'),
        );
    }

    protected function isListingUrl(string $url): bool
    {
        return Str::contains($url, 'wallapop.com/item/');
    }
}
