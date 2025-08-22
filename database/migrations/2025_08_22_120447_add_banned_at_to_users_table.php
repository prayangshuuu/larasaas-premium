<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create a flexible audit_logs table for admin/user actions.
     */
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();

                // Who performed the action (admin or user). Nullable for system events.
                $table->foreignId('actor_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->index();

                // What was acted on (generic polymorphic target: model class + id)
                $table->string('target_type', 120)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->index(['target_type', 'target_id']);

                // What happened
                $table->string('action', 120)->index(); // e.g. "user.ban", "user.delete", "user.promote"
                $table->text('description')->nullable(); // human-friendly notes

                // Context
                $table->string('ip_address', 45)->nullable();        // IPv4/IPv6
                $table->string('user_agent', 1024)->nullable();
                $table->json('metadata')->nullable();                // any extra details (old/new values, etc.)

                $table->timestamps(); // created_at is most important; updated_at kept for completeness
                $table->index('created_at'); // fast timeline queries
            });
        }
    }

    /**
     * Drop the audit_logs table.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
