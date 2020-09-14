<?php


namespace App\Console\Commands\Services;


use Illuminate\Support\Facades\DB;

class SugarcrmService
{
    public function migrateEmailTemplate()
    {
        $arr            = [];
        $items = DB::connection('sugarcrm')->table('email_templates')->where('deleted', '=', 0)->get();
        foreach ($items as $item) {
            $arr[] = [
                'title'                 => $item->subject,
                'email_template'        => $item->body_html,
                'created_at'            => $item->date_entered,
                'updated_at'            => $item->date_modified,
                'migrated_template_id'  => $item->id,
            ];
        }
        DB::connection('mysql')->table('email_templates')->truncate();
        $isInserted = DB::connection('mysql')->table('email_templates')->insert($arr);
        return $isInserted;
    }

    public function migrateAnalysts()
    {
        $arr            = [];
        $items = DB::connection('sugarcrm')->table('ra_researchanalysts')->where('deleted', '=', 0)->get();
        foreach ($items as $item) {
            $arr[] = [
                'name'                 => $item->first_name.' '.$item->last_name,
                'migrated_id'           => $item->id,
            ];
        }
        DB::connection('mysql')->table('analyst')->truncate();
        $isInserted = DB::connection('mysql')->table('analyst')->insert($arr);
        return $isInserted;
    }

    public function migrateContactsAndGroups(){
        /*
         * Accounts / Contacts
         * */

        $migrateGroups              = $this->MigrateGroups();
        $migrateGroupsRelations     =  $this->MigrateGroupsRelations();

        $prospect_lists             = DB::connection('mysql')->table('groups')->get();
        $prospect_lists_prospects   = DB::connection('sugarcrm')->table('prospect_lists_prospects')
            ->whereIn('prospect_list_id', $prospect_lists->pluck('migrated_id')->toArray())
            ->where('deleted', '=', 0)
           // ->where('related_type', ['Accounts'])
            ->get();

        $migrateAccounts        = $this->MigrateAccounts($prospect_lists_prospects->pluck('related_id')->toArray());

        $Accounts_Contact_rel   = DB::connection('sugarcrm')->table('accounts_contacts')
            ->whereIn('account_id', $prospect_lists_prospects->pluck('related_id')->toArray())
            ->where('deleted', '=', 0)
            ->get();

        $list = array_merge($prospect_lists_prospects->pluck('related_id')->toArray(),$Accounts_Contact_rel->pluck('contact_id')->toArray());
        $migrateContacts = $this->MigrateContacts($list);

        $migrateAccountRel = $this->MigrateAccountRel();

        $migrateEmailAddresses = $this->MigrateEmailAddresses();

        //return $prospect_lists_prospects->toJson(JSON_PRETTY_PRINT);


        return [$migrateGroups,$migrateGroupsRelations,$migrateAccounts,$migrateContacts,$migrateAccountRel,$migrateEmailAddresses];
    }


    public function MigrateGroups(){
        $prospect_lists = DB::connection('sugarcrm')->table('prospect_lists')->where('deleted', '=', 0)->get();
        $items = [];
        foreach ($prospect_lists AS $item){
            $items[] = [
                'name' => $item->name,
                'migrated_id' => $item->id,
            ];
        }
        DB::connection('mysql')->table('groups')->truncate();
        DB::connection('mysql')->table('groups')->insert($items);
        unset($items);
        return 'prospect_lists inserted successfully';
    }
    public function MigrateGroupsRelations(){
        $prospect_lists = DB::connection('mysql')->table('groups')->get();
        $prospect_lists_prospects = DB::connection('sugarcrm')->table('prospect_lists_prospects')
            ->whereIn('prospect_list_id', $prospect_lists->pluck('migrated_id')->toArray())
            ->where('deleted', '=', 0)
            ->where('related_type', '=', 'Contacts')
            ->get();
        $items = [];
        foreach ($prospect_lists_prospects AS $item){
            $items[] = [
                'migrated_group_id' => $item->prospect_list_id,
                'migrated_client_id' => $item->related_id,
            ];
        }
        DB::connection('mysql')->table('client_group_rel')->truncate();
        DB::connection('mysql')->table('client_group_rel')->insert($items);
        unset($items);
        return 'client group rel inserted successfully';
    }

