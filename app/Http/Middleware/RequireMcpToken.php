<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMcpToken
{
    /**
     * Restrict access to the web MCP endpoint using a shared secret token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('mcp.web_token');
        $header = (string) config('mcp.web_header', 'X-MCP-Token');

        if ($expected === '') {
            return response()->json([
                'message' => 'MCP access is not configured.',
            ], 403);
        }

        $provided = $request->bearerToken() ?? (string) $request->header($header);

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
