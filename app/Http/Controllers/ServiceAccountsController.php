<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceAccountsRequest;
use App\Http\Requests\UpdateServiceAccountsRequest;
use App\Repositories\ServiceAccountsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\Towns;
use App\Models\Barangays;
use App\Models\ServiceConnectionInspections;
use App\Models\ServiceConnectionAccountTypes;
use App\Models\ServiceAccounts;
use App\Models\MeterReaders;
use App\Models\BillingMeters;
use App\Models\AccountNameHistory;
use App\Models\BillingTransformers;
use App\Models\ServiceConnectionMtrTrnsfrmr;
use App\Models\User;
use App\Models\Users;
use App\Models\Bills;
use App\Models\Readings;
use App\Models\Collectibles;
use App\Models\IDGenerator;
use App\Models\PaidBills;
use App\Models\TransactionIndex;
use App\Models\ArrearsLedgerDistribution;
use App\Models\DisconnectionHistory;
use App\Models\Tickets;
use App\Models\ChangeMeterLogs;
use App\Models\PrePaymentBalance;
use App\Models\PrePaymentTransHistory;
use App\Models\AccountLocationHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Flash;
use Response;

class ServiceAccountsController extends AppBaseController
{
    /** @var  ServiceAccountsRepository */
    private $serviceAccountsRepository;

    public function __construct(ServiceAccountsRepository $serviceAccountsRepo)
    {
        $this->middleware('auth');
        $this->serviceAccountsRepository = $serviceAccountsRepo;
    }

