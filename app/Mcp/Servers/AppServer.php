<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateCarListingTool;
use App\Mcp\Tools\CreateUserTool;
use App\Mcp\Tools\ListCarsTool;
use App\Mcp\Tools\ListUsersTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Car Marketplace MCP Server')]
#[Version('1.0.0')]
#[Instructions('This MCP server provides tools to manage a car marketplace application. You can create users, create car listings (advertisements), and list/search both users and car listings. All write operations validate input data before persisting to the database.')]
class AppServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        CreateUserTool::class,
        CreateCarListingTool::class,
        ListUsersTool::class,
        ListCarsTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
