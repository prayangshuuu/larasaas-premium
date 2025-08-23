<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenRevoke extends Command
{
    /**
     * Revoke Sanctum Personal Access Tokens.
     *
     * Examples:
     *  php artisan sanctum:token:revoke 12
     *  php artisan sanctum:token:revoke "12|plain-text-token"
     *  php artisan sanctum:token:revoke "plain-text-token-without-id"
     *  php artisan sanctum:token:revoke user:1 --all
     *  php artisan sanctum:token:revoke user:admin@example.com --name="CI Bot"
     *  php artisan sanctum:token:revoke user:admin_prayangshu --all --json --force
     */
    protected $signature = 'sanctum:token:revoke
                            {identifier : Token ID, "{id}|{token}", raw token, or "user:<id|email|username>"}
                            {--all : Revoke all tokens for the matched user (only with user:...)}
                            {--name= : Revoke tokens by name for the matched user (only with user:...)}
                            {--json : Output JSON}
                            {--force : Do not prompt for confirmation}';

    protected $description = 'Revoke Sanctum personal access tokens by ID, token, or user filter';

    public function handle(): int
    {
        $identifier = trim((string) $this->argument('identifier'));
        $json       = (bool) $this->option('json');
        $force      = (bool) $this->option('force');

        try {
            if (str_starts_with($identifier, 'user:')) {
                // Mode: revoke by user filter
                $userKey = substr($identifier, 5);
                [$ok, $payload] = $this->revokeForUser(
                    $userKey,
                    (bool) $this->option('all'),
                    (string) $this->option('name'),
                    $force
                );

                return $this->out($ok, $payload, $json);
            }

            // Mode: single token by id / id|token / raw token
            [$ok, $payload] = $this->revokeSingle($identifier, $force);

            return $this->out($ok, $payload, $json);

        } catch (\Throwable $e) {
            return $this->out(false, ['message' => $e->getMessage()], $json);
        }
    }

    /* ---------------------------------------------------------------------
     | Internals
     | ------------------------------------------------------------------ */

    /**
     * Revoke one token by:
     *  - numeric ID
     *  - "{id}|{plain-token}" (the format Sanctum shows when creating)
     *  - raw plain token (we hash and match DB "token")
     */
    private function revokeSingle(string $identifier, bool $force): array
    {
        // Case A: "{id}|{plain}" → use the ID part
        if (str_contains($identifier, '|')) {
            [$idPart] = explode('|', $identifier, 2);
            if (is_numeric($idPart)) {
                $token = PersonalAccessToken::find((int) $idPart);
                if (! $token) {
                    return [false, ['message' => "Token id {$idPart} not found."]];
                }
                return $this->deleteTokensCollect([$token], $force, "Revoke token #{$token->id}?");
            }
        }

        // Case B: numeric ID directly
        if (is_numeric($identifier)) {
            $token = PersonalAccessToken::find((int) $identifier);
            if (! $token) {
                return [false, ['message' => "Token id {$identifier} not found."]];
            }
            return $this->deleteTokensCollect([$token], $force, "Revoke token #{$token->id}?");
        }

        // Case C: raw plain token → hash and match DB "token" column (sha256)
        $hash = hash('sha256', $identifier);
        $matches = PersonalAccessToken::query()->where('token', $hash)->get();

        if ($matches->isEmpty()) {
            return [false, ['message' => 'No token matched the provided value.']];
        }

        return $this->deleteTokensCollect(
            $matches->all(),
            $force,
            "Revoke {$matches->count()} token(s) matched by raw token?"
        );
    }

    /**
     * Revoke tokens for a specific user (id, email, or username), with --all or --name filters.
     */
    private function revokeForUser(string $userKey, bool $all, ?string $name, bool $force): array
    {
        $user = $this->resolveUser($userKey);
        if (! $user) {
            return [false, ['message' => "User '{$userKey}' not found."]];
        }

        $q = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id);

        if ($name !== null && $name !== '') {
            $q->where('name', $name);
        }

        $tokens = $q->get();

        if ($tokens->isEmpty()) {
            return [true, [
                'revoked' => 0,
                'user_id' => $user->id,
                'note'    => 'No tokens matched.'
            ]];
        }

        if (! $all && $name === null) {
            // Neither --all nor --name → prevent accidental mass revoke
            $msg = "Matched {$tokens->count()} token(s) for user #{$user->id} ({$user->email}). ".
                "Re-run with --all or --name=\"...\" to revoke.";
            return [false, ['message' => $msg]];
        }

        return $this->deleteTokensCollect(
            $tokens->all(),
            $force,
            "Revoke {$tokens->count()} token(s) for user #{$user->id} ({$user->email})?"
        );
    }

    /**
     * Delete a set of tokens (with confirmation unless --force).
     *
     * @param array<int,\Laravel\Sanctum\PersonalAccessToken> $tokens
     */
    private function deleteTokensCollect(array $tokens, bool $force, string $confirmMessage): array
    {
        if (empty($tokens)) {
            return [true, ['revoked' => 0]];
        }

        if (! $force && ! $this->confirm($confirmMessage)) {
            return [false, ['message' => 'Cancelled by user.']];
        }

        $count = 0;
        foreach ($tokens as $t) {
            try {
                $t->delete();
                $count++;
            } catch (\Throwable $e) {
                // Continue; partial failures will reflect in the count.
            }
        }

        return [true, ['revoked' => $count]];
    }

    /**
     * Resolve user by ID, email, or username.
     */
    private function resolveUser(string $key): ?User
    {
        $key = trim($key);

        if (ctype_digit($key)) {
            return User::find((int) $key);
        }

        if (str_contains($key, '@')) {
            $u = User::where('email', $key)->first();
            if ($u) return $u;
        }

        // fallback to username
        return User::where('username', $key)->first();
    }

    /**
     * Output helper (table-friendly text or JSON).
     */
    private function out(bool $ok, array $payload, bool $json): int
    {
        if ($json) {
            $this->line(json_encode(['ok' => $ok] + $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            if ($ok) {
                if (isset($payload['revoked'])) {
                    $this->info("Revoked {$payload['revoked']} token(s).");
                } elseif (isset($payload['message'])) {
                    $this->info($payload['message']);
                } else {
                    $this->info('Done.');
                }
            } else {
                $this->error($payload['message'] ?? 'Failed.');
            }
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
