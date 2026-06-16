<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('runner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date');
            $table->dateTime('departed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'date']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_run_id')->nullable()->after('company_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_run_id');
        });

        Schema::dropIfExists('delivery_runs');
    }
};
