<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenList extends Command
{
    /**
     * List Sanctum Personal Access Tokens.
     *
     * Examples:
     *  php artisan sanctum:token:list
     *  php artisan sanctum:token:list 1
     *  php artisan sanctum:token:list admin@example.com --active
     *  php artisan sanctum:token:list admin_prayangshu --expired --limit=50 --json
     */
    protected $signature = 'sanctum:token:list
                            {user? : (optional) User ID, email, or username to filter}
                            {--active : Only show non-expired tokens}
                            {--expired : Only show expired tokens}
                            {--limit=100 : Maximum rows to return}
                            {--json : Output JSON instead of a table}';

    protected $description = 'List Sanctum personal access tokens (optionally filtered)';

    public function handle(): int
    {
        $userArg     = $this->argument('user');
        $onlyActive  = (bool) $this->option('active');
        $onlyExpired = (bool) $this->option('expired');
        $limit       = (int) $this->option('limit') ?: 100;

        if ($onlyActive && $onlyExpired) {
            return $this->failOut('Choose either --active or --expired (not both).');
        }

        $query = PersonalAccessToken::query()->with('tokenable');

        // Filter by user (ID/email/username) if provided
        if ($userArg) {
            $user = $this->resolveUser((string) $userArg);
            if (! $user) {
                return $this->failOut("User '{$userArg}' not found.");
            }
            $query->where('tokenable_type', User::class)
                ->where('tokenable_id', $user->id);
        }

        // Active / expired filters
        $now = CarbonImmutable::now();
        if ($onlyActive) {
            $query->where(function (Builder $q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            });
        } elseif ($onlyExpired) {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', $now);
        }

        $tokens = $query->orderByDesc('id')->limit($limit)->get();

        // Map for output
        $rows = $tokens->map(function (PersonalAccessToken $t) use ($now) {
            $user   = $t->tokenable;
            $active = is_null($t->expires_at) || $t->expires_at->gt($now);

            return [
                'token_id'     => $t->id,
                'user_id'      => $user?->id,
                'user_email'   => $user?->email,
                'name'         => $t->name,
                'abilities'    => implode(',', (array) $t->abilities),
                'last_used_at' => $t->last_used_at?->toDateTimeString(),
                'expires_at'   => $t->expires_at?->toDateTimeString() ?? 'never',
                'active'       => $active ? 1 : 0,
                'created_at'   => $t->created_at?->toDateTimeString(),
            ];
        })->values();

        if ($this->option('json')) {
            $this->line(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return self::SUCCESS;
        }

        if ($rows->isEmpty()) {
            $this->info('No tokens found.');
            return self::SUCCESS;
        }

        $this->table(
            ['Token ID', 'User ID', 'Email', 'Name', 'Abilities', 'Last Used', 'Expires', 'Active', 'Created'],
            $rows->map(fn ($r) => [
                $r['token_id'],
                $r['user_id'],
                $r['user_email'],
                $r['name'],
                $r['abilities'],
                $r['last_used_at'],
                $r['expires_at'],
                $r['active'] ? 'yes' : 'no',
                $r['created_at'],
            ])->all()
        );

        return self::SUCCESS;
    }

    /**
     * Resolve user by ID, email, or username.
     */
    private function resolveUser(string $key): ?User
    {
        if (ctype_digit($key)) {
            return User::find((int) $key);
        }
        if (str_contains($key, '@')) {
            return User::where('email', $key)->first();
        }
        return User::where('username', $key)->first();
    }

    /**
     * Emit an error and return FAILURE without colliding with Command::fail().
     */
    private function failOut(string $message): int
    {
        if ($this->option('json')) {
            $this->line(json_encode(['ok' => false, 'message' => $message], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->error($message);
        }
        return self::FAILURE;
    }
}
