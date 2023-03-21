<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class copyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copydata
                            {from : The Server from copy (dev, prod)}
                            {--dry : run in dry run mode}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy data from Prod or Dev';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sourceEnv = $this->argument('from');
        $dryRun = $this->option('dry');

        $host = $sourceEnv == 'prod' ? env('CONNECT_HOST_PROD') : env('CONNECT_HOST_DEV');
        $projectFolder = $sourceEnv == 'prod' ? env('PROJECT_FOLDER_PROD') : env('PROJECT_FOLDER_DEV');
        $cmd1 = 'rsync -rtvPhix --stats  mark16@'.$host.':'.$projectFolder.'/data/manuscripts '.storage_path('/app/from').' --delete '.($dryRun ? '--dry-run' : '');
        $cmd2 = 'rsync -rtvPhix --stats  mark16@'.$host.':'.$projectFolder.'/data/database.sqlite '.storage_path('/app/from').' --delete '.($dryRun ? '--dry-run' : '');

        $this->info($cmd1);
        $result1 = Process::run($cmd1);
        $this->info($result1->output());
        $resultOut1 = [
            $result1->successful(),
            $result1->failed(),
            $result1->exitCode(),
            $result1->output(),
            $result1->errorOutput(),
        ];
        $this->info($cmd2);
        $result2 = Process::run($cmd2);
        $this->info($result2->output());
        $resultOut2 = [
            $result2->successful(),
            $result2->failed(),
            $result2->exitCode(),
            $result2->output(),
            $result2->errorOutput(),
        ];

        // dd([
        //     $resultOut1,
        //     $resultOut2,
        //     $cmd1,
        //     $cmd2,
        // ]);
    }
}
