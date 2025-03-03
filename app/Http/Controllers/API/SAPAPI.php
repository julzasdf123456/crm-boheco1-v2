<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SAPObject;
use App\Models\IDGenerator;

class SAPAPI extends Controller {
    public function getServiceInvoiceBatch(Request $request) {
        $batchCount = $request['batchCount'];

        $data = DB::connection("sqlsrvbilling")
                    ->table('Bills')
                    ->leftJoin('AccountMaster', 'Bills.AccountNumber', '=', 'AccountMaster.AccountNumber')
                    ->leftJoin('BillsExtension', function($join) {
                        $join->on('Bills.AccountNumber', '=', 'BillsExtension.AccountNumber')
                            ->on('Bills.ServicePeriodEnd', '=', 'BillsExtension.ServicePeriodEnd');
                    })
                    ->leftJoin('Readings', function($join) {
                        $join->on('Bills.AccountNumber', '=', 'Readings.AccountNumber')
                            ->on('Bills.ServicePeriodEnd', '=', 'Readings.ServicePeriodEnd');
                    })
                    ->whereRaw("Bills.Reserve1 IS NULL")                   
                    ->whereDate('Bills.ServicePeriodEnd', '>=', '2025-01-01')
                    ->select(
                        'Bills.AccountNumber',
                        'Bills.ServicePeriodEnd',
                        'AccountMaster.Item1 AS AccountCoordinates',
                        'AccountMaster.MeterNumber',
                        'AccountMaster.ConsumerName',

                        'Bills.DAA_GRAM',
                        'Bills.DAA_ICERA',
                        'Bills.ACRM_TAFPPCA',
                        'Bills.ACRM_TAFxA',
                        'Bills.DAA_VAT',
                        'Bills.ACRM_VAT',
                        'Bills.NetPresReading',
                        'Bills.NetPowerKWH',
                        'Bills.NetGenerationAmount',
                        'Bills.CreditKWH',
                        'Bills.CreditAmount',
                        'Bills.NetMeteringSystemAmt',
                        'Bills.Item3',
                        'Bills.Item4 AS FitAll',
                        'Bills.SeniorCitizenDiscount',
                        'Bills.SeniorCitizenSubsidy',
                        'Bills.UCMERefund',
                        'Bills.NetPrevReading',
                        'Bills.CrossSubsidyCreditAmt',
                        'Bills.MissionaryElectrificationAmt',
                        'Bills.EnvironmentalAmt',
                        'Bills.LifelineSubsidyAmt',
                        'Bills.Item1',
                        'Bills.Item2',
                        'Bills.DistributionSystemAmt',
                        'Bills.SupplyRetailCustomerAmt',
                        'Bills.SupplySystemAmt',
                        'Bills.MeteringRetailCustomerAmt',
                        'Bills.MeteringSystemAmt',
                        'Bills.SystemLossAmt',
                        'Bills.FBHCAmt',
                        'Bills.FPCAAdjustmentAmt AS NPCStrandedDebts',
                        'Bills.ForexAdjustmentAmt',
                        'Bills.TransmissionDemandAmt',
                        'Bills.TransmissionSystemAmt',
                        'Bills.DistributionDemandAmt',
                        'Bills.EPAmount',
                        'Bills.PCAmount',
                        'Bills.LoanCondonation',
                        'Bills.BillingPeriod',
                        'Bills.UnbundledTag',
                        'Bills.GenerationSystemAmt',
                        'Bills.PPCAAmount',
                        'Bills.UCAmount',
                        'Bills.MeterNumber',
                        'Bills.ConsumerType',
                        'Bills.BillType',
                        'Bills.QCAmount',
                        'Bills.PPA',
                        'Bills.PPAAmount',
                        'Bills.BasicAmount',
                        'Bills.PRADiscount',
                        'Bills.PRAAmount',
                        'Bills.PPCADiscount',
                        'Bills.AverageKWDemand',
                        'Bills.CoreLoss',
                        'Bills.Meter',
                        'Bills.PR',
                        'Bills.SDW',
                        'Bills.Others',
                        'Bills.ServiceDateFrom',
                        'Bills.ServiceDateTo',
                        'Bills.DueDate',
                        'Bills.BillNumber',
                        'Bills.Remarks',
                        'Bills.AverageKWH',
                        'Bills.Charges',
                        'Bills.Deductions',
                        'Bills.NetAmount',
                        'Bills.PowerRate',
                        'Bills.DemandRate',
                        'Bills.BillingDate',
                        'Bills.AdditionalKWH',
                        'Bills.AdditionalKWDemand',
                        'Bills.PowerKWH',
                        'Bills.KWHAmount',
                        'Bills.DemandKW',
                        'Bills.KWAmount',
                        'Bills.InvoiceNumber',
                        'Bills.InvoicePrefix',
                        'Bills.InvoiceSuffix',
                        'BillsExtension.GenerationVAT',
                        'BillsExtension.TransmissionVAT',
                        'BillsExtension.SLVAT',
                        'BillsExtension.DistributionVAT',
                        'BillsExtension.OthersVAT',
                        'BillsExtension.Item5',
                        'BillsExtension.Item6',
                        'BillsExtension.Item7',
                        'BillsExtension.Item8',
                        'BillsExtension.Item9',
                        'BillsExtension.Item10 AS RFSC',
                        'BillsExtension.Item11',
                        'BillsExtension.Item12',
                        'BillsExtension.Item13',
                        'BillsExtension.Item14',
                        'BillsExtension.Item15',
                        'BillsExtension.Item16 AS BusinessTax',
                        'BillsExtension.Item17 AS RPT',
                        'BillsExtension.Item18 AS OGA',
                        'BillsExtension.Item19 AS OTGA',
                        'BillsExtension.Item20 AS OSLA',
                        'BillsExtension.Item21',
                        'BillsExtension.Item22',
                        'BillsExtension.Item23',
                        'BillsExtension.Item24',

                        'Readings.ReadingDate AS DocDate',
                        
                    )
                    ->orderByDesc('Bills.ServicePeriodEnd')
                    ->limit($batchCount)
                    //->offset(120)
                    ->get();

        $sapObj = new SAPObject;
        $sapObj->BatchID = SAPObject::getSIBatchIdDaily();
        $sapObj->BatchDate = date('Ymd');

        $docHeaders = [];
        foreach ($data as $key => $item) {
            $header = [];

            // $header['U_TransId'] = $item->InvoicePrefix . $item->InvoiceNumber . $item->InvoiceSuffix; // uncomment during deploymet
            //$header['U_TransId'] = 'SI' . IDGenerator::generateID() . $key; // THIS IS ONLY A TEST comment during deploymet
            $header['U_TransId'] = 'SI' . $item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd)); // THIS IS ONLY A TEST comment during deploymet
            $header['CardCode'] = 'C00001'; // FIXED CARD CODE FOR BOHECO I, Change if necessary
            // $header['NumAtCard'] = $item->InvoicePrefix . $item->InvoiceNumber . $item->InvoiceSuffix; // uncomment during deploymet
            $header['NumAtCard'] = $item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd));
            $header['DocDate'] = date('Ymd', strtotime($item->DocDate));
            $header['DocDueDate'] = date('Ymd', strtotime($item->DueDate));
            $header['TaxDate'] = date('Ymd', strtotime($item->DocDate));
            $header['Comments'] = $item->AccountNumber;
            $header['AccountNumber'] = $item->AccountNumber;
            $header['ServicePeriodEnd'] = $item->ServicePeriodEnd;

            $docDetails = [
                [
                    "LineNum" => 0,
                    "Description" => "Generation Charges",
                    "AcctCode" => "41040100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->GenerationSystemAmt, $item->OGA]),
                ],
                [
                    "LineNum" => 1,
                    "Description" => "Transmission Delivery Charges",
                    "AcctCode" => "41040200000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->TransmissionSystemAmt, $item->TransmissionDemandAmt, $item->OTGA]),
                ],
                [
                    "LineNum" => 2,
                    "Description" => "System Loss Charges",
                    "AcctCode" => "41040300000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SystemLossAmt, $item->OSLA]),
                ],
                [
                    "LineNum" => 3,
                    "Description" => "Distribution Network Charge",
                    "AcctCode" => SAPObject::getAccountCodeByConsumerType($item->ConsumerType),
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DistributionDemandAmt, $item->DistributionSystemAmt]),
                ],
                [
                    "LineNum" => 4,
                    "Description" => "Supply System Charge",
                    "AcctCode" => "41040500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SupplySystemAmt]),
                ],
                [
                    "LineNum" => 5,
                    "Description" => "Supply Retail Customer Charge",
                    "AcctCode" => "41040500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SupplyRetailCustomerAmt]),
                ],
                [
                    "LineNum" => 6,
                    "Description" => "Metering System Charge",
                    "AcctCode" => "41040600000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MeteringSystemAmt]),
                ],
                [
                    "LineNum" => 7,
                    "Description" => "Metering Retail Cust. Charge",
                    "AcctCode" => "41040600000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MeteringRetailCustomerAmt]),
                ],
                [
                    "LineNum" => 8,
                    "Description" => "Missionary Electrification Charge",
                    "AcctCode" => "22220310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MissionaryElectrificationAmt]),
                ],
                [
                    "LineNum" => 9,
                    "Description" => "NPC Stranded Debts Charge",
                    "AcctCode" => "22220314000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->NPCStrandedDebts]),
                ],
                [
                    "LineNum" => 10,
                    "Description" => "NPC Stranded Con. Cost Charge",
                    "AcctCode" => "22220313000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => 0, // CHANGE IF THERE's ALREADY A FIGURE
                ],
                [
                    "LineNum" => 11,
                    "Description" => "Environmental Charge",
                    "AcctCode" => "22220312000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->EnvironmentalAmt]),
                ],
                [
                    "LineNum" => 12,
                    "Description" => "ACRM - TAFPPCA Charge",
                    "AcctCode" => "-", // UPDATE IF THERE'S ALREADY VALUE
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->ACRM_TAFPPCA]),
                ],
                [
                    "LineNum" => 13,
                    "Description" => "DAA - GRAM Charge",
                    "AcctCode" => "22220500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DAA_GRAM]),
                ],
                [
                    "LineNum" => 14,
                    "Description" => "Generation Charges (Delivered)",
                    "AcctCode" => "41040100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->NetGenerationAmount]),
                ],
                [
                    "LineNum" => 15,
                    "Description" => "Senior Citizen Discount/Subsidy Charge",
                    "AcctCode" => "41041000000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SeniorCitizenDiscount, $item->SeniorCitizenSubsidy]),
                ],
                [
                    "LineNum" => 16,
                    "Description" => "Lifeline Subsidy Charge (Discount)",
                    "AcctCode" => "41041100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->LifelineSubsidyAmt]),
                ],
                [
                    "LineNum" => 17,
                    "Description" => "RFSC",
                    "AcctCode" => "31031100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->RFSC]),
                ],
                [
                    "LineNum" => 18,
                    "Description" => "Feed-In Tariff Allow (FIT All) Charge",
                    "AcctCode" => "22220700000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->FitAll]),
                ],
                [
                    "LineNum" => 19,
                    "Description" => "Franchise Tax",
                    "AcctCode" => "53051300000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->FBHCAmt]),
                ],
                [
                    "LineNum" => 20,
                    "Description" => "Business Tax",
                    "AcctCode" => "53051400000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->BusinessTax]),
                ],
                [
                    "LineNum" => 21,
                    "Description" => "Real Property Tax",
                    "AcctCode" => "53051200000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->RPT]),
                ],
                [
                    "LineNum" => 22,
                    "Description" => "Generation VAT Charge",
                    "AcctCode" => "12410310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->GenerationVAT]),
                ],
                [
                    "LineNum" => 23,
                    "Description" => "Transmission VAT Charge",
                    "AcctCode" => "12410311000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->TransmissionVAT]),
                ],
                [
                    "LineNum" => 24,
                    "Description" => "System Loss VAT Charge",
                    "AcctCode" => "12410310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SLVAT]),
                ],
                [
                    "LineNum" => 25,
                    "Description" => "Distribution VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DistributionVAT]),
                ],
                [
                    "LineNum" => 25,
                    "Description" => "DAA VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DAA_VAT]),
                ],
                [
                    "LineNum" => 26,
                    "Description" => "ACRM VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->ACRM_VAT]),
                ],
                [
                    "LineNum" => 27,
                    "Description" => "Other VAT Charges",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->OthersVAT]),
                ],
            ];

            // FILTER ZERO AMOUNTS IN LINE TOTAL
            // Remove if LineTotal amount is zero
            $filtered = array_filter($docDetails, function($x) {
                return abs($x['LineTotal']) > 0 || abs($x['LineTotal']) < 0;
            });

            $filtered = array_values($filtered);

            $header['DocDetails'] = $filtered;

            $docHeaders[] = $header;
        }

        $sapObj->DocHeaders = $docHeaders;
        
        return response()->json($sapObj, 200);
    }

    public function getIncomingPaymentBatch(Request $request) {
        $batchCount = $request['batchCount'];
        
        $data = DB::connection("sqlsrvbilling")
                    ->table('Bills')
                    ->leftJoin('AccountMaster', 'Bills.AccountNumber', '=', 'AccountMaster.AccountNumber')
                    ->leftJoin('PaidBills', function($join) {
                        $join->on('Bills.AccountNumber', '=', 'PaidBills.AccountNumber')
                            ->on('Bills.ServicePeriodEnd', '=', 'PaidBills.ServicePeriodEnd');
                    })
                                     
                    ->whereDate('PaidBills.ServicePeriodEnd', '>=', '2025-01-01')
                    ->whereRaw("PaidBills.Others1 IS NULL")   
                    ->select(
                        'Bills.AccountNumber',
                        'Bills.ServicePeriodEnd',
                        'AccountMaster.Item1 AS AccountCoordinates',
                        'AccountMaster.MeterNumber',
                        'AccountMaster.ConsumerName',

                        'Bills.BillNumber as InvReferenceNo',
                        'Bills.BillingDate',
                        'Bills.DueDate',

                        'PaidBills.NetAmount as PaidAmount',
                        'PaidBills.PostingDate as DocDate',
                        
                    )
                    ->orderByDesc('PaidBills.ServicePeriodEnd')
                    ->limit($batchCount)
                    //->offset(120)
                    ->get();
                        

        $sapObj = new SAPObject;
        $sapObj->BatchID = SAPObject::getIPBatchIdDaily();
        $sapObj->BatchDate = date('Ymd ');

        $paymentDetails = [];
        foreach ($data as $key => $item) {
            if (isset($item->PaidAmount)) {
                $details = [];

                // $details['U_TransId'] = $item->InvoicePrefix . $item->InvoiceNumber . $item->InvoiceSuffix; // uncomment during deploymet
                $details['U_TransId'] = 'INPAY' .$item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd)); // THIS IS ONLY A TEST comment during deploymet
                $details['InvReferenceNo'] = $item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd));
                $details['CardCode'] = 'C00001'; // FIXED CARD CODE FOR BOHECO I, Change if necessary            

                $details['DocDate'] = date('Ymd', strtotime($item->DocDate));
                $details['DocDueDate'] = date('Ymd', strtotime($item->DueDate));
                $details['TaxDate'] = date('Ymd', strtotime($item->DocDate));
                
                $details['Account'] = '12110201000';
                $details['PaidAmount'] = $item->PaidAmount;
                $details['Remarks'] = "Payment for InvReferenceNo: ".$item->InvReferenceNo;
                $details['JournalRemarks'] = "Payment for InvReferenceNo: ".$item->InvReferenceNo;
                $details['AccountNumber'] = $item->AccountNumber;
                $details['ServicePeriodEnd'] = $item->ServicePeriodEnd;

                $paymentDetails[] = $details;
            }
        }

        $sapObj->PaymentDetails = $paymentDetails;
        
        return response()->json($sapObj, 200);
    }

    public function getAccountingServiceInvoiceBatch(Request $request) {
        $batchCount = $request['batchCount'];

        $data = DB::connection("sqlsrvaccounting")
                    ->table('Bills')
                    ->leftJoin('AccountMaster', 'Bills.AccountNumber', '=', 'AccountMaster.AccountNumber')
                    ->leftJoin('BillsExtension', function($join) {
                        $join->on('Bills.AccountNumber', '=', 'BillsExtension.AccountNumber')
                            ->on('Bills.ServicePeriodEnd', '=', 'BillsExtension.ServicePeriodEnd');
                    })
                    ->leftJoin('Readings', function($join) {
                        $join->on('Bills.AccountNumber', '=', 'Readings.AccountNumber')
                            ->on('Bills.ServicePeriodEnd', '=', 'Readings.ServicePeriodEnd');
                    })
                    ->whereRaw("Bills.Reserve1 IS NULL")                    
                    ->whereDate('Bills.ServicePeriodEnd', '>=', '2025-01-01')
                    ->select(
                        'Bills.AccountNumber',
                        'Bills.ServicePeriodEnd',
                        'AccountMaster.Item1 AS AccountCoordinates',
                        'AccountMaster.MeterNumber',
                        'AccountMaster.ConsumerName',

                        'Bills.DAA_GRAM',
                        'Bills.DAA_ICERA',
                        'Bills.ACRM_TAFPPCA',
                        'Bills.ACRM_TAFxA',
                        'Bills.DAA_VAT',
                        'Bills.ACRM_VAT',
                        'Bills.NetPresReading',
                        'Bills.NetPowerKWH',
                        'Bills.NetGenerationAmount',
                        'Bills.CreditKWH',
                        'Bills.CreditAmount',
                        'Bills.NetMeteringSystemAmt',
                        'Bills.Item3',
                        'Bills.Item4 AS FitAll',
                        'Bills.SeniorCitizenDiscount',
                        'Bills.SeniorCitizenSubsidy',
                        'Bills.UCMERefund',
                        'Bills.NetPrevReading',
                        'Bills.CrossSubsidyCreditAmt',
                        'Bills.MissionaryElectrificationAmt',
                        'Bills.EnvironmentalAmt',
                        'Bills.LifelineSubsidyAmt',
                        'Bills.Item1',
                        'Bills.Item2',
                        'Bills.DistributionSystemAmt',
                        'Bills.SupplyRetailCustomerAmt',
                        'Bills.SupplySystemAmt',
                        'Bills.MeteringRetailCustomerAmt',
                        'Bills.MeteringSystemAmt',
                        'Bills.SystemLossAmt',
                        'Bills.FBHCAmt',
                        'Bills.FPCAAdjustmentAmt AS NPCStrandedDebts',
                        'Bills.ForexAdjustmentAmt',
                        'Bills.TransmissionDemandAmt',
                        'Bills.TransmissionSystemAmt',
                        'Bills.DistributionDemandAmt',
                        'Bills.EPAmount',
                        'Bills.PCAmount',
                        'Bills.LoanCondonation',
                        'Bills.BillingPeriod',
                        'Bills.UnbundledTag',
                        'Bills.GenerationSystemAmt',
                        'Bills.PPCAAmount',
                        'Bills.UCAmount',
                        'Bills.MeterNumber',
                        'Bills.ConsumerType',
                        'Bills.BillType',
                        'Bills.QCAmount',
                        'Bills.PPA',
                        'Bills.PPAAmount',
                        'Bills.BasicAmount',
                        'Bills.PRADiscount',
                        'Bills.PRAAmount',
                        'Bills.PPCADiscount',
                        'Bills.AverageKWDemand',
                        'Bills.CoreLoss',
                        'Bills.Meter',
                        'Bills.PR',
                        'Bills.SDW',
                        'Bills.Others',
                        'Bills.ServiceDateFrom',
                        'Bills.ServiceDateTo',
                        'Bills.DueDate',
                        'Bills.BillNumber',
                        'Bills.Remarks',
                        'Bills.AverageKWH',
                        'Bills.Charges',
                        'Bills.Deductions',
                        'Bills.NetAmount',
                        'Bills.PowerRate',
                        'Bills.DemandRate',
                        'Bills.BillingDate',
                        'Bills.AdditionalKWH',
                        'Bills.AdditionalKWDemand',
                        'Bills.PowerKWH',
                        'Bills.KWHAmount',
                        'Bills.DemandKW',
                        'Bills.KWAmount',
                        'Bills.InvoiceNumber',
                        'Bills.InvoicePrefix',
                        'Bills.InvoiceSuffix',
                        'BillsExtension.GenerationVAT',
                        'BillsExtension.TransmissionVAT',
                        'BillsExtension.SLVAT',
                        'BillsExtension.DistributionVAT',
                        'BillsExtension.OthersVAT',
                        'BillsExtension.Item5',
                        'BillsExtension.Item6',
                        'BillsExtension.Item7',
                        'BillsExtension.Item8',
                        'BillsExtension.Item9',
                        'BillsExtension.Item10 AS RFSC',
                        'BillsExtension.Item11',
                        'BillsExtension.Item12',
                        'BillsExtension.Item13',
                        'BillsExtension.Item14',
                        'BillsExtension.Item15',
                        'BillsExtension.Item16 AS BusinessTax',
                        'BillsExtension.Item17 AS RPT',
                        'BillsExtension.Item18 AS OGA',
                        'BillsExtension.Item19 AS OTGA',
                        'BillsExtension.Item20 AS OSLA',
                        'BillsExtension.Item21',
                        'BillsExtension.Item22',
                        'BillsExtension.Item23',
                        'BillsExtension.Item24',

                        'Readings.ReadingDate AS DocDate',
                    )
                    ->orderByDesc('Bills.ServicePeriodEnd')
                    ->limit($batchCount)
                    //->offset(120)
                    ->get();

        $sapObj = new SAPObject;
        $sapObj->BatchID = SAPObject::getSIBatchIdDaily();
        $sapObj->BatchDate = date('Ymd');

        $docHeaders = [];
        foreach ($data as $key => $item) {
            $header = [];

            // $header['U_TransId'] = $item->InvoicePrefix . $item->InvoiceNumber . $item->InvoiceSuffix; // uncomment during deploymet
            //$header['U_TransId'] = 'SI' . IDGenerator::generateID() . $key; // THIS IS ONLY A TEST comment during deploymet
            $header['U_TransId'] = 'SI' . $item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd)); // THIS IS ONLY A TEST comment during deploymet
            $header['CardCode'] = 'C00001'; // FIXED CARD CODE FOR BOHECO I, Change if necessary
            // $header['NumAtCard'] = $item->InvoicePrefix . $item->InvoiceNumber . $item->InvoiceSuffix; // uncomment during deploymet
            $header['NumAtCard'] = $item->AccountNumber.date('Ymd', strtotime($item->ServicePeriodEnd));
            

            $header['DocDate'] = date('Ymd', strtotime($item->DocDate));
            $header['DocDueDate'] = date('Ymd', strtotime($item->DueDate));
            $header['TaxDate'] = date('Ymd', strtotime($item->DocDate));

            $header['Comments'] = $item->AccountNumber;
            $header['AccountNumber'] = $item->AccountNumber;
            $header['ServicePeriodEnd'] = $item->ServicePeriodEnd;

            $docDetails = [
                [
                    "LineNum" => 0,
                    "Description" => "Generation Charges",
                    "AcctCode" => "41040100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->GenerationSystemAmt, $item->OGA]),
                ],
                [
                    "LineNum" => 1,
                    "Description" => "Transmission Delivery Charges",
                    "AcctCode" => "41040200000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->TransmissionSystemAmt, $item->TransmissionDemandAmt, $item->OTGA]),
                ],
                [
                    "LineNum" => 2,
                    "Description" => "System Loss Charges",
                    "AcctCode" => "41040300000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SystemLossAmt, $item->OSLA]),
                ],
                [
                    "LineNum" => 3,
                    "Description" => "Distribution Network Charge",
                    "AcctCode" => SAPObject::getAccountCodeByConsumerType($item->ConsumerType),
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DistributionDemandAmt, $item->DistributionSystemAmt]),
                ],
                [
                    "LineNum" => 4,
                    "Description" => "Supply System Charge",
                    "AcctCode" => "41040500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SupplySystemAmt]),
                ],
                [
                    "LineNum" => 5,
                    "Description" => "Supply Retail Customer Charge",
                    "AcctCode" => "41040500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SupplyRetailCustomerAmt]),
                ],
                [
                    "LineNum" => 6,
                    "Description" => "Metering System Charge",
                    "AcctCode" => "41040600000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MeteringSystemAmt]),
                ],
                [
                    "LineNum" => 7,
                    "Description" => "Metering Retail Cust. Charge",
                    "AcctCode" => "41040600000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MeteringRetailCustomerAmt]),
                ],
                [
                    "LineNum" => 8,
                    "Description" => "Missionary Electrification Charge",
                    "AcctCode" => "22220310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->MissionaryElectrificationAmt]),
                ],
                [
                    "LineNum" => 9,
                    "Description" => "NPC Stranded Debts Charge",
                    "AcctCode" => "22220314000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->NPCStrandedDebts]),
                ],
                [
                    "LineNum" => 10,
                    "Description" => "NPC Stranded Con. Cost Charge",
                    "AcctCode" => "22220313000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => 0, // CHANGE IF THERE's ALREADY A FIGURE
                ],
                [
                    "LineNum" => 11,
                    "Description" => "Environmental Charge",
                    "AcctCode" => "22220312000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->EnvironmentalAmt]),
                ],
                [
                    "LineNum" => 12,
                    "Description" => "ACRM - TAFPPCA Charge",
                    "AcctCode" => "-", // UPDATE IF THERE'S ALREADY VALUE
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->ACRM_TAFPPCA]),
                ],
                [
                    "LineNum" => 13,
                    "Description" => "DAA - GRAM Charge",
                    "AcctCode" => "22220500000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DAA_GRAM]),
                ],
                [
                    "LineNum" => 14,
                    "Description" => "Generation Charges (Delivered)",
                    "AcctCode" => "41040100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->NetGenerationAmount]),
                ],
                [
                    "LineNum" => 15,
                    "Description" => "Senior Citizen Discount/Subsidy Charge",
                    "AcctCode" => "41041000000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SeniorCitizenDiscount, $item->SeniorCitizenSubsidy]),
                ],
                [
                    "LineNum" => 16,
                    "Description" => "Lifeline Subsidy Charge (Discount)",
                    "AcctCode" => "41041100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->LifelineSubsidyAmt]),
                ],
                [
                    "LineNum" => 17,
                    "Description" => "RFSC",
                    "AcctCode" => "31031100000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->RFSC]),
                ],
                [
                    "LineNum" => 18,
                    "Description" => "Feed-In Tariff Allow (FIT All) Charge",
                    "AcctCode" => "22220700000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->FitAll]),
                ],
                [
                    "LineNum" => 19,
                    "Description" => "Franchise Tax",
                    "AcctCode" => "53051300000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->FBHCAmt]),
                ],
                [
                    "LineNum" => 20,
                    "Description" => "Business Tax",
                    "AcctCode" => "53051400000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->BusinessTax]),
                ],
                [
                    "LineNum" => 21,
                    "Description" => "Real Property Tax",
                    "AcctCode" => "53051200000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => -SAPObject::sumAll([$item->RPT]),
                ],
                [
                    "LineNum" => 22,
                    "Description" => "Generation VAT Charge",
                    "AcctCode" => "12410310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->GenerationVAT]),
                ],
                [
                    "LineNum" => 23,
                    "Description" => "Transmission VAT Charge",
                    "AcctCode" => "12410311000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->TransmissionVAT]),
                ],
                [
                    "LineNum" => 24,
                    "Description" => "System Loss VAT Charge",
                    "AcctCode" => "12410310000",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->SLVAT]),
                ],
                [
                    "LineNum" => 25,
                    "Description" => "Distribution VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DistributionVAT]),
                ],
                [
                    "LineNum" => 25,
                    "Description" => "DAA VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->DAA_VAT]),
                ],
                [
                    "LineNum" => 26,
                    "Description" => "ACRM VAT Charge",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->ACRM_VAT]),
                ],
                [
                    "LineNum" => 27,
                    "Description" => "Other VAT Charges",
                    "AcctCode" => "22420414001",
                    "VatGroup" => "VATOUT-N",
                    "LineTotal" => SAPObject::sumAll([$item->OthersVAT]),
                ],
            ];

            // FILTER ZERO AMOUNTS IN LINE TOTAL
            // Remove if LineTotal amount is zero
            $filtered = array_filter($docDetails, function($x) {
                return abs($x['LineTotal']) > 0 || abs($x['LineTotal']) < 0;
            });

            $filtered = array_values($filtered);

            $header['DocDetails'] = $filtered;

            $docHeaders[] = $header;
        }

        $sapObj->DocHeaders = $docHeaders;
        
        return response()->json($sapObj, 200);
    }
}