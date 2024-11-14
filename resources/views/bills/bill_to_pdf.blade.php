@php
    use App\Models\AccountMaster;
@endphp

<link rel="stylesheet" href="{{ URL::asset('css/all.css'); }} ">
<link rel="stylesheet" href="{{ URL::asset('css/adminlte.min.css'); }} ">
<link rel="stylesheet" href="{{ URL::asset('css/responsive.bootstrap4.min.css'); }}">
<link rel="stylesheet" href="{{ URL::asset('css/style.css'); }}">
<link rel="stylesheet" href="{{ URL::asset('css/bill.print.css'); }}">

<style>
    html, body {
        font-size: .92em !important;
        -webkit-print-color-adjust: exact;
        height: 100% !important;
    }
    
    td, th, tr {
        font-size: .88em !important;
        background-color: transparent !important;
    }

    .table-sm td, 
    .table-sm th {
        padding: 0px 3px !important;
    }

    .header-official-bg {
        width: 100%;
        height: 100%;
        margin: 0;
        /* padding: 290px 30px 0 30px; */
        background-image: url("{{ URL::asset('imgs/header_official_a4.png') }}");
        background-size: contain;      
        background-position: top center; 
        background-repeat: no-repeat;
    }

    .charges-body {
        width: 100%;
        padding: 0 30px 0 30px;
    }

    .footer-official {
        width: 100%;
        object-fit: fill;
        position: absolute;
        bottom: 0;
    }
</style>

