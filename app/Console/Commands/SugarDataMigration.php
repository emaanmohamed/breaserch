<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\SugarcrmService;
use Illuminate\Console\Command;

class SugarDataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SugarDataMigration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'script to Migration Data from Sugar CRM';

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
        $sugarService = new SugarcrmService();
        $this->info('Start >>');
//        $attachmentResults = $sugarService->migrateContactsAndGroups();
//        foreach ($attachmentResults as $step){
//            $this->info('===========================================');
//            $this->info($step);
//        }

        $Results = $sugarService->FixClientGroupRel();
        $this->info($Results);
        /*

        $this->info('===========================================');
        $this->info('start migrate email templates');
        $this->info('===========================================');
        $attachmentResults = $sugarService->migrateEmailTemplate();
        if ($attachmentResults) {
            $this->info("Email Templates inserted successfully");
        }
        $this->info('===========================================');
        $this->info('start migrate analysts');
        $this->info('===========================================');
        $attachmentResults = $sugarService->migrateAnalysts();
        if ($attachmentResults) {
            $this->info("analysts inserted successfully");
        }
        $this->info('===========================================');
        */
        //$attachmentResults = $sugarService->migrateContactsAndGroups();
        //$this->info($attachmentResults);
        $this->info('===========================================');
        $this->info('end <<');
    }
}
