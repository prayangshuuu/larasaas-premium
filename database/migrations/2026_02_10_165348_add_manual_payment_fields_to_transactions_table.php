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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('sender_number')->nullable()->after('payment_method');
            $table->string('transaction_id')->nullable()->after('sender_number');
            $table->timestamp('payment_date')->nullable()->after('transaction_id');
            $table->json('payment_metadata')->nullable()->after('payment_date');
            $table->text('admin_note')->nullable()->after('payment_metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'sender_number',
                'transaction_id',
                'payment_date',
                'payment_metadata',
                'admin_note',
            ]);
        });
    }
};
