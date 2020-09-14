<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class contactMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:contactMigration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script to Migration Data from Sugarcrm AND DMI';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       // $prospect_lists = DB::connection('sugarcrm')->table('prospect_lists')->get();
        $researchpubsectors = DB::connection('dmi')->table('researchpubsectors')->get();
        //
        $this->info('Start script !');
//        $this->info('===========================================');
//        $this->info('prospect lists');
//        $this->info('===========================================');
//        $this->info($prospect_lists);
        $this->info('===========================================');
        $this->info('research sectors');
        $this->info('===========================================');
        $this->info($researchpubsectors);
        $this->info('===========================================');
        $this->info('end script !');

    }
}
