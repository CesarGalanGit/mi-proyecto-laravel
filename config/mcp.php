<?php

return [
    // Requests per minute for the web MCP endpoint.
    'rate_limit_per_minute' => (int) env('MCP_RATE_LIMIT_PER_MINUTE', 60),
];
