<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_reminder_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day'); // 0 = Sunday … 6 = Saturday
            $table->timestamps();
            $table->unique(['company_id', 'day']);
        });

        DB::table('companies')
            ->whereNotNull('reminder_days')
            ->get(['id', 'reminder_days'])
            ->each(function (object $company) {
                $days = json_decode($company->reminder_days, true) ?? [];
                foreach ($days as $day) {
                    DB::table('company_reminder_days')->insertOrIgnore([
                        'company_id' => $company->id,
                        'day'        => $day,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('reminder_days');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('reminder_days')->nullable();
        });

        DB::table('company_reminder_days')
            ->get(['company_id', 'day'])
            ->groupBy('company_id')
            ->each(function ($rows, int $companyId) {
                DB::table('companies')
                    ->where('id', '=', $companyId)
                    ->update(['reminder_days' => json_encode($rows->pluck('day')->values()->all())]);
            });

        Schema::dropIfExists('company_reminder_days');
    }
};
