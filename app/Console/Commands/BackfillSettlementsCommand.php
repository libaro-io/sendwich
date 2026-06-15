<?php

namespace App\Console\Commands;

use App\Actions\BackfillSettlements;
use App\Exceptions\SettlementBackfillException;
use Illuminate\Console\Command;

class BackfillSettlementsCommand extends Command
{
    protected $signature = 'orders:backfill-settlements {--dry-run : Rehearse and verify, then roll back without committing}';

    protected $description = 'Migrate payout orders into the settlements table (balance-preserving, verified, reversible)';

    public function handle(BackfillSettlements $backfill): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('Dry run: migrating payouts and verifying balances, then rolling back…');
        } else {
            $this->info('Migrating payout orders into settlements…');
        }

        try {
            $stats = $backfill->execute($dryRun);
        } catch (SettlementBackfillException $exception) {
            $this->error('Verification FAILED — nothing was committed.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['Result', 'Value'], [
            ['Settlements created', $stats['settlements']],
            ['Balances preserved', 'PASSED'],
            ['Committed', $dryRun ? 'no (dry run, rolled back)' : 'yes'],
        ]);

        return self::SUCCESS;
    }
}