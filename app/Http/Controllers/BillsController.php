<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillsRequest;
use App\Http\Requests\UpdateBillsRequest;
use App\Repositories\BillsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Bills;
use App\Models\BillsExtension;
use App\Models\UnbundledRates;
use App\Models\UnbundledRatesExtension;
use App\Models\PaidBills;
use App\Models\Meters;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Enums\Format;
use App\Mail\BillsMail;
use Mail;
use Flash;
use Response;

class BillsController extends AppBaseController
{
    /** @var  BillsRepository */
    private $billsRepository;

    public function __construct(BillsRepository $billsRepo)
    {
        $this->middleware('auth')->except('sendUnsentEmailBills');
        $this->billsRepository = $billsRepo;
    }

    /**
     * Display a listing of the Bills.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $bills = $this->billsRepository->all();

        return view('bills.index')
            ->with('bills', $bills);
    }

    /**
     * Show the form for creating a new Bills.
     *
     * @return Response
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created Bills in storage.
     *
     * @param CreateBillsRequest $request
     *
     * @return Response
     */
    public function store(CreateBillsRequest $request)
    {
        $input = $request->all();

        $bills = $this->billsRepository->create($input);

        Flash::success('Bills saved successfully.');

        return redirect(route('bills.index'));
    }

    /**
     * Display the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.show')->with('bills', $bills);
    }

    /**
     * Show the form for editing the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.edit')->with('bills', $bills);
    }

    /**
     * Update the specified Bills in storage.
     *
     * @param int $id
     * @param UpdateBillsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBillsRequest $request)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $bills = $this->billsRepository->update($request->all(), $id);

        Flash::success('Bills updated successfully.');

        return redirect(route('bills.index'));
    }

    /**
     * Remove the specified Bills from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $this->billsRepository->delete($id);

        Flash::success('Bills deleted successfully.');

        return redirect(route('bills.index'));
    }

    public function showBill($accountNumber, $period) {
        // $billDetails = DB::connection('sqlsrvbilling')
        //     ->select("EXEC sp_SOA @ServicePeriodEnd='" . date('Y-m-d', strtotime($period)) . "', @WhereClause='Bi.AccountNumber=''" . $accountNumber . "'''");
        // dd($billDetails);
        $bills = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->first();
        $billsExtension = BillsExtension::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->first();
        $paidBill = PaidBills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->first();
        $account = AccountMaster::where('AccountNumber', $accountNumber)->first();
        $rate = UnbundledRates::where('ServicePeriodEnd', $period)
            ->where('ConsumerType', $bills->ConsumerType)
            ->first();
        $rateExtension = UnbundledRatesExtension::where('ServicePeriodEnd', $period)
            ->where('ConsumerType', $bills->ConsumerType)
            ->first();
        $meter = Meters::where('MeterNumber', $account->MeterNumber)->first();

        return view('/bills/show', [
            'bills' => $bills,
            'billsExtension' => $billsExtension,
            'paidBill' => $paidBill,
            'account' => $account,
            'rate' => $rate,
            'rateExtension' => $rateExtension,
            'meter' => $meter,
        ]);
        
    }

    public function dashboard(Request $request) {
        return view('/bills/dashboard');
    }

    public function getLastestMonthsStatistics(Request $request) {
        $latestMonth = DB::connection('sqlsrvbilling')
            ->table('Bills')
            ->orderByDesc('ServicePeriodEnd')
            ->select('ServicePeriodEnd')
            ->first();

        $data = [];

        if ($latestMonth != null) {
            $totalStats = DB::connection('sqlsrvbilling')
                ->table('Bills')
                ->whereRaw("ServicePeriodEnd='" . $latestMonth->ServicePeriodEnd . "'")
                ->select(
                    DB::raw("SUM(PowerKWH) AS TotalKwh"),
                    DB::raw("SUM(NetAmount) AS TotalAmount"),
                    DB::raw("COUNT(AccountNumber) AS TotalBilledAccounts"),
                    DB::raw("SUM(DistributionDemandAmt + DistributionSystemAmt + SupplyRetailCustomerAmt + SupplySystemAmt + MeteringRetailCustomerAmt + MeteringSystemAmt + LifelineSubsidyAmt) AS TotalDSM"),
                )
                ->first();

            $data['LatestMonth'] = $latestMonth->ServicePeriodEnd != null ? date('Y-m-d', strtotime($latestMonth->ServicePeriodEnd)) : null;
            $data['TotalStats'] = $totalStats;
        } else {
            $data['LatestMonth'] = null;
            $data['TotalStats'] = [];
        }

        return response()->json($data, 200);
    }

    public function getBillsAnnualStats(Request $request) {
        $year = $request['Year'];
        $from = date('Y-m-d', strtotime($year . '-01-01'));
        $to = date('Y-m-d', strtotime($year . '-12-31'));

        $thisYearData = [];
        $prevYearData = [];
        $labels = [];

        $pieData = DB::connection('sqlsrvbilling')
            ->table('Bills')
            ->whereRaw("ServicePeriodEnd BETWEEN '" . $from . "' AND '" . $to . "'")
            ->select(
                "ConsumerType",
                DB::raw("SUM(PowerKWH) AS Consumption"),   
            )
            ->groupBy("ConsumerType")
            ->get();

        for ($i=1; $i<13; $i++) {
            $thisYear = date('Y-m-d', strtotime($year . '-' . $i . '-01'));
            $prevYear = date('Y-m-d', strtotime((intval($year)-1) . '-' . $i . '-01'));
            
            $tyQuery = DB::connection('sqlsrvbilling')
                ->table('Bills')
                ->whereRaw("ServicePeriodEnd='" . $thisYear . "'")
                ->select(
                    DB::raw("'" . $thisYear . "' AS Period"),
                    DB::raw("SUM(PowerKWH) AS TotalKwh"),
                    DB::raw("SUM(NetAmount) AS TotalAmount"),
                    DB::raw("COUNT(AccountNumber) AS TotalBilledAccounts"),
                    DB::raw("SUM(DistributionDemandAmt + DistributionSystemAmt + SupplyRetailCustomerAmt + SupplySystemAmt + MeteringRetailCustomerAmt + MeteringSystemAmt + LifelineSubsidyAmt) AS TotalDSM"),
                )
                ->first();

            $pyQuery = DB::connection('sqlsrvbilling')
                ->table('Bills')
                ->whereRaw("ServicePeriodEnd='" . $prevYear . "'")
                ->select(
                    DB::raw("'" . $prevYear . "' AS Period"),
                    DB::raw("SUM(PowerKWH) AS TotalKwh"),
                    DB::raw("SUM(NetAmount) AS TotalAmount"),
                    DB::raw("COUNT(AccountNumber) AS TotalBilledAccounts"),
                    DB::raw("SUM(DistributionDemandAmt + DistributionSystemAmt + SupplyRetailCustomerAmt + SupplySystemAmt + MeteringRetailCustomerAmt + MeteringSystemAmt + LifelineSubsidyAmt) AS TotalDSM"),
                )
                ->first();

            array_push($thisYearData, $tyQuery);
            array_push($prevYearData, $pyQuery);
            array_push($labels, date('F', strtotime($thisYear)));
        }

        $data = [
            'PreviousYear' => $prevYearData,
            'ThisYear' => $thisYearData,
            'Labels' => $labels,
            'PieData' => $pieData,
        ];

        return response()->json($data, 200);
    }

    public function downloadPDF($accountNumber, $billingMonth) {
        Pdf::view('/bills/bill_to_pdf')
            ->save(public_path() . "/pdfs/" . $accountNumber . "-" . $billingMonth . ".pdf");

        return redirect(route('accountMasters.view-account', [$accountNumber]));
    }

    public function sendPDFMail($accountNumber, $billingMonth) {
        Pdf::view('/bills/bill_to_pdf')
            ->save(public_path() . "/pdfs/" . $accountNumber . "-" . $billingMonth . ".pdf");

        // send email
        Mail::to('julzasdf123456@gmail.com')->send(new BillsMail([
            'title' => 'The Title',
            'body' => 'The Body',
        ]));

        return redirect(route('accountMasters.view-account', [$accountNumber]));
    }

    public function createPDFBill(Request $request) {
        $accountNumber = $request['AccountNumber']; 
        $period = $request['BillingMonth'];

        $accountMaster = AccountMaster::where('AccountNumber', $accountNumber)->first();
        $meterInfo = Meters::where('MeterNumber', $accountMaster != null ? $accountMaster->MeterNumber : '')->first();
        $bill = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->first();
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

        $prevPeriod = date('Y-m-01', strtotime($period . ' -1 month'));
        $billPrev = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $prevPeriod)
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
                'billPrev' => $billPrev,
            ])
            ->format(Format::A4)
            // ->paperSize(215.9, 330.2, 'mm')
            ->margins(0, 0, 0, 0)
            ->save(public_path() . "/pdfs/" . $accountNumber . "-" . $period . ".pdf");

        return response()->json([
            'File' => $accountNumber . "-" . $period . ".pdf",
            'Email' => $accountMaster->Email,
        ], 200);
    }

    public function testViewPDFBill($accountNumber, $period) {
        $accountMaster = AccountMaster::where('AccountNumber', $accountNumber)->first();
        $meterInfo = Meters::where('MeterNumber', $accountMaster != null ? $accountMaster->MeterNumber : '')->first();
        $bill = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $period)
            ->first();
        $rates = DB::connection('sqlsrvbilling')
            ->table('UnbundledRates')
            ->where('ConsumerType', AccountMaster::validateConsumerTypes($accountMaster->ConsumerType))
            ->where('ServicePeriodEnd', $period)
            ->first();
        $billExtension = DB::connection('sqlsrvbilling')
            ->table('BillsExtension')
            ->where('AccountNumber', $accountNumber)
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

        $prevPeriod = date('Y-m-01', strtotime($period . ' -1 month'));
        $billPrev = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriodEnd', $prevPeriod)
            ->first();

        // dd($billPrev);

        return view('/bills/bill_to_pdf', [
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
            'billPrev' => $billPrev,
        ]);
    }

    public function sendUnsentEmailBills(Request $request) {
        $bill = DB::connection("sqlsrvbilling")
            ->table("Bills")
            ->leftJoin("AccountMaster", "AccountMaster.AccountNumber", "=", "Bills.AccountNumber")
            // ->whereRaw("AccountMaster.Email IS NOT NULL AND Bills.EmailSent IS NULL")
            ->whereRaw("AccountMaster.Email LIKE '%julzasdf%' AND Bills.EmailSent IS NULL")
            ->select("Bills.*")
            ->orderByDesc('ServicePeriodEnd')
            ->first();

        if ($bill != null) {
            $accountNumber = $bill->AccountNumber;
            $period = date('Y-m-d', strtotime($bill->ServicePeriodEnd));

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
                
            $prevPeriod = date('Y-m-01', strtotime($period . ' -1 month'));
            $billPrev = Bills::where('AccountNumber', $accountNumber)
                ->where('ServicePeriodEnd', $prevPeriod)
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
                    'billPrev' => $billPrev,
                ])
                ->format(Format::A4)
                // ->paperSize(215.9, 330.2, 'mm')
                ->margins(0, 0, 0, 0)
                ->save(public_path() . "/pdfs/" . $accountNumber . "-" . $period . ".pdf");

            return response()->json([
                'File' => $accountNumber . "-" . $period . ".pdf",
                'Email' => $accountMaster->Email,
                'BillingMonth' => date('F Y', strtotime($period)),
                'BillingMonthRaw' => date('Y-m-d', strtotime($period)),
                'AccountName' => $accountMaster->ConsumerName,
                'AccountNumber' => $accountNumber,
            ], 200);
        } else {
            return response()->json('No unsent bill', 200);
        }
    }
}
