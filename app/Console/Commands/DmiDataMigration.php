<?php

namespace App\Console\Commands;

use App\Console\Commands\Services\DmiService;
use App\Services\DocumentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DmiDataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DmiDataMigration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'script to Migration Data from DMI';

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
        $dmiService = new DmiService();
        $this->info('Start >>');
        $this->info('===========================================');
        $this->info('research sectors');
        $this->info('===========================================');
        $sectorResults = $dmiService->migrateSectors();
        if ($sectorResults) {
            $this->info("Sectors inserted successfully");
        }

        $this->info('===========================================');
        $this->info('research languages');
        $this->info('===========================================');
        $langResults = $dmiService->migrateLanguages();
        if ($langResults) {
            $this->info("Langs inserted successfully");
        }


        $this->info('===========================================');
        $this->info('research companies');
        $this->info('===========================================');
        $companiesRecords = $dmiService->migrateCompanies();
        if ($companiesRecords) {
            $this->info("Companies inserted successfully");
        }
        $this->info('===========================================');
        $this->info('research countries');
        $this->info('===========================================');
        $countriesResults = $dmiService->migrateCountries();
        if ($countriesResults) {
            $this->info("Countries inserted successfully");
        }
        $this->info('===========================================');
        $this->info('research reportType');
        $this->info('===========================================');
        $reportTypeResults = $dmiService->migrateReportType();
        if ($reportTypeResults) {
            $this->info("ReportType inserted successfully");
        }
        $this->info('===========================================');
        $this->info('research docs');
        $this->info('===========================================');
        $docsResults = $dmiService->migrateResearchDocs();
        if ($docsResults) {
            $this->info("docs inserted successfully");
        }
        $this->info('===========================================');
         $this->info('research attachments');
         $this->info('===========================================');
         $attachmentResults = $dmiService->migrateAttachments();
        if ($attachmentResults) {
            $this->info("docs inserted successfully :: LastID:".$attachmentResults);
        }else{
            $this->info($attachmentResults);
        }
        $this->info('===========================================');


        $this->info('end <<');

    }
}