    /**
     * Display a listing of the ServiceAccounts.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request) {
        if ($request['params'] == null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo', 
                            'Billing_ServiceAccounts.AccountCount',  
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),   
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        } elseif ($request['params'] == null && $request['oldaccount'] != null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay')
                        // ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } elseif ($request['params'] != null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay')
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        // ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay')
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        }  

        return view('service_accounts.index', ['serviceAccounts' => $serviceAccounts]);
    }

    /**
     * Show the form for creating a new ServiceAccounts.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_accounts.create');
    }

    /**
     * Store a newly created ServiceAccounts in storage.
     *
     * @param CreateServiceAccountsRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceAccountsRequest $request)
    {
        $input = $request->all();

        $sc = ServiceAccounts::find($input['id']);

        if ($sc != null) {
            $serviceAccounts = $this->serviceAccountsRepository->update($request->all(), $sc->id);

            return redirect(route('serviceAccounts.account-migration-step-two', [$input['id']]));
        } else {
            $serviceAccounts = $this->serviceAccountsRepository->create($input);

            Flash::success('Service Accounts saved successfully.');

            return redirect(route('serviceAccounts.account-migration-step-two', [$serviceAccounts->id]));
        }
        
    }

    /**
     * Display the specified ServiceAccounts.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $transformer = BillingTransformers::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $bills = DB::table('Billing_Bills')
            ->where('Billing_Bills.AccountNumber', $id)
            ->select('Billing_Bills.*',
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND (Status IS NULL OR Status='Application')) AS ORNumber"),
                DB::raw("(SELECT TOP 1 ORDate FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND (Status IS NULL OR Status='Application')) AS ORDate"),
                DB::raw("(SELECT TOP 1 id FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND (Status IS NULL OR Status='Application')) AS PaidBillId"))
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();
        
        // ARREARS
        $collectibles = Collectibles::where('AccountNumber', $id)->first();

        $arrearsLedger = ArrearsLedgerDistribution::where('AccountNumber', $id)
            ->orderBy('ServicePeriod')
            ->get();

        $checkLedger = ArrearsLedgerDistribution::where('AccountNumber', $id)
            ->whereNull('IsPaid')
            ->orderBy('ServicePeriod')
            ->get();

        $billArrears = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_Bills.AccountNumber', $id)
            ->whereRaw("Billing_Bills.id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber='" . $id . "')")
            // ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->where('AccountNumber', $id)->pluck('ObjectSourceId'))
            ->select('Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_Bills.*')
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        $unmergedArrears = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_Bills.AccountNumber', $id)
            ->whereNull('Billing_Bills.MergedToCollectible')
            ->whereRaw("Billing_Bills.id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber='" . $id . "')")
            // ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->where('AccountNumber', $id)->pluck('Cashier_PaidBills.ObjectSourceId'))
            ->select('Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_Bills.*')
            ->orderByDesc('Billing_Bills.ServicePeriod')
            // ->offset(1)
            ->get();

        $disconnectionHistory = DisconnectionHistory::where('AccountNumber', $id)
            ->orderByDesc('created_at')
            ->get();

        $arrearTransactionHistory = TransactionIndex::where('ObjectId', $id)
            ->whereIn('Source', ['Arrears Termed Ledger', 'Arrears Collectible'])
            ->orderByDesc('created_at')
            ->get();

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        // tickets
        $complaints = DB::table('CRM_Tickets')
            ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')                
            ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
            ->select('CRM_Tickets.id as id',
                            'CRM_TicketsRepository.Name as Ticket', 
                            'CRM_Tickets.Status',  
                            'CRM_Tickets.Sitio as Sitio', 
                            'CRM_Tickets.Reason', 
                            'CRM_Tickets.Ticket as TicketID', 
                            'CRM_Tickets.created_at')
            ->where(function ($query) {
                                $query->where('CRM_Tickets.Trash', 'No')
                                    ->orWhereNull('CRM_Tickets.Trash');
                            }) 
            ->where('CRM_Tickets.AccountNumber', $id)    
            ->whereNotIn('CRM_Tickets.Ticket', Tickets::getViolations())         
            ->orderByDesc('CRM_Tickets.created_at')
            ->get();

        // violations
        $violations = DB::table('CRM_Tickets')
            ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')                
            ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
            ->select('CRM_Tickets.id as id',
                            'CRM_TicketsRepository.Name as Ticket', 
                            'CRM_Tickets.Status',  
                            'CRM_Tickets.Sitio as Sitio', 
                            'CRM_Tickets.Ticket as TicketID', 
                            'CRM_Tickets.created_at')
            ->where(function ($query) {
                                $query->where('CRM_Tickets.Trash', 'No')
                                    ->orWhereNull('CRM_Tickets.Trash');
                            }) 
            ->where('CRM_Tickets.AccountNumber', $id)    
            ->whereIn('CRM_Tickets.Ticket', Tickets::getViolations())         
            ->orderByDesc('CRM_Tickets.created_at')
            ->get();

        // transaction balance
        $prepaymentBalance = PrePaymentBalance::where('AccountNumber', $id)->first();
        $prepaymentHistory = DB::table('Billing_PrePaymentTransactionHistory')
            ->leftJoin('users', 'Billing_PrePaymentTransactionHistory.UserId', '=', 'users.id')
            ->where('Billing_PrePaymentTransactionHistory.AccountNumber', $id)
            ->select('Billing_PrePaymentTransactionHistory.*',
                'users.name')
            ->orderByDesc('Billing_PrePaymentTransactionHistory.created_at')
            ->get();

        $meterHistory = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            // ->offset(1)
            ->get();

        $changeNameHistory = DB::table('Billing_AccountNameHistory')
            ->leftJoin('users', 'Billing_AccountNameHistory.UserId', '=', 'users.id')
            ->where('Billing_AccountNameHistory.AccountNumber', $id)
            ->select('users.name',
                'Billing_AccountNameHistory.*')
            ->orderByDesc('Billing_AccountNameHistory.created_at')
            ->get();

        $relocationHistory = DB::table('Billing_AccountLocationHistory')        
            ->leftJoin('CRM_Towns', 'Billing_AccountLocationHistory.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_AccountLocationHistory.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_AccountLocationHistory.Purok',
                'Billing_AccountLocationHistory.AreaCode',
                'Billing_AccountLocationHistory.SequenceCode',
                'Billing_AccountLocationHistory.RelocationDate',
                'CRM_Towns.Town',
                'CRM_Barangays.Barangay')
            ->where('Billing_AccountLocationHistory.AccountNumber', $id)
            ->orderByDesc('Billing_AccountLocationHistory.created_at')
            ->get();

        // ALL TRANSACTIONS
        $paidBills = DB::table('Cashier_PaidBills')
            ->where("AccountNumber", $id)
            ->select('id', 'ORNumber', 'ORDate', 'NetAmount', DB::raw("'BILLS PAYMENT' AS PaymentType"), 'Source', 'created_at');
        $allPayments = DB::table('Cashier_TransactionIndex')
            ->where("AccountNumber", $id)
            ->select('id', 'ORNumber', 'ORDate', 'Total', DB::raw("'OTHER PAYMENT' AS PaymentType"), 'Source', 'created_at')
            ->union($paidBills)
            ->orderByDesc('created_at')
            ->get();

        // reading history
        $readings = DB::table('Billing_Readings')
            ->leftJoin('users', 'Billing_Readings.MeterReader', '=', 'users.id')
            ->select('Billing_Readings.*', 'users.name')
            ->where('Billing_Readings.AccountNumber', $id)
            ->orderByDesc('Billing_Readings.ServicePeriod')
            ->get();

        return view('service_accounts.show', [
            'serviceAccounts' => $serviceAccounts,
            'meters' => $meters,
            'transformer' => $transformer,
            'bills' => $bills,
            'collectibles' => $collectibles,
            'arrearsLedger' => $arrearsLedger,
            'billArrears' => $billArrears,
            'unmergedArrears' => $unmergedArrears,
            'checkLedger' => $checkLedger,
            'arrearTransactionHistory' => $arrearTransactionHistory,
            'disconnectionHistory' => $disconnectionHistory,
            'complaints' => $complaints,
            'violations' => $violations,
            'prepaymentBalance' => $prepaymentBalance,
            'prepaymentHistory' => $prepaymentHistory,
            'meterHistory' => $meterHistory,
            'changeNameHistory' => $changeNameHistory,
            'relocationHistory' => $relocationHistory,
            'ledger' => $allPayments,
            'readings' => $readings,
        ]);
    }

    /**
     * Show the form for editing the specified ServiceAccounts.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceAccount = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccount)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        return view('service_accounts.edit')->with('serviceAccount', $serviceAccount);
    }

    /**
     * Update the specified ServiceAccounts in storage.
     *
     * @param int $id
     * @param UpdateServiceAccountsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceAccountsRequest $request)
    {
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        $serviceAccounts = $this->serviceAccountsRepository->update($request->all(), $id);

        Flash::success('Service Accounts updated successfully.');

        return redirect(route('serviceAccounts.show', [$id]));
    }

    /**
     * Remove the specified ServiceAccounts from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        $this->serviceAccountsRepository->delete($id);

        Flash::success('Service Accounts deleted successfully.');

        return redirect(route('serviceAccounts.index'));
    }

    public function pendingAccounts(Request $request) {
        $office = isset($request['Office']) ? $request['Office'] : 'All';

        if ($office == 'All') {
            $serviceConnections = DB::table('CRM_ServiceConnections')
                ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
                ->select('CRM_ServiceConnections.id as id',
                                'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                'CRM_ServiceConnections.Status as Status',
                                'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                'CRM_ServiceConnections.DateTimeOfEnergization', 
                                'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                'CRM_ServiceConnections.AccountCount as AccountCount',  
                                'CRM_ServiceConnections.ConnectionApplicationType',  
                                'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                                'CRM_ServiceConnectionAccountTypes.Alias',
                                'CRM_ServiceConnections.AccountNumber',  
                                'CRM_ServiceConnections.updated_at',  
                                'CRM_ServiceConnections.Sitio as Sitio', 
                                'CRM_Towns.Town as Town',
                                'CRM_Barangays.Barangay as Barangay')
                ->whereRaw("(CRM_ServiceConnections.Trash IS NULL OR CRM_ServiceConnections.Trash='No') AND CRM_ServiceConnections.Status IN ('Energized', 'Approved For Change Name')") 
                ->whereRaw("CRM_ServiceConnections.created_at > '2023-02-28' AND CRM_ServiceConnections.AccountType NOT IN " . ServiceConnections::getBapaAccountCodes() . " AND ConnectionApplicationType NOT IN ('Rewiring', 'Change Name')")
                ->orderBy('CRM_ServiceConnections.DateTimeOfEnergization')
                ->get();
        } else {
            $serviceConnections = DB::table('CRM_ServiceConnections')
                ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
                ->select('CRM_ServiceConnections.id as id',
                                'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                'CRM_ServiceConnections.Status as Status',
                                'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                'CRM_ServiceConnections.DateTimeOfEnergization', 
                                'CRM_ServiceConnections.AccountCount as AccountCount',  
                                'CRM_ServiceConnections.ConnectionApplicationType',  
                                'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                                'CRM_ServiceConnectionAccountTypes.Alias',
                                'CRM_ServiceConnections.AccountNumber',  
                                'CRM_ServiceConnections.Sitio as Sitio',   
                                'CRM_ServiceConnections.updated_at',
                                'CRM_Towns.Town as Town',
                                'CRM_Barangays.Barangay as Barangay')
                ->whereRaw("(CRM_ServiceConnections.Trash IS NULL OR CRM_ServiceConnections.Trash='No') AND CRM_ServiceConnections.Status IN ('Energized', 'Approved For Change Name')") 
                ->whereRaw("CRM_ServiceConnections.Office='" . $office . "' AND CRM_ServiceConnections.created_at > '2023-02-28' AND CRM_ServiceConnections.AccountType NOT IN " . ServiceConnections::getBapaAccountCodes() . " AND ConnectionApplicationType NOT IN ('Rewiring', 'Change Name')")
                ->orderBy('CRM_ServiceConnections.DateTimeOfEnergization')
                ->get();
        }      

        return view('/service_accounts/pending_accounts', ['serviceConnections' => $serviceConnections]);
    }

    public function confirmChangeName($id) {
        $serviceConnection = ServiceConnections::find($id);

        if ($serviceConnection != null) {
            if ($serviceConnection->AccountNumber != null) {
                $serviceAccount = DB::table('Billing_ServiceAccounts')
                    ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                    ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                    ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
                    ->select('Billing_ServiceAccounts.id',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.AccountType',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.AreaCode',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.ForDistribution',
                            'Billing_ServiceAccounts.Organization',
                            'Billing_ServiceAccounts.OrganizationParentAccount',
                            'Billing_ServiceAccounts.Main',
                            'Billing_ServiceAccounts.GroupCode',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.Coreloss',
                            'Billing_ServiceAccounts.ConnectionDate',
                            'Billing_ServiceAccounts.ServiceConnectionId',
                            'Billing_ServiceAccounts.SeniorCitizen',
                            'Billing_ServiceAccounts.Evat5Percent',
                            'Billing_ServiceAccounts.Ewt2Percent',
                            'Billing_ServiceAccounts.Contestable',
                            'Billing_ServiceAccounts.NetMetered',
                            'Billing_ServiceAccounts.AccountRetention',
                            'Billing_ServiceAccounts.DurationInMonths',
                            'Billing_ServiceAccounts.AccountExpiration',
                            'CRM_Towns.Town',
                            'CRM_Barangays.Barangay',
                            'users.name as MeterReader')
                    ->where('Billing_ServiceAccounts.id', $serviceConnection->AccountNumber)
                    ->first();
            } else {
                $serviceAccount = null;
            }

            return view('/service_accounts/confirm_change_name', [
                'serviceConnection' => $serviceConnection,
                'serviceAccount' => $serviceAccount,
            ]);
        } else {
            return abort(404, 'Service Connection Data Not Found');
        }
    }

    public function accountMigration($id) {
        $serviceConnection = ServiceConnections::find($id);
        $serviceAccount = ServiceAccounts::where('ServiceConnectionId', $id)->first();
        $serviceConnectionInspection = ServiceConnectionInspections::where('ServiceConnectionId', $id)->orderByDesc('created_at')->first();
        $towns = Towns::where('id', $serviceConnection->Town)->pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $serviceConnection->Town)->pluck('Barangay', 'id');
        $accountTypes = ServiceConnectionAccountTypes::all();
        $meterReaders = User::role('Meter Reader Inhouse')->get();

        return view('/service_accounts/account_migration',
            [
                'serviceConnection' => $serviceConnection,
                'inspection' => $serviceConnectionInspection,
                'town' => $towns,
                'barangays' => $barangays,
                'accountTypes' => $accountTypes,
                'meterReaders' => $meterReaders,
                'serviceAccount' => $serviceAccount,
            ]
        );
    }

    public function accountMigrationStepTwo($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $serviceConnection = ServiceConnections::find($serviceAccount->ServiceConnectionId);
        $meters = BillingMeters::find($serviceAccount->MeterDetailsId);
        $meterAndTransformer = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $serviceAccount->ServiceConnectionId)->first();

        return view('/service_accounts/account_migration_step_two', [
            'serviceAccount' => $serviceAccount,
            'meterAndTransformer' => $meterAndTransformer,
            'serviceConnection' => $serviceConnection,
            'meters' => $meters
        ]);
    }

    public function accountMigrationStepThree($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $serviceConnection = ServiceConnections::find($serviceAccount->ServiceConnectionId);
        $meters = BillingMeters::find($serviceAccount->MeterDetailsId);
        $meterAndTransformer = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $serviceAccount->ServiceConnectionId)->first();
        $bapa = DB::table('Billing_ServiceAccounts')
            ->select('OrganizationParentAccount')
            ->groupBy('OrganizationParentAccount')
            ->orderBy('OrganizationParentAccount')
            ->get();

        return view('/service_accounts/account_migration_step_three', [
            'serviceAccount' => $serviceAccount,
            'meterAndTransformer' => $meterAndTransformer,
            'serviceConnection' => $serviceConnection,
            'meters' => $meters,
            'bapa' => $bapa,
        ]);
    }

    public function updateStepOne($id) {
        $serviceAccount = ServiceAccounts::find($id);
        $towns = Towns::where('id', $serviceAccount->Town)->pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $serviceAccount->Town)->pluck('Barangay', 'id');
        $accountTypes = ServiceConnectionAccountTypes::all();
        $meterReaders = User::role('Meter Reader Inhouse')->get();
        $bapa = DB::table('Billing_ServiceAccounts')
            ->select('OrganizationParentAccount')
            ->groupBy('OrganizationParentAccount')
            ->orderBy('OrganizationParentAccount')
            ->get();

        return view('/service_accounts/update_step_one', [
            'serviceAccount' => $serviceAccount,
            'towns' => $towns,
            'barangays' => $barangays,
            'accountTypes' => $accountTypes,
            'meterReaders' => $meterReaders,
            'bapa' => $bapa,
        ]);
    }

    public function mergeAllBillArrears($id) {
        $billArrears = DB::table('Billing_Bills')
            ->where('AccountNumber', $id)
            ->whereNull('MergedToCollectible')
            ->whereNotIn('id', DB::table('Cashier_PaidBills')->pluck('ObjectSourceId'))
            ->orderByDesc('ServicePeriod')
            ->get();

        $total = 0;

        foreach($billArrears as $item) {
            $billArrear = Bills::find($item->id);
            $total = $total + Bills::computePenalty($item->NetAmount);
            if ($billArrear != null) {
                $billArrear->MergedToCollectible = 'Yes';
                $billArrear->save();
            }            
        }

        $collectibles = Collectibles::where('AccountNumber', $id)->first();
        if ($collectibles != null) {
            $balance = floatval($collectibles->Balance) + $total;
            $collectibles->Balance = round($balance, 2);
            $collectibles->save();
        } else {
            $collectibles = new Collectibles;
            $collectibles->id = IDGenerator::generateIDandRandString();
            $collectibles->AccountNumber = $id;
            $collectibles->Balance = round($total, 2);
            $collectibles->save();
        }

        return redirect(route('serviceAccounts.show', [$id]));
    }

    public function unmergeAllBillArrears($id) {
        $billArrears = DB::table('Billing_Bills')
            ->where('AccountNumber', $id)
            ->where('MergedToCollectible', 'Yes')
            ->whereNotIn('id', DB::table('Cashier_PaidBills')->pluck('ObjectSourceId'))
            ->orderByDesc('ServicePeriod')
            ->get();

        $total = 0;

        foreach($billArrears as $item) {
            $billArrear = Bills::find($item->id);
            $total = $total + Bills::computePenalty($item->NetAmount);
            if ($billArrear != null) {
                $billArrear->MergedToCollectible = null;
                $billArrear->save();
            }            
        }

        $collectibles = Collectibles::where('AccountNumber', $id)->first();
        if ($collectibles != null) {
            $balance = floatval($collectibles->Balance) - $total;
            $collectibles->Balance = $balance;
            $collectibles->save();
        } 

        return redirect(route('serviceAccounts.show', [$id]));
    }

    public function unmergeBillArrear($billId) {
        $bill = Bills::find($billId);

        if ($bill != null) {
            $bill->MergedToCollectible = null;
            $bill->save();

            $collectibles = Collectibles::where('AccountNumber', $bill->AccountNumber)->first();
            if ($collectibles != null) {
                $balance = floatval($collectibles->Balance) - Bills::computePenalty($bill->NetAmount);
                $collectibles->Balance = $balance;
                $collectibles->save();
            }

            return redirect(route('serviceAccounts.show', [$bill->AccountNumber]));
        } else {
            return abort(404, 'Bill not found');
        }        
    }

    public function mergeBillArrear($billId) {
        $bill = Bills::find($billId);

        if ($bill != null) {
            $bill->MergedToCollectible = 'Yes';
            $bill->save();

            $collectibles = Collectibles::where('AccountNumber', $bill->AccountNumber)->first();
            if ($collectibles != null) {
                $balance = floatval($collectibles->Balance) + Bills::computePenalty($bill->NetAmount);
                $collectibles->Balance = $balance;
                $collectibles->save();
            } else {
                $collectibles = new Collectibles;
                $collectibles->id = IDGenerator::generateIDandRandString();
                $collectibles->AccountNumber = $bill->AccountNumber;
                $collectibles->Balance = Bills::computePenalty($bill->NetAmount);
                $collectibles->save();
            }

            return redirect(route('serviceAccounts.show', [$bill->AccountNumber]));
        } else {
            return abort(404, 'Bill not found');
        }        
    }

    public function accountsMapView() {
        $towns = Towns::orderBy('Town')->get();
        return view('/service_accounts/accounts_map_view', [
            'towns' => $towns,
        ]);
    }

    public function getAccountsByTown(Request $request) {
        $accounts = ServiceAccounts::where('Town', $request['Town'])
            ->get();

        return response()->json($accounts, 200);
    }

    public function bapa() {
        $towns = Towns::all();

        return view('/service_accounts/bapa', [
            'towns' => $towns,
        ]);
    }

    public function searchBapaAjax(Request $request) {
        $param = $request['BAPA'];
        $town = $request['Town'];

        if ($town == 'All') {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        } else {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->where('sa.Town', $town)
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        }

        $output = "";
        foreach($bapas as $item) {
            if (strlen($item->OrganizationParentAccount) > 1) {
                $output .= '<tr>
                                <td><a href="' . route('serviceAccounts.bapa-view', [urlencode($item->OrganizationParentAccount)]) . '">' . $item->OrganizationParentAccount . '</a></td>
                                <td>' . $item->Town . '</td>
                                <td>' . number_format($item->NoOfAccounts) . '</td>
                                <td>' . $item->Result . '</td>
                            </tr>';
            }
            
        }

        return response()->json($output, 200);
    }

    public function createBapa() {
        $towns = Towns::orderBy('id')->get();
        return view('/service_accounts/create_bapa', [
            'towns' => $towns,
        ]);
    }

    public function getRoutesFromDistrict(Request $request) {
        $routes = DB::table('Billing_ServiceAccounts')
            ->where('Town', $request['Town'])
            ->select('AreaCode',
                DB::raw('COUNT(id) AS NoOfConsumers'))
            ->groupBy('AreaCode')
            ->orderBy('AreaCode')
            ->get();

        $output = "";
        if (count($routes) > 0) {
            foreach($routes as $item) {
                $output .= '<tr>
                                <td>' . $item->AreaCode . '</td>
                                <td>' . $item->NoOfConsumers . '</td>
                                <td class="text-right">
                                    <button id="btn-' . $item->AreaCode . '" class="btn btn-primary btn-xs" onclick=addToBapa("' . $item->AreaCode . '")>Add</button>
                                </td>
                            </tr>';
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }
    }

    public function addToBapa(Request $request) {
        ServiceAccounts::where('AreaCode', $request['AreaCode'])
            ->where('Town', $request['Town'])
            ->whereNull('OrganizationParentAccount')
            ->update(['OrganizationParentAccount' => $request['BAPAName'], 'Organization' => 'BAPA']);

        $accounts = ServiceAccounts::where('OrganizationParentAccount', $request['BAPAName'])
            ->orderBy('ServiceAccountName')
            ->get();

        $output = "";

        foreach($accounts as $item) {
            $output .= '<tr>
                            <td>' . $item->id . '</td>
                            <td>' . $item->OldAccountNo . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                        </tr>';
        }

        return response()->json($output, 200);
    }

    public function bapaView($bapaName) {
        $bapaName = urldecode($bapaName);
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->orderBy('Billing_ServiceAccounts.AreaCode')
            ->orderBy('Billing_ServiceAccounts.OldAccountNo')
            ->get();

        $routes = DB::table('Billing_ServiceAccounts')
            ->where('OrganizationParentAccount', $bapaName)
            ->select('AreaCode',
                DB::raw("COUNT(id) AS NoOfConsumers"))
            ->groupBy('AreaCode')
            ->get();

        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->select('Billing_Readings.ServicePeriod',
                DB::raw("COUNT(Billing_Readings.id) AS NoOfReadings"),
                DB::raw("(SELECT COUNT(id) FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "') AS NoOfConsumers"))
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->groupBy('Billing_Readings.ServicePeriod')
            ->orderByDesc('Billing_Readings.ServicePeriod')
            ->get();

        return view('/service_accounts/bapa_view', [
            'serviceAccounts' => $serviceAccounts,
            'bapaName' => $bapaName,
            'routes' => $routes,
            'readings' => $readings,
        ]);
    }

    public function removeBapaByRoute(Request $request) {
        $bapaName = $request['BAPAName'];
        $route = $request['Route'];

        ServiceAccounts::where('OrganizationParentAccount', $bapaName)
            ->where('AreaCode', $route)
            ->update(['OrganizationParentAccount' => null, 'Organization' => null]);

        return response()->json('ok', 200);
    }

    public function removeBapaByAccount(Request $request) {
        $accountNo = $request['AccountNumber'];

        ServiceAccounts::where('id', $accountNo)
            ->update(['OrganizationParentAccount' => null, 'Organization' => null]);

        return response()->json('ok', 200);
    }

    public function updateBapa($bapaName) {
        $towns = Towns::orderBy('id')->get();
        $accounts = ServiceAccounts::where('OrganizationParentAccount', urldecode($bapaName))
            ->orderBy('ServiceAccountName')
            ->get();
        return view('/service_accounts/update_bapa', [
            'towns' => $towns,
            'bapaName' => $bapaName,
            'accounts' => $accounts,
        ]);
    }

    public function searchAccountBapa(Request $request) {
        $results = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->whereNull('Billing_ServiceAccounts.Organization')
            ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['query'] . '%')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $output .= '
                        <tr>
                            <td>' . $item->id . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>' . $item->AccountStatus . '</td>
                            <td>
                                <button class="btn btn-link text-primary" onclick=addAccountToBapa("' . $item->id . '")><i class="fas fa-forward"></i></button>
                            </td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }

    public function addSingleAccountToBapa(Request $request) {
        $id = $request['id'];
        $bapaName = $request['BAPAName'];

        ServiceAccounts::where('id', $id)
            ->whereNull('OrganizationParentAccount')
            ->update(['OrganizationParentAccount' => $bapaName, 'Organization' => 'BAPA']);

        return response()->json('ok', 200);
    }

    public function readingAccountGrouper() {
        $towns = DB::table('CRM_Towns')
            ->select('id',
                'Town',
                DB::raw("(SELECT COUNT(id) FROM Billing_ServiceAccounts WHERE Town=CRM_Towns.id) AS ConsumerCount"))
            ->orderBy('id')
            ->get();

        return view('/service_accounts/reading_account_grouper', [
            'towns' => $towns
        ]);
    }

    public function accountGrouperView($townCode) {
        $town = Towns::find($townCode);

        $groupings = DB::table('Billing_ServiceAccounts')
            ->select('GroupCode',
                DB::raw('COUNT(id) AS ConsumerCount'))
            ->where('Town', $townCode)
            ->groupBy('GroupCode')
            ->get();

        return view('/service_accounts/account_grouper_view', [
            'town' => $town,
            'groupings' => $groupings
        ]);
    }

    public function accountGrouperOrganizer($townCode, $groupCode) {
        return view('/service_accounts/account_grouper_organizer', [

        ]);
    }

    public function bapaViewReadings($period, $bapaName) {
        $bapaName = urldecode($bapaName);

        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->select('Billing_Readings.ServicePeriod',
                'Billing_ServiceAccounts.id as AccountNumber',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Readings.ReadingTimestamp',
                'Billing_Readings.KwhUsed',
                'Billing_ServiceAccounts.AccountStatus',
                DB::raw("(SELECT id FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND AccountNumber=Billing_ServiceAccounts.id) AS BillId"),
                DB::raw("(SELECT NetAmount FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND AccountNumber=Billing_ServiceAccounts.id) AS NetAmount"),
                DB::raw("(SELECT BillNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND AccountNumber=Billing_ServiceAccounts.id) AS BillNumber"),
                DB::raw("(SELECT DueDate FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND AccountNumber=Billing_ServiceAccounts.id) AS DueDate"))
            ->where('Billing_Readings.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->orderBy('Billing_ServiceAccounts.AreaCode')
            ->orderBy('Billing_ServiceAccounts.OldAccountNo')
            ->get();

        return view('/service_accounts/bapa_view_readings', [
            'readings' => $readings,
            'period' => $period,
            'bapaName' => $bapaName,
        ]);
    }

    public function reSequenceAccounts(Request $request) {
        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.MeterReader', $request['MeterReader'])
            ->where('Billing_Readings.ServicePeriod', $request['ServicePeriod'])
            ->where('Billing_ServiceAccounts.GroupCode', $request['Day'])
            ->where('Billing_ServiceAccounts.Town', $request['Town'])
            ->select('Billing_Readings.AccountNumber',
                'Billing_Readings.ReadingTimestamp',
                'Billing_Readings.KwhUsed',
                'Billing_ServiceAccounts.ServiceAccountName')
            ->orderBy('Billing_Readings.ReadingTimestamp')
            ->get();

        $i = 0;
        foreach($readings as $item) {
            $account = ServiceAccounts::find($item->AccountNumber);
            if ($account != null) {
                $account->SequenceCode = $i;
                $account->save();
            }
            $i++;
        }

        return response()->json('ok', 200);
    }

    public function updateGPSCoordinates(Request $request) {
        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.MeterReader', $request['MeterReader'])
            ->where('Billing_Readings.ServicePeriod', $request['ServicePeriod'])
            ->where('Billing_ServiceAccounts.GroupCode', $request['Day'])
            ->where('Billing_ServiceAccounts.Town', $request['Town'])
            ->select('Billing_Readings.Latitude',
                'Billing_Readings.Longitude',
                'Billing_Readings.AccountNumber')
            ->orderBy('Billing_Readings.ReadingTimestamp')
            ->get();

        foreach($readings as $item) {
            $account = ServiceAccounts::find($item->AccountNumber);
            if ($account != null) {
                $account->Latitude = $item->Latitude;
                $account->Longitude = $item->Longitude;
                $account->save();
            }
        }

        return response()->json('ok', 200);
    }

    public function searchGlobal(Request $request) {
        $results = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['query'] . '%')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $output .= '
                        <tr onclick=goToAccount("' . $item->id . '")>
                            <td>' . $item->OldAccountNo . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>' . $item->AccountStatus . '</td>
                            <td>
                                <button class="btn btn-link text-primary" onclick=goToAccount("' . $item->id . '")><i class="fas fa-forward"></i></button>
                            </td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }

    public function termedPaymentAccounts() {
        $termedAccounts = DB::table('Billing_ArrearsLedgerDistribution')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_ArrearsLedgerDistribution.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereNull('Billing_ArrearsLedgerDistribution.IsPaid')
            ->select('Billing_ArrearsLedgerDistribution.AccountNumber', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->groupBy('Billing_ArrearsLedgerDistribution.AccountNumber', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo')
            ->get();

        return view('/service_accounts/termed_payment_accounts', [
            'termedAccounts' => $termedAccounts,
        ]);
    }

    public function disconnectManual(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $account->AccountStatus = 'DISCONNECTED';
            $account->save();

            $disconnectionHistory = new DisconnectionHistory;
            $disconnectionHistory->id = IDGenerator::generateIDandRandString();
            $disconnectionHistory->AccountNumber = $account->id;
            $disconnectionHistory->ServicePeriod = date('Y-m-01');
            $disconnectionHistory->Status = 'DISCONNECTED';
            $disconnectionHistory->UserId = Auth::id();
            $disconnectionHistory->Notes = $request['Notes'];
            $disconnectionHistory->DateDisconnected = $request['DateDisconnected'];
            $disconnectionHistory->TimeDisconnected = $request['TimeDisconnected'];
            $disconnectionHistory->save();
        }

        return response()->json('ok', 200);
    }

    public function apprehendManual(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $account->AccountStatus = 'APPREHENDED';
            $account->save();

            $disconnectionHistory = new DisconnectionHistory;
            $disconnectionHistory->id = IDGenerator::generateIDandRandString();
            $disconnectionHistory->AccountNumber = $account->id;
            $disconnectionHistory->ServicePeriod = date('Y-m-01');
            $disconnectionHistory->Status = 'APPREHENDED';
            $disconnectionHistory->UserId = Auth::id();
            $disconnectionHistory->Notes = $request['Notes'];
            $disconnectionHistory->DateDisconnected = $request['DateDisconnected'];
            $disconnectionHistory->TimeDisconnected = $request['TimeDisconnected'];
            $disconnectionHistory->save();
        }

        return response()->json('ok', 200);
    }

    public function pulloutManual(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $account->AccountStatus = 'PULLOUT';
            $account->save();

            $disconnectionHistory = new DisconnectionHistory;
            $disconnectionHistory->id = IDGenerator::generateIDandRandString();
            $disconnectionHistory->AccountNumber = $account->id;
            $disconnectionHistory->ServicePeriod = date('Y-m-01');
            $disconnectionHistory->Status = 'PULLOUT';
            $disconnectionHistory->UserId = Auth::id();
            $disconnectionHistory->Notes = $request['Notes'];
            $disconnectionHistory->DateDisconnected = $request['DateDisconnected'];
            $disconnectionHistory->TimeDisconnected = $request['TimeDisconnected'];
            $disconnectionHistory->save();
        }

        return response()->json('ok', 200);
    }

    public function changeName(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $acctNameHist = new AccountNameHistory;
            $acctNameHist->id = IDGenerator::generateIDandRandString();
            $acctNameHist->AccountNumber = $account->id;
            $acctNameHist->OldAccountName = $account->ServiceAccountName;
            $acctNameHist->Notes = $request['Notes'];
            $acctNameHist->UserId = Auth::id();
            $acctNameHist->save();

            $account->ServiceAccountName = $request['NewName'];
            $account->save();
        } 
        
        return response()->json('ok', 200);
    }

    public function updateName(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $acctNameHist = new AccountNameHistory;
            $acctNameHist->id = IDGenerator::generateIDandRandString();
            $acctNameHist->AccountNumber = $account->id;
            $acctNameHist->OldAccountName = $account->ServiceAccountName;
            $acctNameHist->Notes = $request['Notes'];
            $acctNameHist->UserId = Auth::id();
            $acctNameHist->save();

            $account->ServiceAccountName = $request['NewName'];
            $account->save();

            $serviceConnection = ServiceConnections::find($request['ServiceConnectionId']);

            // update service connection
            if ($serviceConnection != null) {
                $serviceConnection->Status = 'Closed';
                $serviceConnection->save();
            }
        } 

        return redirect(route('serviceAccounts.pending-accounts'));
    }

    public function relocationForm($accountNumber, $scId) {
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $accountNumber)
            ->first();
        $serviceConnection = ServiceConnections::find($scId);
        $towns = Towns::where('id', $serviceConnection->Town)->pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $serviceConnection->Town)->pluck('Barangay', 'id');        
        $meterReaders = User::role('Meter Reader Inhouse')->get();

        return view('/service_accounts/relocation_form', [
            'account' => $account,
            'serviceConnection' => $serviceConnection,
            'town' => $towns,
            'barangays' => $barangays,
            'meterReaders' => $meterReaders,
        ]);
    }

    public function storeRelocation(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);
        $serviceConnection = ServiceConnections::find($request['ServiceConnectionId']);

        if ($account != null) {
            // ADD TO HISTORY
            $acctLocHistory = new AccountLocationHistory;
            $acctLocHistory->id = IDGenerator::generateIDandRandString();
            $acctLocHistory->AccountNumber = $account->id;
            $acctLocHistory->Town = $account->Town;
            $acctLocHistory->Barangay = $account->Barangay;
            $acctLocHistory->Purok = $account->Purok;
            $acctLocHistory->AreaCode = $account->AreaCode;
            $acctLocHistory->SequenceCode = $account->SequenceCode;
            $acctLocHistory->MeterReader = $account->MeterReader;
            $acctLocHistory->ServiceConnectionId = ($serviceConnection != null ? $serviceConnection->id : null);
            $acctLocHistory->RelocationDate = ($serviceConnection != null ? $serviceConnection->DateTimeOfEnergization : null);
            $acctLocHistory->save();

            // UPDATE ACCOUNT
            $account->Town = $request['Town'];
            $account->Barangay = $request['Barangay'];
            $account->Purok = $request['Purok'];
            $account->AreaCode = $request['AreaCode'];
            $account->SequenceCode = $request['SequenceCode'];
            $account->GroupCode = $request['GroupCode'];
            $account->MeterReader = $request['MeterReader'];
            $account->save();

            // update service connection
            if ($serviceConnection != null) {
                $serviceConnection->Status = 'Closed';
                $serviceConnection->save();
            }
        }

        Flash::success('Account relocated successfully.');

        if ($serviceConnection != null) {
            return redirect(route('serviceAccounts.pending-accounts'));
        } else {
            return redirect(route('serviceAccounts.show', [$account->id]));
        }
       
    }

    public function printLedger($id, $from, $to) {
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Cashier_PaidBills', 'Billing_Bills.id', '=', 'Cashier_PaidBills.ObjectSourceId')
            ->leftJoin('users', 'users.id', '=', 'Cashier_PaidBills.Teller')
            ->where('Billing_Bills.AccountNumber', $id)
            ->whereRaw("Billing_Bills.ServicePeriod BETWEEN '" . date('Y-m-d', strtotime('January 1 ' . $from)) . "' AND '" . date('Y-m-d', strtotime('December 31 ' . $to)) . "'")
            ->select('Billing_Bills.ServicePeriod',
                'Billing_Bills.BillNumber',
                'Billing_Bills.NetAmount',
                'Billing_Bills.KwhUsed',
                'Billing_Bills.DemandPresentKwh',
                'Billing_Bills.PresentKwh',
                'Cashier_PaidBills.Surcharge',
                'Cashier_PaidBills.NetAmount as ORAmount',
                'Cashier_PaidBills.ORNumber',
                'Cashier_PaidBills.ORDate',
                'users.username',
                'Cashier_PaidBills.OfficeTransacted')
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        return view('/service_accounts/print_ledger', [
            'account' => $serviceAccounts,
            'ledgers' => $ledgers,
            'from' => $from,
            'to' => $to,
            'meters' => $meters
        ]);
    }

    public function searchForCaptured(Request $request) {
        $results = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->whereRaw("Billing_ServiceAccounts.ServiceAccountName LIKE '%" . $request['query'] . "%' 
                OR Billing_ServiceAccounts.id LIKE '%" . $request['query'] . "%' 
                OR Billing_ServiceAccounts.OldAccountNo LIKE '%" . $request['query'] . "%'
                OR Billing_ServiceAccounts.id IN (SELECT ServiceAccountId FROM Billing_Meters WHERE SerialNumber LIKE '%" . $request['query'] . "%')")
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"))
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $output .= '
                        <tr onclick=proceedBilling("' . $item->id . '")>
                            <td>' . $item->OldAccountNo . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>' . $item->MeterNumber . '</td>
                            <td>' . $item->AccountStatus . '</td>
                            <td>
                                <button class="btn btn-link btn-sm text-primary" onclick=proceedBilling("' . $item->id . '")><i class="fas fa-forward"></i> Proceed Billing</button>
                            </td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }

    public function printBapaBillsList($bapaName, $period) {
        $bapaName = urldecode($bapaName);
        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.ServicePeriod', $period)
            // ->where(function ($query) use ($reading, $bapaName) {
            //     $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')")
            //             ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
            // })
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')")
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "') AS PrevKwh"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS ORNumber"),
                DB::raw("(SELECT TOP 1 NetAmount FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS AmountDue"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('CurrentKwh')
            ->orderBy('FieldStatus')
            ->get();

        return view('/service_accounts/print_bapa_bills_list', [
            'readingReport' => $readingReport,
            'bapaName' => $bapaName,
            'period' => $period
        ]);
    }

    public function printGroupBillsList($period, $groupId) {
        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.ServicePeriod', $period)
            ->whereRaw("Billing_ServiceAccounts.MemberConsumerId='" . $groupId . "'")
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "') AS PrevKwh"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS ORNumber"),
                DB::raw("(SELECT TOP 1 NetAmount FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS AmountDue"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('CurrentKwh')
            ->orderBy('FieldStatus')
            ->get();
        
        $memberConsumers = DB::table('CRM_MemberConsumers')
            ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
            ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
            ->select('CRM_MemberConsumers.Id as ConsumerId',
                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                    'CRM_MemberConsumers.FirstName as FirstName', 
                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                    'CRM_MemberConsumers.LastName as LastName', 
                    'CRM_MemberConsumers.Suffix', 
                    'CRM_MemberConsumers.OrganizationName as OrganizationName')
            ->where('CRM_MemberConsumers.Id', $groupId)
            ->first();

        return view('/service_accounts/print_group_bills_list', [
            'readingReport' => $readingReport,
            'memberConsumers' => $memberConsumers,
            'period' => $period
        ]);
    }

    public function renameBapa(Request $request) {
        $oldBapaName = $request['OldBapaName'];
        $newBapaName = $request['NewBapaName'];

        $update = ServiceAccounts::where('OrganizationParentAccount', $oldBapaName)
            ->update(['OrganizationParentAccount' => $newBapaName]);

        return response()->json($newBapaName, 200);
    }

    public function manualAccountMigrationOne() {
        $towns = Towns::orderBy('id')->pluck('Town', 'id');
        $accountTypes = ServiceConnectionAccountTypes::all();
        $meterReaders = User::role('Meter Reader Inhouse')->get();

        return view('/service_accounts/manual_account_migration_one', [
            'town' => $towns,
            'accountTypes' => $accountTypes,
            'meterReaders' => $meterReaders
        ]);
    }

    public function validateOlAccountNo(Request $request) {
        $oldAcctNo = $request['OldAccountNo'];

        $account = ServiceAccounts::where('OldAccountNo', $oldAcctNo)->first();

        if ($account != null) {
            return response()->json('duplicate', 200);
        } else {
            return response()->json('ok', 200);
        }
    }

    public function storeManual(CreateServiceAccountsRequest $request) {
        $input = $request->all();
        $input['id'] = $input['Town'] . '-' . IDGenerator::generateID();
        $input['ServiceAccountName'] = strtoupper($input['ServiceAccountName']);
        $serviceAccounts = $this->serviceAccountsRepository->create($input);
        Flash::success('Service Accounts saved successfully.');

        return redirect(route('serviceAccounts.manual-account-migration-two', [$serviceAccounts->id]));
    }

    public function manualAccountMigrationTwo($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        return view('/service_accounts/manual_account_migration_two', [
            'serviceAccount' => $serviceAccount
        ]);
    }

    public function storeMetersManual(Request $request) {
        $input = $request->all();
        $input['id'] = IDGenerator::generateIDandRandString();
        $sa = ServiceAccounts::find($input['ServiceAccountId']);

        $billingMeters = BillingMeters::create($input);

        if ($sa != null) {
            $sa->Multiplier = $billingMeters->Multiplier;
            $sa->save();
        }

        return redirect(route('serviceAccounts.manual-account-migration-three', [$sa->id]));
    }

    public function manualAccountMigrationThree($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $bapa = DB::table('Billing_ServiceAccounts')
            ->select('OrganizationParentAccount')
            ->groupBy('OrganizationParentAccount')
            ->orderBy('OrganizationParentAccount')
            ->get();

        return view('/service_accounts/manual_account_migration_three', [
            'serviceAccount' => $serviceAccount,
            'meters' => $meters,
            'bapa' => $bapa,
        ]);
    }

    public function storeTransformerManual(Request $request) {
        $input = $request->all();
        $input['id'] = IDGenerator::generateRandString(30);
        $billingTransformers = BillingTransformers::create($input);

        // UPDATE SERVICE ACCOUNT
        $serviceAccount = ServiceAccounts::find($request['ServiceAccountId']);
        $serviceAccount->Coreloss = $request['Coreloss'];
        $serviceAccount->UserId = Auth::id();
        $serviceAccount->Main = $request['Main'];
        $serviceAccount->Organization = $request['BAPA'];
        $serviceAccount->OrganizationParentAccount = $request['OrganizationParentAccount'];
        $serviceAccount->TransformerDetailsId = $input['id'];
        $serviceAccount->Locked = 'Yes';
        $serviceAccount->Evat5Percent = $request['Evat5Percent'];
        $serviceAccount->Ewt2Percent = $request['Ewt2Percent'];
        $serviceAccount->save();

        Flash::success('Account migrated successfully.');

        return redirect(route('serviceAccounts.show', [$serviceAccount->id]));
    }

    public function changeMeterManual(Request $request) {
        if ($request['params'] == null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        } elseif ($request['params'] == null && $request['oldaccount'] != null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        // ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } elseif ($request['params'] != null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhereRaw("Billing_ServiceAccounts.id IN (SELECT ServiceAccountId FROM Billing_Meters WHERE SerialNumber LIKE '%" . $request['params'] . "%')")
                        // ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orWhereRaw("Billing_ServiceAccounts.id IN (SELECT ServiceAccountId FROM Billing_Meters WHERE SerialNumber LIKE '%" . $request['params'] . "%')")
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        }  

        return view('/service_accounts/change_meter_manual', [
            'serviceAccounts' => $serviceAccounts
        ]);
    }

    public function changeMeterManualConsole($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $activeMeter = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        return view('/service_accounts/change_meter_manual_console', [
            'serviceAccount' => $serviceAccount,
            'activeMeter' => $activeMeter
        ]);
    }

    public function storeChangeMeterManual(Request $request) {
        $input = $request->all();
        $input['id'] = IDGenerator::generateIDandRandString();
        $sa = ServiceAccounts::find($input['ServiceAccountId']);

        // UPDATE PREV METER
        $activeMeter = BillingMeters::where('ServiceAccountId', $sa->id)
            ->orderByDesc('created_at')
            ->first();
        if ($activeMeter != null) {
            $activeMeter->LatestReading = $input['PullOutReading'];
            $activeMeter->LatestReadingDate = $input['ConnectionDate'] != null ? $input['ConnectionDate'] : date('Y-m-d');
            $activeMeter->save();

                // COMPUTE AVERAGING
            // ------------------------------------
            // 1. GET LATEST BILL
            $latestBill = Bills::where('AccountNumber', $sa->id)
                ->orderByDesc('ServicePeriod')
                ->first();

            if ($latestBill != null) {
                // ------------------------------------
                // 2. AVERAGE LATEST BILLS
                $latestBills = Bills::where('AccountNumber', $sa->id)
                ->orderByDesc('ServicePeriod')
                ->limit(3)
                ->get();

                $averageKwh = 0;
                foreach($latestBills as $item) {
                    $averageKwh += floatval($item->KwhUsed);
                }
                $averageKwh = $averageKwh/count($latestBills);
                
                // ------------------------------------
                // 3. COMPUTE DAYS INCURED
                $lastReadingDate = strtotime($latestBill->BillingDate);
                $now = $input['ConnectionDate'] != null ? strtotime($input['ConnectionDate']) : date('Y-m-d');
                $daysIncured = $now - $lastReadingDate;
                $daysIncured = round($daysIncured / (60 * 60 * 24));

                // ------------------------------------
                // 4. GET DAILY AVERAGE
                $averageDaily = ($averageKwh/30) * $daysIncured;

                // ------------------------------------
                // 5. CREATE CHANGE METER LOGS
                $changeMeterLogs = new ChangeMeterLogs;
                $changeMeterLogs->id = IDGenerator::generateIDandRandString();
                $changeMeterLogs->AccountNumber = $sa->id;
                $changeMeterLogs->OldMeterSerial = $activeMeter->SerialNumber;
                $changeMeterLogs->NewMeterSerial = $request['SerialNumber'];
                $changeMeterLogs->PullOutReading = $input['PullOutReading']; 
                $changeMeterLogs->NewMeterStartKwh = $input['InitialReading'];
                $changeMeterLogs->AdditionalKwhForNextBilling = round($averageDaily, 2);
                $changeMeterLogs->ServicePeriod = date('Y-m-01', strtotime($latestBill->ServicePeriod . ' +1 month')); 
                $changeMeterLogs->save(); 
            } else {
                $svPeriod = date('Y-m-01');

                $changeMeterLogs = new ChangeMeterLogs;
                $changeMeterLogs->id = IDGenerator::generateIDandRandString();
                $changeMeterLogs->AccountNumber = $sa->id;
                $changeMeterLogs->OldMeterSerial = $activeMeter->SerialNumber;
                $changeMeterLogs->NewMeterSerial = $request['SerialNumber'];
                $changeMeterLogs->PullOutReading = $input['PullOutReading']; 
                $changeMeterLogs->NewMeterStartKwh = $input['InitialReading'];
                $changeMeterLogs->AdditionalKwhForNextBilling = '0';
                $changeMeterLogs->ServicePeriod = $svPeriod; 
                $changeMeterLogs->save(); 
            }             
        }    

        // SAVE NEW METER
        $billingMeters = BillingMeters::create($input);

        // UPDATE MULTIPLIER
        if ($sa != null) {
            $sa->Multiplier = $billingMeters->Multiplier;
            $sa->save();
        }

        return redirect(route('serviceAccounts.show', [$sa->id]));
    }

    public function relocationManual(Request $request) {
        if ($request['params'] == null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        } elseif ($request['params'] == null && $request['oldaccount'] != null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        // ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        // ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } elseif ($request['params'] != null && $request['oldaccount'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhereRaw("Billing_ServiceAccounts.id IN (SELECT ServiceAccountId FROM Billing_Meters WHERE SerialNumber LIKE '%" . $request['params'] . "%')")
                        // ->where('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);            
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.id', 
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.OldAccountNo',   
                            'Billing_ServiceAccounts.AccountCount',  
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay',
                            'Billing_ServiceAccounts.AccountStatus',
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber")
                        )
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['oldaccount'] . '%')
                        ->orWhereRaw("Billing_ServiceAccounts.id IN (SELECT ServiceAccountId FROM Billing_Meters WHERE SerialNumber LIKE '%" . $request['params'] . "%')")
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        }  

        return view('/service_accounts/relocation_manual', [
            'serviceAccounts' => $serviceAccounts
        ]);
    }

    public function relocationFormManual($id) {
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.MeterReader',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'Billing_ServiceAccounts.Town as TownId',
                    'Billing_ServiceAccounts.Barangay as BarangayId',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $towns = Towns::pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $account->BarangayId)->pluck('Barangay', 'id');        
        $meterReaders = User::role('Meter Reader Inhouse')->get();

        return view('/service_accounts/relocation_form_manual', [
            'account' => $account,
            'town' => $towns,
            'barangays' => $barangays,
            'meterReaders' => $meterReaders,
        ]);
    }

    public function checkAvailableAccountNumbers(Request $request) {
        $acctNoSample = $request['AccountNumberSample'];

        $lastAccount = DB::table('Billing_ServiceAccounts')
            ->whereRaw("OldAccountNo LIKE '" . $acctNoSample . "%'")
            ->orderByDesc('OldAccountNo')
            ->first();

        $output = "";
        if ($lastAccount != null) {
            $lastNumber = $lastAccount->OldAccountNo;
            if ($lastNumber != null) {
                $acctHead = explode('-', $lastNumber)[0] . '-' . explode('-', $lastNumber)[1];
                $lastSeq = explode('-', $lastNumber)[2];
                $lastSeq = intval($lastSeq)+1;

                for ($i=$lastSeq; $i<1000; $i++) {
                    $accountNew = $acctHead . '-' . sprintf("%003d", $i);
                    $output .= "<tr onclick=selectAccount('" . $accountNew . "')>" .
                                "<td>" . $accountNew . "</td>" .
                            "</tr>";
                }
            } 
        } else {
            $acctHead = explode('-', $acctNoSample)[0] . '-' . explode('-', $acctNoSample)[1];
            for ($i=1; $i<1000; $i++) {
                $accountNew = $acctHead . '-' . sprintf("%003d", $i);
                $output .= "<tr onclick=selectAccount('" . $accountNew . "')>" .
                            "<td>" . $accountNew . "</td>" .
                        "</tr>";
            }
        }
        
        return response()->json($output, 200);
    }
}