    public function FixClientGroupRel(){
        $ByGroup = DB::connection('mysql')->table('groups')->select('id','migrated_id')->get();
        foreach ($ByGroup as $item){
            DB::connection('mysql')->table('client_group_rel')
                ->where('migrated_group_id',$item->migrated_id)
                ->update([
                    'group_id' => $item->id
                ]);
        }

        $ByClient = DB::connection('mysql')->table('clients')->select('id','migrated_contact_id')->get();
        foreach ($ByClient as $item){
            DB::connection('mysql')->table('client_group_rel')
                ->where('migrated_client_id',$item->migrated_contact_id)
                ->update([
                    'client_id' => $item->id
                ]);
        }

        return 'Group Client Rel Fixed Successfully';
    }

    public function MigrateAccounts($listIds){
        $Accounts = DB::connection('sugarcrm')->table('Accounts')
            ->whereIn('id',$listIds )
            ->where('deleted', '=', 0)
            ->get();
        $items = [];
        foreach ($Accounts AS $item){
            $items[] = [
                'name' => $item->name,
                'phone_number' => $item->phone_office,
                'notes' => $item->website,
                'migrated_account_id' => $item->id,
                'address' => $item->billing_address_street,
                'created_at' => $item->date_entered,
                'updated_at' => $item->date_modified,
            ];
        }
        DB::connection('mysql')->table('institutions')->truncate();
        DB::connection('mysql')->table('institutions')->insert($items);
        unset($items);
        return 'Accounts inserted successfully';

    }

    public function MigrateContacts($listIds){
        $Account_Contacts = DB::connection('sugarcrm')->table('contacts')
            ->whereIn('id', $listIds)
            ->where('deleted', '=', 0)
            ->get();
        $items = [];
        foreach ($Account_Contacts AS $item){
            $items[] = [
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'title' => $item->title,
                'phone_number' => $item->phone_work,
                'mobile_number' => $item->phone_mobile,
                'notes' => $item->description,
                'country' => $item->primary_address_country,
                'address' => $item->primary_address_street.'/'.$item->primary_address_city.'/'.$item->primary_address_state,
                'created_at' => $item->date_entered,
                'updated_at' => $item->date_modified,
                'migrated_contact_id' => $item->id,
            ];
        }

        DB::connection('mysql')->table('clients')->truncate();
        DB::connection('mysql')->table('clients')->insert($items);
        unset($items);
        return 'Contacts inserted successfully';

    }

    public function MigrateAccountRel(){
        $accounts = DB::connection('mysql')->table('institutions')->get();
        foreach ($accounts as $account){
            $Accounts_Contact_rel = DB::connection('sugarcrm')->table('accounts_contacts')
                ->where('account_id', $account->migrated_account_id)
                ->where('deleted', '=', 0)
                ->get();
            DB::connection('mysql')->table('clients')
                ->whereIn('migrated_contact_id',$Accounts_Contact_rel->pluck('contact_id')->toArray())
                ->update([
                    'institution_id' => $account->id
                ]);
        }

        return 'Account Contact Rel inserted successfully';
    }

    public function MigrateEmailAddresses(){
        $clients = DB::connection('mysql')->table('clients')->get();
        $email_addr_bean_rel = DB::connection('sugarcrm')->table('email_addr_bean_rel')
            ->whereIn('bean_id',$clients->pluck('migrated_contact_id')->toArray())->get();
        $email_addrs = DB::connection('sugarcrm')->table('email_addresses')
            ->whereIn('id',$email_addr_bean_rel->pluck('email_address_id')->toArray())
            ->where('deleted', '=', 0)
            ->get();
        $items = [];
        foreach ($email_addrs AS $item){
            $items[] = [
                'email_address' => $item->email_address,
                'migrated_id'   => $item->id,
                'created_at'    => $item->date_created,
                'updated_at'    => $item->date_modified,
            ];
        }
        DB::connection('mysql')->table('client_email')->truncate();
        DB::connection('mysql')->table('client_email')->insert($items);
        unset($items);
        foreach ($clients as $client){
            $Accounts_Contact_rel = DB::connection('sugarcrm')->table('email_addr_bean_rel')
                ->where('bean_id', $client->migrated_contact_id)
                ->where('bean_module', 'Contacts')
                ->where('deleted', '=', 0)
                ->orderByDesc('primary_address')
                ->get();
            DB::connection('mysql')->table('client_email')
                ->whereIn('migrated_id',$Accounts_Contact_rel->pluck('email_address_id')->toArray())
                ->update([
                    'client_id' => $client->id
                ]);
        }
        return 'Contact Emails Rel inserted successfully';
    }



}
