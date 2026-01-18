<?php

namespace App\Console\Commands;

use App\Services\ResultGenerator;
use Illuminate\Console\Command;

class ResultsCommand extends Command
{
    protected $signature = 'app:results';

    protected $description = 'Calculate voting results';

    public function handle(ResultGenerator $resultGenerator): void
    {
        $resultGenerator->performFullUpdate();
    }
}
