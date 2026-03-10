<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Title('List Users')]
#[Description('Lists registered users in the application. Supports optional search by name or email, and pagination via limit/offset.')]
#[IsReadOnly]
#[IsIdempotent]
class ListUsersTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $search = $request->get('search');
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $query = User::query()
            ->select(['id', 'name', 'email', 'created_at']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $users = $query->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return Response::text(json_encode([
            'success' => true,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
            ])->toArray(),
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
            'search' => $schema->string()
                ->description('Optional search term to filter users by name or email.'),

            'limit' => $schema->integer()
                ->description('Maximum number of users to return. Default: 20.')
                ->default(20),

            'offset' => $schema->integer()
                ->description('Number of users to skip for pagination. Default: 0.')
                ->default(0),
        ];
    }
}
