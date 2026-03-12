<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class McpHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('mcp/*')) {
            // Disable buffering for Nginx (Render proxy) and Apache
            $response->headers->set('X-Accel-Buffering', 'no');
        }

        if ($response instanceof StreamedResponse) {
            // Ensure SSE headers for streaming
            if ($response->headers->get('Content-Type') === 'text/event-stream') {
                $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->headers->set('Connection', 'keep-alive');
            }

            // Clean output buffers to prevent delays in streaming.
            // Avoid closing PHPUnit buffers when running tests.
            if (! app()->runningUnitTests()) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }
        }

        return $response;
    }
}
