<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Hash;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;

#[Title('Create User')]
#[Description('Creates a new user in the application with name, email and password. Returns the created user data as JSON.')]
class CreateUserTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:128',
        ], [
            'name.required' => 'You must provide a user name. Example: "John Doe".',
            'email.required' => 'You must provide a valid email address. Example: "john@example.com".',
            'email.unique' => 'This email address is already registered. Try a different one.',
            'password.required' => 'You must provide a password with at least 8 characters.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return Response::text(json_encode([
            'success' => true,
            'message' => "User '{$user->name}' created successfully.",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Full name of the user to create. Example: "María García".')
                ->required(),

            'email' => $schema->string()
                ->description('Email address for the user. Must be unique. Example: "maria@example.com".')
                ->required(),

            'password' => $schema->string()
                ->description('Password for the user account. Minimum 8 characters.')
                ->required(),
        ];
    }
}
