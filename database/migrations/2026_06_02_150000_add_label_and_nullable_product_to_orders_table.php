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
        Schema::table('orders', function (Blueprint $table) {
            // Extra items scanned from a receipt are not tied to a known product.
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('label')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('label');
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};