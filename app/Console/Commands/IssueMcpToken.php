<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class IssueMcpToken extends Command
{
    protected $signature = 'mcp:token
                            {user : User ID or email}
                            {--name=mcp : Token name}
                            {--keep-existing : Keep existing tokens with the same name}
                            {--ability=* : Token ability (repeatable). Defaults to *}';

    protected $description = 'Genera un token de Laravel Sanctum para autenticar el endpoint MCP web.';

    public function handle(): int
    {
        $userArg = trim((string) $this->argument('user'));

        $user = ctype_digit($userArg)
            ? User::query()->find((int) $userArg)
            : User::query()->where('email', $userArg)->first();

        if (! $user) {
            $this->error('Usuario no encontrado. Pasa un ID o un email existente.');

            return self::FAILURE;
        }

        $name = trim((string) $this->option('name'));
        $name = $name !== '' ? $name : 'mcp';

        if (! (bool) $this->option('keep-existing')) {
            $user->tokens()->where('name', $name)->delete();
        }

        $abilities = collect((array) $this->option('ability'))
            ->map(fn ($value): string => trim((string) $value))
            ->filter()
            ->values()
            ->all();

        if ($abilities === []) {
            $abilities = ['*'];
        }

        $token = $user->createToken($name, $abilities);

        $this->line($token->plainTextToken);
        $this->newLine();
        $this->info('Usalo asi: Authorization: Bearer <token>');

        return self::SUCCESS;
    }
}
