<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceConnectionCrew;
use App\Models\Towns;
use App\Models\Barangays;
use App\Models\Bills;
use App\Models\BillsExtension;
use App\Models\UnbundledRates;
use App\Models\UnbundledRatesExtension;
use App\Models\PaidBills;
use App\Models\Meters;
use App\Models\AccountMaster;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Enums\Format;

class OtherData extends Controller {
    public $successStatus = 200;

    public function getTowns() {
        $towns = Towns::all();

        if ($towns == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($towns, $this->successStatus); 
        } 
    }

    public function getBarangays() {
        $barangays = Barangays::all();

        if ($barangays == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($barangays, $this->successStatus); 
        } 
    }

    public function getAllCrew() {
        $crew = ServiceConnectionCrew::get();

        if ($crew) {
            return response()->json($crew, $this->successStatus);
        } else {
            return response()->json(['response' => 'No data'], 404);
        }
    }

    // auto email
    public function sendUnsentEmailBills(Request $request) {
        $bill = DB::connection("sqlsrvbilling")
            ->table("Bills")
            ->leftJoin("AccountMaster", "AccountMaster.AccountNumber", "=", "Bills.AccountNumber")
            // ->whereRaw("AccountMaster.Email IS NOT NULL AND Bills.EmailSent IS NULL")
            ->whereRaw("Bills.AccountNumber='0101062555' AND Bills.EmailSent IS NULL")
            ->select("Bills.*")
            ->orderByDesc('ServicePeriodEnd')
            ->first();

        if ($bill != null) {
            $accountNumber = $bill->AccountNumber;
            $period = $bill->ServicePeriodEnd;

            $accountMaster = AccountMaster::where('AccountNumber', $accountNumber)->first();
            $meterInfo = Meters::where('MeterNumber', $accountMaster != null ? $accountMaster->MeterNumber : '')->first();

            $billExtension = DB::connection('sqlsrvbilling')
                ->table('BillsExtension')
                ->where('AccountNumber', $accountNumber)
                ->where('ServicePeriodEnd', $period)
                ->first();
            $rates = DB::connection('sqlsrvbilling')
                ->table('UnbundledRates')
                ->where('ConsumerType', AccountMaster::validateConsumerTypes($accountMaster->ConsumerType))
                ->where('ServicePeriodEnd', $period)
                ->first();
            $ratesExtension = DB::connection('sqlsrvbilling')
                ->table('UnbundledRatesExtension')
                ->where('ConsumerType', AccountMaster::validateConsumerTypes($accountMaster->ConsumerType))
                ->where('ServicePeriodEnd', $period)
                ->first();
            $arrears = DB::connection('sqlsrvbilling')
                ->table('Bills')
                ->whereRaw("AccountNumber='" . $accountNumber . "' AND AccountNumber NOT IN (SELECT AccountNumber FROM PaidBills WHERE AccountNumber=Bills.AccountNumber AND ServicePeriodEnd=Bills.ServicePeriodEnd) 
                    AND ServicePeriodEnd NOT IN ('" . $period . "')")
                ->select(
                    DB::raw("COUNT(AccountNumber) AS ArrearsCount"),
                    DB::raw("SUM(NetAmount) AS ArrearsTotal"),
                )
                ->first();
            $billsDcrRevisionView = DB::connection('sqlsrvbilling')
                ->table('BillsForDCRRevision')
                ->where('AccountNumber', $accountNumber)
                ->where('ServicePeriodEnd', $period)
                ->first();

            Pdf::view('/bills/bill_to_pdf', [
                    'period' => $period,
                    'accountNumber' => $accountNumber,
                    'accountMaster' => $accountMaster,
                    'meterInfo' => $meterInfo,
                    'bill' => $bill,
                    'rates' => $rates,
                    'billExtension' => $billExtension,
                    'ratesExtension' => $ratesExtension,
                    'arrears' => $arrears,
                    'billsDcrRevisionView' => $billsDcrRevisionView,
                ])
                ->format(Format::A4)
                // ->paperSize(215.9, 330.2, 'mm')
                ->margins(0, 0, 0, 0)
                ->save(public_path() . "/pdfs/" . $accountNumber . "-" . $period . ".pdf");

            return response()->json([
                'File' => $accountNumber . "-" . $period . ".pdf",
                'Email' => $accountMaster->Email,
                'BillingMonth' => date('F Y', strtotime($period)),
                'AccountName' => $accountMaster->ConsumerName,
                'AccountNumber' => $accountNumber,
            ], 200);
        } else {
            return response()->json(['res' => 'Bill not found!'], 404);
        }
    }

    public function updateBillsEmailSent(Request $request) {
        $accountNumber = $request['AccountNumber'];
        $period = $request['ServicePeriodEnd'];
        $status = $request['Status'];

        Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->update(['EmailSent' => $status]);
        
        return response()->json(['res' => 'ok'], 200);
    }
}