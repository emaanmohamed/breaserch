<?php

namespace App\Services;

use App\Models\ClientType;

class ClientTypeService {

    public function getClientTypes()
    {
        $clientTypes = ClientType::all();
        return $clientTypes;
    }


}
