<?php

namespace App\Console\Commands;

use App\Importers\Importer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ImportLegacyDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-db {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from a legacy database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->argument('year');

        if ($year) {
            $this->importYear($year);
            return;
        }

        // This repo is not yet set up for working as an archive
//        $years = range(2011, 2023);
//        foreach ($years as $year) {
//            $this->importYear((string)$year);
//        }
    }

    public function importYear(string $year)
    {
        $this->info("Importing $year");

        $class = "App\\Importers\\Vga$year";

        if (!class_exists($class)) {
            $this->error("No importer found for $year");
            return;
        }

        Model::unguard();

        /** @var Importer $importer */
        $importer = new $class();
        $importer->database();
//        $this->info("Awards");
//        $importer->awards();
//        $this->info("Nominees");
//        $importer->nominees();
//        $this->info("Files");
//        $importer->files();
//        $this->info("Permissions");
//        $importer->permissions();
//        $this->info("Users");
//        $importer->users();
    }
}
