<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Extra/receipt items have no product and cannot exist under the old NOT NULL schema.
        // Export them before deleting so the rollback is reversible and no data is silently lost.
        $orphanOrders = DB::table('orders')->whereNull('product_id')->get();

        if ($orphanOrders->isNotEmpty()) {
            $exportPath = storage_path('app/migration-backups');

            if (!is_dir($exportPath)) {
                mkdir($exportPath, 0755, true);
            }

            $handle = fopen($exportPath . '/orders_null_product_' . now()->format('Ymd_His') . '.csv', 'w');
            fputcsv($handle, array_keys((array) $orphanOrders->first()));

            foreach ($orphanOrders as $order) {
                fputcsv($handle, (array) $order);
            }

            fclose($handle);

            DB::table('orders')->whereNull('product_id')->delete();
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('label');
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};