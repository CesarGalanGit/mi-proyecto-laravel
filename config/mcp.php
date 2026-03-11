<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Web MCP Authentication
    |--------------------------------------------------------------------------
    |
    | The web MCP server is exposed over HTTP (e.g. /mcp/app). Protect it with
    | a shared secret so only trusted clients can access it.
    |
    */

    'web_token' => env('MCP_WEB_TOKEN', ''),

    // The header name to read the token from when not using Bearer auth.
    'web_header' => env('MCP_WEB_HEADER', 'X-MCP-Token'),

    // Requests per minute for the web MCP endpoint.
    'rate_limit_per_minute' => (int) env('MCP_RATE_LIMIT_PER_MINUTE', 60),
];
