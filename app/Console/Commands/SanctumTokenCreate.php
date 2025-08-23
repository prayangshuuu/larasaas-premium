<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SanctumTokenCreate extends Command
{
    /**
     * Create a Sanctum Personal Access Token for a user.
     *
     * Examples:
     *  php artisan sanctum:token:create 1 --name="CLI"
     *  php artisan sanctum:token:create admin@example.com --abilities=read --abilities=write --expires="30d"
     *  php artisan sanctum:token:create admin_prayangshu --name="dev" --abilities="*"
     *  php artisan sanctum:token:create 2 --json
     */
    protected $signature = 'sanctum:token:create
                            {user : User ID, email, or username}
                            {--name=CLI : Token name}
                            {--abilities=* : One or more abilities; repeat or comma-separate. Use * for all.}
                            {--expires= : Expiry (e.g. 30d, 12h, 90m, or full datetime like "2026-01-01 23:59")}
                            {--json : Output JSON only}
                            {--force : Do not prompt confirmations}';

    protected $description = 'Create a Sanctum personal access token for a user';

    public function handle(): int
    {
        $userArg = trim((string) $this->argument('user'));

        $user = $this->resolveUser($userArg);
        if (! $user) {
            return $this->failJsonOrText('User not found. Use ID, email, or username.');
        }

        $name = (string) $this->option('name');

        $abilities = $this->parseAbilities($this->option('abilities'));
        if (empty($abilities)) {
            $abilities = ['*'];
        }

        $expiresAt = $this->parseExpires((string) ($this->option('expires') ?? ''));

        if (! $this->option('force') && ! $this->option('json')) {
            $this->line('');
            $this->info('About to create token:');
            $this->line(sprintf('  User     : #%d %s (%s)',
                $user->id,
                $user->name ?? '-',
                $user->email ?? '-'
            ));
            $this->line("  Name     : {$name}");
            $this->line('  Abilities: ' . implode(',', $abilities));
            $this->line('  Expires  : ' . ($expiresAt ? $expiresAt->toDateTimeString() : 'never'));

            if (! $this->confirm('Proceed?', true)) {
                $this->warn('Cancelled.');
                return self::INVALID;
            }
        }

        // Sanctum: createToken(string $name, array $abilities = ['*'], \DateTimeInterface $expiresAt = null)
        $token   = $user->createToken($name, $abilities, $expiresAt);
        $plain   = $token->plainTextToken;
        $model   = $token->accessToken; // Laravel\Sanctum\PersonalAccessToken

        if ($this->option('json')) {
            $this->line(json_encode([
                'ok'           => true,
                'token'        => $plain,
                'token_id'     => $model->id,
                'user_id'      => $user->id,
                'user_email'   => $user->email,
                'name'         => $model->name,
                'abilities'    => $model->abilities,
                'last_used_at' => $model->last_used_at?->toJSON(),
                'expires_at'   => $model->expires_at?->toJSON(),
                'created_at'   => $model->created_at?->toJSON(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return self::SUCCESS;
        }

        $this->line('');
        $this->info('✓ Token created!');
        $this->warn('Copy it now; you will not be able to see it again:');
        $this->line('');
        $this->line($plain);
        $this->line('');

        $this->table(
            ['Token ID', 'User', 'Name', 'Abilities', 'Expires'],
            [[
                $model->id,
                sprintf('#%d %s', $user->id, $user->email ?? '-'),
                $model->name,
                implode(',', (array) $model->abilities),
                $model->expires_at?->toDateTimeString() ?: 'never',
            ]]
        );

        return self::SUCCESS;
    }

    /**
     * Resolve user by ID, email, or username.
     */
    private function resolveUser(string $arg): ?User
    {
        // Numeric → ID
        if (ctype_digit($arg)) {
            return User::find((int) $arg);
        }

        // Contains @ → email
        if (str_contains($arg, '@')) {
            return User::where('email', $arg)->first();
        }

        // Otherwise assume username
        return User::where('username', $arg)->first();
    }

    /**
     * Accepts:
     *  - repeated: --abilities=read --abilities=write
     *  - CSV:      --abilities="read,write"
     *  - star:     --abilities="*"
     */
    private function parseAbilities(mixed $opt): array
    {
        if ($opt === null) return ['*'];

        $vals = is_array($opt) ? $opt : [$opt];

        // If any is exactly "*", collapse to ["*"]
        foreach ($vals as $v) {
            if (trim((string) $v) === '*') {
                return ['*'];
            }
        }

        $out = [];
        foreach ($vals as $chunk) {
            foreach (explode(',', (string) $chunk) as $a) {
                $a = trim($a);
                if ($a !== '') $out[] = $a;
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * Parse expiry:
     *  - Relative TTLs: 30d, 12h, 90m
     *  - Full datetime: "2026-01-01 23:59", "2026-01-01T23:59:00"
     *  - Empty/invalid → null (no expiry)
     */
    private function parseExpires(?string $raw): ?Carbon
    {
        if (! $raw) return null;
        $raw = trim($raw);
        if ($raw === '') return null;

        // TTL like 30d / 12h / 90m
        if (preg_match('/^(\d+)\s*([mhd])$/i', $raw, $m)) {
            $num  = (int) $m[1];
            $unit = strtolower($m[2]);
            return match ($unit) {
                'm' => Carbon::now()->addMinutes($num),
                'h' => Carbon::now()->addHours($num),
                'd' => Carbon::now()->addDays($num),
                default => null,
            };
        }

        // Try datetime parse
        try {
            return Carbon::parse($raw);
        } catch (\Throwable $e) {
            if (! $this->option('json')) {
                $this->warn('Could not parse --expires. Token will not expire.');
            }
            return null;
        }
    }

    private function failJsonOrText(string $message): int
    {
        if ($this->option('json')) {
            $this->line(json_encode(['ok' => false, 'message' => $message], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->error($message);
        }
        return self::FAILURE;
    }
}
