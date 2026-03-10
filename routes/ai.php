<?php

use App\Mcp\Servers\AppServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| MCP Server Routes
|--------------------------------------------------------------------------
|
| Web server: accessible via HTTP at /mcp/app (for remote AI clients).
| Local server: accessible via Artisan command (for local AI assistants).
|
*/

// Web-accessible MCP server (HTTP Streamable transport)
Mcp::web('/mcp/app', AppServer::class)
    ->middleware(['api', \App\Http\Middleware\McpHeaders::class]);

// Local MCP server (stdio transport, for local AI tools like Boost/Cursor)
Mcp::local('app', AppServer::class);
