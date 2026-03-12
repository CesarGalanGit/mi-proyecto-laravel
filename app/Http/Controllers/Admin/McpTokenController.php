<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;

class McpTokenController
{
    public function show(): View
    {
        return view('admin.mcp-token');
    }

    public function store(Request $request): View
    {
        $user = $request->user();

        $abilities = $user !== null && $user->can('manage-users')
            ? ['*']
            : ['cars:list'];

        $user->tokens()->where('name', 'mcp')->delete();

        $token = $user->createToken('mcp', $abilities);

        return view('admin.mcp-token', [
            'plainTextToken' => $token->plainTextToken,
        ]);
    }
}
