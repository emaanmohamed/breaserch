<?php


namespace App\Console\Commands\Services;


use Illuminate\Support\Facades\DB;



class DmiService
{
    public function migrateSectors()
    {
        $sectors            = [];
        $researchpubsectors = DB::connection('dmi')->table('researchpubsectors')->where('deleted', '=', 0)->get();
        foreach ($researchpubsectors as $item) {
            $sectors[] = [
                'id'      => $item->id,
                'name_en' => $item->name,
                'name_ar' => $item->nameAR
            ];
        }
        DB::connection('mysql')->table('sectors')->truncate();
        $sectorRecords = DB::connection('mysql')->table('sectors')->insert($sectors);
        return $sectorRecords;

    }

    public function migrateLanguages()
    {
        $languages            = [];
        $researchLanguages = DB::connection('dmi')->table('researchlanguages')->get();
        foreach ($researchLanguages as $item) {
            $languages[] = [
                'id'   => $item->id,
                'name' => $item->name,
            ];
        }
        DB::connection('mysql')->table('lookup_languages')->truncate();
        $langsRecords = DB::connection('mysql')->table('lookup_languages')->insert($languages);
        return $langsRecords;

    }

    public function migrateCompanies()
    {
        $companies            = [];
        $researchpubcompanies = DB::connection('dmi')->table('researchpubcompanies')->where('deleted', '=', 0)->get();
        foreach ($researchpubcompanies as $item) {
            $companies[] = [
                'id'          => $item->id,
                'name'        => $item->name,
                'sector_id'   => $item->idSector,
                'country_id'  => $item->idCountry,
                'description' => $item->description,
            ];
        }
        DB::connection('mysql')->table('lookup_companies')->truncate();
        $companyRecords = DB::connection('mysql')->table('lookup_companies')->insert($companies);
        return $companyRecords;

    }

    public function migrateCountries()
    {
        $countries         = [];
        $researchCountries = DB::connection('dmi')->table('researchcountries')->get();
        foreach ($researchCountries as $item) {
            $countries[] = [
                'id'      => $item->id,
                'name_en' => $item->name
            ];
        }
        DB::connection('mysql')->table('lookup_countries')->truncate();
        $countryRecords = DB::connection('mysql')->table('lookup_countries')->insert($countries);
        return $countryRecords;
    }

    public function migrateReportType()
    {
        $reportTypes         = [];
        $researchReportTypes = DB::connection('dmi')->table('researchreporttype')->where('deleted', '=', 0)->get();
        foreach ($researchReportTypes as $item) {
            $reportTypes[] = [
                'id'         => $item->id,
                'name_en'    => $item->name,
                'short_name' => $item->api_name
            ];
        }
        DB::connection('mysql')->table('lookup_report_types')->truncate();
        $reportTypeRecords = DB::connection('mysql')->table('lookup_report_types')->insert($reportTypes);
        return $reportTypeRecords;
    }

    public function migrateResearchDocs()
    {
        $docs  = [];
        $lastid = DB::table('settings')->first()->docs_last_id;


        $researchDocs = DB::connection('dmi')->table('researchdocs')->where('id','>',$lastid)->orderBy('id')->take(1000)->get();

        foreach ($researchDocs as $item) {
            $docs[] = [
                'id'                => $item->id,
                'updated_at'        => $item->LAST_UPDATED,
                'created_at'        => $item->date,
                'country_id'        => $item->country,
                'report_type_id'    => $item->reportType,
                'description'       => $item->subject,
                'subject'           => $item->subject,
                'analyst_id'        => $item->analyst,
                'sector_id'         => $item->sector,
                'company_id'        => $item->company,
                'html_content'      => $item->htmlContent,
                'email_template_id' => $item->email_template_id,
                'language_id'       => $item->language
            ];
        }

        DB::beginTransaction();
        try {
            $last_item = end($docs);
            if(isset($last_item['id']) && $last_item['id'] > 0){
                DB::table('settings')->update(
                    ['docs_last_id' => $last_item['id']]
                );
            }else{
                throw new \Exception('Not Found New Results');
            }

            // DB::connection('mysql')->table('research_docs')->truncate();
            $docsRecords = DB::connection('mysql')->table('research_docs')->insert($docs);
            DB::commit();
            unset($docs);
            return $last_item['id'];
        }catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $e->getMessage();
        }

    }

    public function migrateAttachments()
    {
        $attachments  = [];
        $attachRel = [];
        $lastid = DB::table('settings')->first()->attach_last_id;
        $researchattachments = DB::connection('dmi')
            ->table('researchattachments')
            ->where('deleted', '=', 0)
            ->where('id','>',$lastid)
            ->orderBy('id')->take(5000)->get();
        foreach ($researchattachments as $item) {
            $attachments[] = [
                'id'                 => $item->id,
                'original_file_name' => $item->file_name,
                'server_file_name'   => $item->file_path,
                'description'        => $item->file_description,
                'created_at'         => $item->date_created,
                'updated_at'         => $item->date_modified
            ];

            $attachRel[] = [
                'research_doc_id' => $item->research_id,
                'attachment_id' => $item->id
            ];


        }
        //DB::connection('mysql')->table('attachments')->truncate();
        //DB::connection('mysql')->table('research_attachments_rel')->truncate();



        DB::beginTransaction();
        try {
            $last_item = end($attachments);
            if(isset($last_item['id']) && $last_item['id'] > 0){
                DB::table('settings')->update(
                    ['attach_last_id' => $last_item['id']]
                );
            }else{
                throw new \Exception('Not Found New Results');

            }

            // DB::connection('mysql')->table('research_docs')->truncate();
            $attachmentRecords = DB::connection('mysql')->table('attachments')->insert($attachments);
            $attachRelRecords = DB::connection('mysql')->table('research_attachments_rel')->insert($attachRel);

            DB::commit();
            unset($attachmentRecords);
            unset($attachRelRecords);
            return $last_item['id'];
        }catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $e->getMessage();
        }
    }





}
