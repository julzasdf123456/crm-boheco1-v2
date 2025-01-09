<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DisconnectionSchedules;
use App\Models\IDGenerator;
use App\Models\AccountMaster;
use App\Models\DisconnectionRoutes;
use App\Models\DisconnectionData;
use App\Models\Bills;
use App\Models\PaidBills;
use App\Http\Requests\CreateReadingsRequest;

class Billing extends Controller {
    public $successStatus = 200;

    public function searchAccounts(Request $request) {
        $params = $request['search'];

        if (isset($params)) {
            $data = DB::connection('sqlsrvbilling')
                ->table('AccountMaster')
                ->whereRaw("(AccountNumber LIKE '%" . $params . "%' OR ConsumerName LIKE '%" . $params . "%' OR MeterNumber LIKE '%" . $params . "%')")
                ->orderBy('ConsumerName')
                ->paginate(15);
        } else {
            $data = DB::connection('sqlsrvbilling')
                ->table('AccountMaster')
                ->orderBy('AccountNumber')
                ->paginate(15);
        }

        return response()->json($data, 200);
    }
}