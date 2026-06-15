<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: ensure every order that has a runner is linked to a delivery_run.
        // This mirrors what BackfillDeliveryRuns does, but inline so timestamps are
        // available when we copy them in step 2.
        DB::statement("
            INSERT INTO delivery_runs (company_id, runner_id, date, created_at, updated_at)
            SELECT
                o.company_id,
                o.paid_by,
                DATE(o.date),
                NOW(),
                NOW()
            FROM orders o
            WHERE o.paid_by IS NOT NULL
              AND (o.product_id IS NULL OR o.product_id != 65)
              AND NOT EXISTS (
                  SELECT 1 FROM delivery_runs dr
                  WHERE dr.company_id = o.company_id
                    AND dr.runner_id  = o.paid_by
                    AND DATE(dr.date) = DATE(o.date)
              )
            GROUP BY o.company_id, o.paid_by, DATE(o.date)
            ON DUPLICATE KEY UPDATE updated_at = updated_at
        ");

        DB::statement("
            UPDATE orders o
            JOIN delivery_runs dr
              ON  dr.company_id = o.company_id
              AND dr.runner_id  = o.paid_by
              AND DATE(dr.date) = DATE(o.date)
            SET o.delivery_run_id = dr.id
            WHERE o.delivery_run_id IS NULL
              AND o.paid_by IS NOT NULL
        ");

        // Step 2: copy the historical timestamps from orders to their delivery_run.
        // departed_at  → the latest departed_at among the run's orders.
        // delivered_at → the latest delivered_at among the run's orders.
        DB::statement("
            UPDATE delivery_runs dr
            JOIN (
                SELECT
                    delivery_run_id,
                    MAX(departed_at)  AS max_departed,
                    MAX(delivered_at) AS max_delivered
                FROM orders
                WHERE delivery_run_id IS NOT NULL
                GROUP BY delivery_run_id
            ) agg ON agg.delivery_run_id = dr.id
            SET
                dr.departed_at  = COALESCE(dr.departed_at,  agg.max_departed),
                dr.delivered_at = COALESCE(dr.delivered_at, agg.max_delivered)
        ");

        // Step 3: now it is safe to drop the redundant columns.
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['departed_at', 'delivered_at']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('departed_at')->nullable();
        });

        // Restore from delivery_run for orders that are linked to one.
        DB::statement("
            UPDATE orders o
            JOIN delivery_runs dr ON dr.id = o.delivery_run_id
            SET
                o.departed_at  = dr.departed_at,
                o.delivered_at = dr.delivered_at
        ");
    }
};
