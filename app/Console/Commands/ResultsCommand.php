<?php

namespace App\Console\Commands;

use App\Services\ResultGenerator;
use Illuminate\Console\Command;

class ResultsCommand extends Command
{
    protected $signature = 'app:results {--backfill : Backfills previous time keys}';

    protected $description = 'Calculate voting results';

    public function handle(ResultGenerator $resultGenerator): void
    {
        if ($this->option('backfill')) {
            $resultGenerator->backfillTimeKeys();
        } else {
            $resultGenerator->performFullUpdate();
        }
    }
}
