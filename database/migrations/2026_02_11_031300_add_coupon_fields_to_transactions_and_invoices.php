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
            $table->foreignId('coupon_id')->nullable()->after('invoice_id')
                  ->constrained('coupons')->nullOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0)->after('amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('user_id')
                  ->constrained('coupons')->nullOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0)->after('amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'subtotal', 'discount_amount']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'subtotal', 'discount_amount']);
        });
    }
};