<div class="bill-body">
    {{-- <img src="{{ URL::asset('imgs/header_official_long.png') }}" class="header-official"> --}}
    <div class="header-official-bg">
        <div style="padding: 240px 30px 0 30px;">
            <div class="row">
                <div class="col-md-8">
                    <p class="no-pads"><span class="text-muted">Account Number: </span><strong class="text-success">{{ $accountNumber }}</strong></p>
                    <h4 class="no-pads">{{ $accountMaster != null ? $accountMaster->ConsumerName : '-' }}</h4>
                    <p class="no-pads">{{ $accountMaster != null ? ($accountMaster->ConsumerAddress . ' • ' . AccountMaster::getTypes($accountMaster->ConsumerType)) : '-' }}</p>
                    <p class="no-pads"><span class="text-muted">Meter Number: </span><span>{{ $accountMaster != null ? $accountMaster->MeterNumber : '' }}</span></p>
                    <p class="no-pads"><span class="text-muted">Subscriber Number: </span><span>{{ $bill != null ? $bill->Remarks : '' }}</span></p>
                </div>

                <div class="col-md-4">
                    <p class="no-pads text-right" style="padding-right: 9px !important;">Billing for</p>
                    {{-- FOR BIR --}}
                    {{-- <p class="no-pads text-right" style="padding-right: 64px !important;">Billing Invoice</p> --}}
                    {{-- <p class="no-pads text-right text-success" style="font-size: 1.2em; padding-right: 64px !important;"><strong>BI1000000000000001</strong></p> --}}
                    <h3 class="text-right" style="padding-right: 9px !important;"><strong>{{ date('F Y', strtotime($period)) }}</strong></h3>
                    <p class="no-pads text-right" style="padding-right: 9px !important;">Bill No.: {{ $bill != null ? $bill->BillNumber : '-' }}</p>
                </div>
            </div>

            <div class="divider my-2"></div>

            {{-- card techical details --}}
            <div class="table-responsive">
                <div class="row">
                    <div class="col-md-9">
                        @if ($accountMaster->ComputeMode === 'NetMetered')
                            <p class="no-pads text-muted text-sm">Bill Summary</p>
                            <table class="table table-sm table-borderless" style="background-color: transparent !important;">
                                <tbody style="background-color: transparent !important;">
                                    <tr>
                                        <td class="text-right">Date Start : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? date('M d, Y', strtotime($bill->ServiceDateFrom)) : '-' }}</td>
                                        <td></td>
                                        <td style="padding-left: 9px !important;"><strong>Received</strong></td>
                                        <td style="padding-left: 9px !important;"><strong>Delivered</strong></td>
                                        <td class="text-right">Add. KWH : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->AdditionalKWH : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Date End : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? date('M d, Y', strtotime($bill->ServiceDateTo)) : '-' }}</td>
                                        <td class="text-right">Prev. Reading : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->PowerPreviousReading : '-' }}</td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->NetPrevReading : '-' }}</td>
                                        <td class="text-right">Demand :</td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->DemandKW : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Due Date : </td>
                                        <td style="padding-left: 9px !important;" class="text-danger"><strong>{{ $bill != null ? date('M d, Y', strtotime($bill->DueDate)) : '-' }}</strong></td>
                                        <td class="text-right">Pres. Reading : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->PowerPresentReading : '-' }}</td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->NetPresReading : '-' }}</td>
                                        <td class="text-right">Add. Demand KW : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->AdditionalKWDemand : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Multiplier : </td>
                                        <td style="padding-left: 9px !important;">{{ $meterInfo != null ? $meterInfo->Multiplier : '-' }}</td>
                                        <td class="text-right">kWH Used : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->PowerKWH : '0.0' }}</td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->NetPowerKWH : '0.0' }}</td>
                                        <td class="text-right">Coreloss : </td>
                                        <td style="padding-left: 9px !important;">{{ $accountMaster != null ? $accountMaster->CoreLoss : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="no-pads text-muted text-sm">Bill Summary</p>
                            <table class="table table-sm table-borderless" style="background-color: transparent !important;">
                                <tbody style="background-color: transparent !important;">
                                    <tr>
                                        <td class="text-right">Date Start : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? date('M d, Y', strtotime($bill->ServiceDateFrom)) : '-' }}</td>
                                        <td class="text-right">Prev. Reading : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->PowerPreviousReading : '-' }}</td>
                                        <td class="text-right">Add. KWH : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->AdditionalKWH : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Date End : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? date('M d, Y', strtotime($bill->ServiceDateTo)) : '-' }}</td>
                                        <td class="text-right">Pres. Reading : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->PowerPresentReading : '-' }}</td>
                                        <td class="text-right">Demand :</td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->DemandKW : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Due Date : </td>
                                        <td style="padding-left: 9px !important;" class="text-danger"><strong>{{ $bill != null ? date('M d, Y', strtotime($bill->DueDate)) : '-' }}</strong></td>

                                        <td class="text-right">Multiplier : </td>
                                        <td style="padding-left: 9px !important;">{{ $meterInfo != null ? $meterInfo->Multiplier : '-' }}</td>
                                        <td class="text-right">Add. Demand KW : </td>
                                        <td style="padding-left: 9px !important;">{{ $bill != null ? $bill->AdditionalKWDemand : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>   
                                        <td></td>
                                        <td></td>                                   
                                        <td class="text-right">Coreloss : </td>
                                        <td style="padding-left: 9px !important;">{{ $accountMaster != null ? $accountMaster->CoreLoss : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="col-md-3">
                        @if ($accountMaster->ComputeMode === 'NetMetered')
                            <p class="no-pads text-right">Solar Generation</p>
                            <h4 class="no-pads text-right">{{ $bill != null ? $bill->NetPowerKWH : '0.0' }}</h4>
                            <p class="no-pads text-right">Consumed from BOHECO I</p>
                            <h4 class="no-pads text-right">{{ $bill != null ? $bill->PowerKWH : '0.0' }}</h4>
                        @else
                            <p class="no-pads text-right">Total kWH Used</p>
                            <h4 class="no-pads text-right">{{ $bill != null ? $bill->PowerKWH : '0.0' }}</h4>
                        @endif
                    </div>
                </div>
            </div>

            <div class="divider mb-2"></div>

            {{-- charges body --}}
            <p class="no-pads text-muted text-sm">Bill Charges</p>
            <div class="table-responsive">
                <table class="table table-borderless table-sm">
                    <thead>
                        <th>CHARGES</th>
                        <th></th>
                        <th class="text-right">RATE</th>
                        <th class="text-right" style="padding-right: 25px !important;">AMOUNT</th>
                        <th style="padding-left: 25px !important;">CHARGES</th>
                        <th></th>
                        <th class="text-right">RATE</th>
                        <th class="text-right">AMOUNT</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" style="padding-right: 25px !important;"><strong>Generation Charges</strong></td>
                            <td colspan="4" style="padding-left: 25px !important;"><strong>Other Charges</strong></td>
                        </tr>
                        {{-- GENERATION, LIFELINE --}}
                        <tr>
                            <td class="indent-1">Generation System</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->GenerationSystemCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->GenerationSystemAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Lifeline Subsidy Charge/Discount</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->LifelineSubsidyCharge, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->LifelineSubsidyAmt, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- OVER UNDER GENERATION, OVER UNDER LIFELINE --}}
                        <tr>
                            <td class="indent-1">Other Generation Adj.</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item7, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $billExtension != null ? (number_format($billExtension->Item18, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Other Lifeline Rate Adj.</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item10, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->Item21, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- TRANSMISSION SYSTEM, RFSC --}}
                        <tr>
                            <td class="indent-1">Transmission Delivery</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->TransmissionSystemCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->TransmissionSystemAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">RFSC</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->MCC, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->Item10, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- TRANSMISSION DEMAND, FIT ALL --}}
                        <tr>
                            <td class="indent-1">Transmission Delivery</td>
                            <td>Per kW</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->TransmissionDemandCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->TransmissionDemandAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Feed-In Tariff Allow(FIT-ALL)</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->PPARefund, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->Item4, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- OVER UNDER TRANSMISSION DEMAND, OVER UNDER SENIOR CITIZEN --}}
                        <tr>
                            <td class="indent-1">Other Transmission Adj.</td>
                            <td>Per kW</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item8, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $billExtension != null ? (number_format($billExtension->Item19, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Other Senior Citizen Adj.</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item11, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->Item22, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- SYSTEM LOSS, OTHER CHARGES --}}
                        <tr>
                            <td class="indent-1">System Loss</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->SystemLossCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->SystemLossAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Other Charges</td>
                            <td>Per kWH</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->Others, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- OVER UNDER SYSTEM LOSS **************************************************************************** --}}
                        <tr>
                            <td class="indent-1">Other System Loss Adj. </td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item9, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $billExtension != null ? (number_format($billExtension->Item20, 2)) : '0.00' }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="padding-right: 25px !important;"><strong>Distribution Charges</strong></td>
                            <td colspan="4" style="padding-left: 25px !important;"><strong>VAT Charges</strong></td>
                        </tr>
                        {{-- DISTRIBUTION SYSTEM, GENERATION VAT --}}
                        <tr>
                            <td class="indent-1">Distribution Network</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->DistributionSystemCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->DistributionSystemAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Generation</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item3, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->GenerationVAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- DISTRIBUTION DEMAND, TRANSMISSION VAT --}}
                        <tr>
                            <td class="indent-1">Distribution Network</td>
                            <td>Per kW</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->DistributionDemandCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->DistributionDemandAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Transmission</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item4, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->TransmissionVAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- SUPPLY RETAIL, SYSTEM LOSS VAT --}}
                        <tr>
                            <td class="indent-1">Retail Electric Service</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->SupplySystemCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->SupplySystemAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">System Loss</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->CrossSubsidyCreditCharge, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->SLVAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- SUPPLY RETAIL MINIMUM, DISTRIBUTION VAT --}}
                        <tr>
                            <td class="indent-1">Retail Electric Service</td>
                            <td>Per Cons.</td>
                            <td class="text-right"></td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->SupplyRetailCustomerAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">Distribution</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item2, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->DistributionVAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- METERING SYSTEM, DAA VAT --}}
                        <tr>
                            <td class="indent-1">Metering System Charge</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->MeteringSystemCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->MeteringSystemAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">DAA</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->DAA_VAT, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->DAA_VAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- METERING RETAIL, ACRM VAT --}}
                        <tr>
                            <td class="indent-1">Metering Retail/Cust.</td>
                            <td>Per Cons.</td>
                            <td class="text-right"></td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->MeteringRetailCustomerAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;">ACRM</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->ACRM_VAT, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->ACRM_VAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- OTHER VAT --}}
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="indent-1" style="padding-left: 25px !important;">Others</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item2, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->OthersVAT, 4)) : '0.00' }}</td>
                        </tr>
                        
                        <tr>
                            <td colspan="4" style="padding-right: 25px !important;"><strong>Universal Charges</strong></td>
                            <td></td>
                        </tr>
                        {{-- MISSIONARY ELECTRIFICATION --}}
                        <tr>
                            <td class="indent-1">Missionary Electrification</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->MissionaryElectrificationCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->MissionaryElectrificationAmt, 2)) : '0.00' }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        {{-- NPC STRANDED DEBT, TRANSFORMER RENTAL ******************************************************************** --}}
                        <tr>
                            <td class="indent-1">NPC Stranded Debts</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->FPCAAdjustmentCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->FPCAAdjustmentAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Transformer Rental</strong></td>
                            <td>Per Month</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->ACRM_VAT, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->ACRM_VAT, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- NPC STRANDED CONTRACT COSTS, SENIOR CITIZEN SUBSIDY/DISCOUNT --}}
                        <tr>
                            <td class="indent-1">NPC Stranded Con. Cost</td>
                            <td>Per kWH</td>
                            <td class="text-right">0.0000</td>
                            <td class="text-right" style="padding-right: 25px !important;">0.00</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Senior Citizen Subsidy/Discount</strong></td>
                            <td></td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->SeniorCitizenSubsidyCharge, 4)) : '0.00' }}</td>
                            <td class="text-right">{{ $bill != null ? ($bill->SeniorCitizenDiscount != null ? number_format($bill->SeniorCitizenDiscount, 2) : number_format($bill->SeniorCitizenSubsidy, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- ENVIRONMENTAL CHARGE, MANDATORY RATE --}}
                        <tr>
                            <td class="indent-1">Environmental</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->EnvironmentalCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->EnvironmentalAmt, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Mandatory Rate Reduction</strong></td>
                            <td>Per KWH</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                        </tr>
                        {{-- ACRM-TAFPPCA --}}
                        <tr>
                            <td class="indent-1">ACRM - TAFPPCA</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->ACRM_TAFPPCACharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->ACRM_TAFPPCA, 2)) : '0.00' }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        {{-- ACRM - TAFxA --}}
                        @php
                            // get AMOUNT DUE
                            $allTaxes = AccountMaster::getTotalBillTaxes($bill, $billExtension);
                        @endphp
                        <tr>
                            <td class="indent-1">ACRM - TAFxA</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->ACRM_TAFxACharge, 6)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->ACRM_TAFxA, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Amount Due</strong></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ $bill != null ? (number_format($bill->NetAmount - $allTaxes, 2)) : '0.00' }}</td>
                        </tr>
                        {{-- DAA GRAM, SENIOR CITIZEN SUBSIDY/DISCOUNT --}}
                        <tr>
                            <td class="indent-1">DAA - GRAM</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->DAA_GRAMCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->DAA_GRAM, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Total VAT Amount</strong></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ number_format($allTaxes, 2) }}</td>
                        </tr>
                        {{-- DAA ICERA, KATAS NG VAT --}}
                        <tr>
                            <td class="indent-1">DAA - ICERA</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->DAA_ICERACharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->DAA_ICERA, 2)) : '0.00' }}</td>
                            <td class="indent-1" style="padding-left: 25px !important;"><strong>Katas ng VAT</strong></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">{{ $billExtension != null ? (number_format($billExtension->Item7, 4)) : '0.00' }}</td>
                        </tr>

                        {{-- NET METERING Charges --}}
                        @if ($accountMaster->ComputeMode === 'NetMetered')
                            <tr>
                                <td colspan="4" style="padding-right: 25px !important;"><strong>Consumer Charges to BOHECO I</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 25px !important;">Generation Charges (Delivered)</td>
                                <td>Per kWH</td>
                                <td class="text-right">{{ $rates != null ? (number_format($rates->GenerationSystemCharge, 4)) : '0.00' }}</td>
                                <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->NetGenerationAmount, 2)) : '0.00' }}</td>
                                <td class="indent-1" style="padding-left: 25px !important; border-top: 1px solid #aeaeae;"><strong>Consumption from BOHECO I</strong></td>
                                <td style="border-top: 1px solid #878787;"></td>
                                <td style="border-top: 1px solid #878787;"></td>
                                <td class="text-right" style="border-top: 1px solid #878787;">{{ $bill != null ? (number_format($bill->NetAmount, 2)) : '0.00' }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-left: 25px !important;">Residual Credit Earned in Prior Months</td>
                                <td class="text-right" style="padding-right: 25px !important;">{{ $billPrev != null ? ($billPrev->NetGenerationAmount != null && $billPrev->NetGenerationAmount < 0 ? ('(' . number_format($billPrev->NetGenerationAmount, 2) . ')') : '0.0') : '0.00' }}</td>
                                <td colspan="3" class="indent-1" style="padding-left: 25px !important;"><strong>Less Total Generation</strong></td>
                                <td class="text-right">{{ $bill != null ? ('(' . number_format($bill->NetGenerationAmount, 2) . ')') : '0.00' }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="3" class="indent-1" style="padding-left: 25px !important;"><strong>Less Total Residual Credit</strong></td>
                                <td class="text-right">{{ $billPrev != null ? ($billPrev->NetMeteringNetAmount != null && floatval($billPrev->NetMeteringNetAmount) < 0 ? ('(' . number_format(abs($billPrev->NetMeteringNetAmount), 2) . ')') : '0.0') : '0.00' }}</td>
                            </tr>
                        @endif
                        
                        <tr>
                            <td colspan="4" style="padding-right: 25px !important;"><strong>Pass-Through Taxes</strong></td>
                            <td></td>
                        </tr>
                        {{-- FRANCHISE TAX --}}
                        <tr>
                            <td class="indent-1">Franchise Tax</td>
                            <td></td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->FBHCCharge, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $bill != null ? (number_format($bill->FBHCAmt, 2)) : '0.00' }}</td>
                            <td colspan="4" class="text-right">Current Amount Due</td>
                        </tr>
                        {{-- BUSINESS TAX --}}
                        <tr>
                            <td class="indent-1">Business Tax</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $ratesExtension != null ? (number_format($ratesExtension->Item6, 4)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $billExtension != null ? (number_format($billExtension->Item16, 2)) : '0.00' }}</td>
                            <td colspan="4" rowspan="2" class="text-right">
                                @if ($accountMaster->ComputeMode === 'NetMetered')
                                    <h2>₱ <strong>{{ $bill != null ? (number_format($bill->NetMeteringNetAmount, 2)) : '0.00' }}</strong></h2>
                                @else 
                                    <h2>₱ <strong>{{ $bill != null ? (number_format($bill->NetAmount, 2)) : '0.00' }}</strong></h2>
                                @endif
                            </td>
                        </tr>
                        {{-- REAL PROPERTY TAX --}}
                        <tr>
                            <td class="indent-1">Real Property Tax</td>
                            <td>Per kWH</td>
                            <td class="text-right">{{ $rates != null ? (number_format($rates->ACRM_TAFxACharge, 6)) : '0.00' }}</td>
                            <td class="text-right" style="padding-right: 25px !important;">{{ $billExtension != null ? (number_format($billExtension->Item17, 2)) : '0.00' }}</td>
                    </tbody>
                </table>
            </div>

            {{-- arrears --}}
            @if ($arrears != null && $arrears->ArrearsTotal > 0)
                <div class="divider mb-2"></div>

                <p class="no-pads text-muted text-sm">Arrears</p>

                <div class="row mt-1">
                    <div class="col-md-4">
                        <p class="indent-1 no-pads"><span class="text-muted">Total No. of Months: </span> {{ $arrears->ArrearsCount }}</p>
                        <p class="indent-1 no-pads"><span class="text-muted">Total Arears Amount: </span> <strong>₱ {{ number_format($arrears->ArrearsTotal, 2) }}</strong></p>
                    </div>
                    <div class="col-md-8">
                        <p class="no-pads text-sm"><strong>NOTE </strong> <span>that these arrears figures DOES NOT contain surcharge/penalty charges yet. You may visit our area offices, 
                            or call our mobile numbers for more information about your arrears/unsettled bills.</span></p>
                    </div>
                </div>
            @endif

            {{-- discounts --}}
            @if ($billsDcrRevisionView != null)
                <div class="divider my-2"></div>

                <p class="no-pads text-muted text-sm">Available Discounts</p>

                <div class="row mt-1">
                    <div class="col-md-4">
                        <p class="indent-1 no-pads"><span class="text-muted">Discount: </span> <strong>₱ {{ number_format($billsDcrRevisionView->NetAmountLessCharges * .01, 2) }}</strong></p>
                        <p class="indent-1 no-pads"><span class="text-muted">WT 2306: </span> <strong>₱ {{ number_format($billsDcrRevisionView->Form2306, 2) }}</strong></p>
                        <p class="indent-1 no-pads"><span class="text-muted">WT 2307: </span> <strong>₱ {{ number_format($billsDcrRevisionView->Form2307, 2) }}</strong></p>
                    </div>
                    <div class="col-md-8">
                        <p class="no-pads text-sm"><strong>NOTE:</strong> 
                            <br>
                            <span>1. AVAILABLE DISCOUNT FIGURE(S) are ONLY applicable to those Commercial and Industrial consumers whose KWH
                                consumptions are greater than 1000, and should be payed before the DUE DATE.</span>
                            <br>
                            <span>2. 2306 & 2307 will be deducted on the current amount due ONLY IF you can provide us the required documents.</span>
                            <br>
                            <span>3. All available deductibles are not automatically deducted on your current bill, not unless you provide us the required documents.</span>
                        </p>
                    </div>
                </div>
            @endif

            {{-- BIR FOOTER --}}
            {{-- <div class="row mt-3">
                <div class="col-md-6">
                    <p class="no-pads text-sm">Generated By: BOHECO I System</p>
                    <p class="no-pads text-sm">Version: v2024</p>
                    <p class="no-pads text-sm">User: 1</p>
                </div>

                
                <div class="col-md-6">
                    <p class="no-pads text-sm">AC No.: </p>
                    <p class="no-pads text-sm">Date Issued:</p>
                    <p class="no-pads text-sm">Series Range: BI1000000000000001 to BI9999999999999999</p>
                </div>
            </div> --}}
        </div>

        {{-- footer --}}
        <img src="{{ URL::asset('imgs/footer_long.png') }}" alt="" class="footer-official">
    </div>
</div>