<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. system_settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable(); // JSON or text
            $table->timestamps();
        });

        // Seed initial settings
        DB::table('system_settings')->insert([
            ['key' => 'subscription_module_enabled', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'stripe_payment_enabled',      'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('stripe_price_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('USD');
            $table->enum('interval', ['month', 'year']);
            $table->json('features')->nullable(); // Stores limits like {"max_projects": 10}
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_subscription_id')->unique();
            $table->string('status'); // active, canceled, past_due, incomplete
            $table->timestamp('current_period_end')->nullable();
            $table->timestamps();
        });

        // 4. invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_invoice_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['paid', 'pending', 'cancelled', 'void'])->default('pending');
            $table->string('invoice_pdf_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('system_settings');
    }
};
