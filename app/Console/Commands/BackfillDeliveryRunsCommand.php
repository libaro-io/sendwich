<?php

namespace App\Console\Commands;

use App\Actions\BackfillDeliveryRuns;
use App\Exceptions\DeliveryRunBackfillException;
use Illuminate\Console\Command;

class BackfillDeliveryRunsCommand extends Command
{
    protected $signature = 'orders:backfill-runs {--dry-run : Rehearse and verify, then roll back without committing}';

    protected $description = 'Backfill delivery_runs from existing orders (lossless, verified, reversible)';

    public function handle(BackfillDeliveryRuns $backfill): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('Dry run: building and verifying delivery runs, then rolling back…');
        } else {
            $this->info('Backfilling delivery runs…');
        }

        try {
            $stats = $backfill->execute($dryRun);
        } catch (DeliveryRunBackfillException $exception) {
            $this->error('Verification FAILED — nothing was committed.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['Result', 'Value'], [
            ['Delivery runs', $stats['runs']],
            ['Orders linked', $stats['orders_linked']],
            ['Verification', 'PASSED'],
            ['Committed', $dryRun ? 'no (dry run, rolled back)' : 'yes'],
        ]);

        if ($dryRun) {
            $this->info('✓ Dry run passed. The real backfill would be lossless. No data was changed.');
        } else {
            $this->info('✓ Backfill committed and verified.');
        }

        return self::SUCCESS;
    }
}
