<div class="content">
    <button class="btn btn-xs btn-success float-right" data-toggle="modal" data-target="#modal-print-ledger">Print Ledger</button>
    <button class="btn btn-xs btn-success float-right" data-toggle="modal" style="margin-right: 5px; margin-bottom: 5px;" data-target="#modal-ledger-history">View Full Ledger</button>
    <button class="btn btn-xs btn-default float-right" style="margin-right: 5px; margin-bottom: 5px;" data-toggle="modal" data-target="#modal-reading-history">View Reading History</button>

    @if ($bills == null)
        <p class="center-text"><i>No billing history recorded</i></p>
    @else
        <div class="card shadow-none"  style="height: 75vh; width: 100%;">
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <th>Bill No.</th>
                        <th>Billing Mon.</th>
                        <th class="text-center">Prev.<br>Read</th>
                        <th class="text-center">Pres.<br>Read</th>
                        <th class="text-center">Kwh</th>
                        <th class="text-center" title="Multiplier">x*</th>
                        <th class="text-center">Total<br>Kwh</th>
                        {{-- <th class="text-right">Rate</th> --}}
                        <th class="text-center">Amount</th>
                        <th class="text-center">OR No.</th>
                        <th class="text-center">Payment<br>Date</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($bills as $item)
                            <tr>
                                <td><i class="fas {{ $item->ORNumber != null ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger' }} ico-tab"></i><a href="{{ route('bills.show-bill', [$item->AccountNumber, $item->ServicePeriodEnd]) }}">{{ $item->BillNumber }}</a></td>
                                
                                <td>{{ date('F Y', strtotime($item->ServicePeriodEnd)) }}</td>
                                <td class="text-right">{{ $item->PowerPreviousReading }}</td>
                                <td class="text-right">{{ $item->PowerPresentReading }}</td>
                                <th class="text-right text-info">{{ is_numeric($meter->Multiplier) ? round(floatval($item->PowerKWH) * floatval($meter->Multiplier),2) : 'MULT_ERR' }}</th>
                                <th class="text-right text-warning">{{ $meter->Multiplier }}</th>
                                <th class="text-right text-primary">{{ $item->PowerKWH }}</th>
                                {{-- <td class="text-right">{{ $item->EffectiveRate != null ? number_format($item->EffectiveRate, 4) : '0' }}</td> --}}
                                <th class="text-right text-danger">P {{ $item->NetAmount != null ? (is_numeric($item->NetAmount) ? number_format($item->NetAmount, 2) : '0') : '0' }}</th>
                                <td class="text-right"><a href="">{{ $item->ORNumber != null ? $item->ORNumber : '-' }}</a></td>
                                <td class="text-right">{{ $item->ORDate != null ? date('F d, Y', strtotime($item->ORDate)) : '-' }}</td>
                                {{-- <td class="text-right">
                                    @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Data Administrator'])) 
                                        @if ($item->ORNumber == null)
                                            <a href="{{ route('bills.adjust-bill', [$item->id]) }}" class="btn btn-link btn-xs text-warning" title="Adjust Reading"><i class="fas fa-pen"></i></a>
                                            <button class="btn btn-link btn-xs text-danger" title="Cancel this Bill" onclick="requestCancel('{{ $item->id }}')"><i class="fas fa-ban"></i></button>
                                            <button class="btn btn-link btn-xs text-info" title="Mark as Paid (Application Adjustment)" onclick="markAsPaid('{{ $item->id }}')"><i class="fas fa-check-circle"></i></button>
                                        @endif
                                    @endif
                                    <a href="{{ route('bills.print-single-bill-new-format', [$item->id]) }}" class="btn btn-xs btn-link" title="Print New Formatted Bill"><i class="fas fa-print"></i></a>
                                    <a href="{{ route('bills.print-single-bill-old', [$item->id]) }}" class="btn btn-link btn-xs text-warning" title="Print Pre-Formatted Bill (Old)"><i class="fas fa-print"></i></a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    @endif
</div>

{{-- Reading History --}}
{{-- <div class="modal fade" id="modal-reading-history" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reading History</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <th>Billing Month</th>
                        <th>Reading</th>
                        <th>Reading Timestamp</th>
                        <th>Meter Reader</th>
                        <th>Remarks</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($readings as $item)
                            <tr>
                                <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                                <td>{{ $item->KwhUsed }}</td>
                                <td>{{ date('F d, Y h:i:s A', strtotime($item->ReadingTimestamp)) }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->Notes }}</td>
                                <td class="text-right">
                                <a href="{{ route('bills.zero-readings-view', [$item->id]) }}"><i class="fas fa-pen"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default float-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}

{{-- Ledger History --}}
{{-- <div class="modal fade" id="modal-ledger-history" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ledger</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-sm">
                    <thead>
                        <th>OR Number</th>
                        <th>OR Date</th>
                        <th class="text-right">Amount</th>
                        <th>Payment For</th>
                    </thead>
                    <tbody>
                        @foreach ($ledger as $item)
                            <tr>
                                <td><a href="{{ route('transactionIndices.browse-ors-view', [$item->id, $item->PaymentType]) }}">{{ $item->ORNumber }}</a></td>
                                <td>{{ $item->ORDate }}</td>
                                <td class="text-right">{{ number_format($item->Total, 2) }}</td>
                                <td>{{ $item->Source }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default float-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}

{{-- Print Ledger History --}}
{{-- <div class="modal fade" id="modal-print-ledger" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Print Ledger</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label for="FromLedger">From Year</label>
                        <input type="text" id="FromLedger" maxlength=4 class="form-control">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="ToLedger">To Year</label>
                        <input type="text" id="ToLedger" maxlength=4 class="form-control" value="{{ date('Y') }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="print-ledger"><i class="fas fa-print ico-tab-mini"></i>Print</button>
            </div>
        </div>
    </div>
</div> --}}

{{-- @push('page_scripts')
    <script>
        function requestCancel(id) {
            (async () => {
                const { value: text } = await Swal.fire({
                    input: 'textarea',
                    inputLabel: 'Remarks',
                    inputPlaceholder: 'Type your remarks here...',
                    inputAttributes: {
                        'aria-label': 'Type your remarks here'
                    },
                    title: 'Cancel this bill?',
                    showCancelButton: true
                })

                if (text) {
                    $.ajax({
                        url : '{{ route("bills.request-cancel-bill") }}',
                        type : 'GET',
                        data : {
                            id : id,
                            Remarks : text
                        },
                        success : function(res) {
                            Swal.fire('Cancel Request Successful', 'Your cancellation request has been forwarded to your Billing Head and is waiting for confirmation', 'success')
                        },
                        error : function(err) {
                            Swal.fire('Cancel Request Error', 'Contact support immediately', 'error')
                        }
                    })
                }
            })()
        }

        function markAsPaid(id) {
            (async () => {
                const { value: text } = await Swal.fire({
                    input: 'textarea',
                    inputLabel: 'Remarks/Notes',
                    inputPlaceholder: 'Type your remarks here...',
                    inputAttributes: {
                        'aria-label': 'Type your remarks here'
                    },
                    title: 'Mark this bill as Paid?',
                    text : 'Are you sure to make this an Application Adjustment?',
                    showCancelButton: true
                })

                if (text) {
                    $.ajax({
                        url : '{{ route("bills.mark-as-paid") }}',
                        type : 'GET',
                        data : {
                            id : id,
                            Remarks : text
                        },
                        success : function(res) {
                            Swal.fire('Application Adjustment Successful', 'Bill marked as paid!', 'success')
                            location.reload()
                        },
                        error : function(err) {
                            Swal.fire('Application Adjustment Error', 'Contact support immediately', 'error')
                        }
                    })
                }
            })()
        }

        $(document).ready(function() {
            $('#print-ledger').on('click', function() {
                var from = $('#FromLedger').val()
                var to = $('#ToLedger').val()

                if (jQuery.isEmptyObject(from) | jQuery.isEmptyObject(to)) {
                    Swal.fire({
                        title : 'Provide Years First!',
                        icon : 'error'
                    })
                } else {                   
                    window.location.href  = "{{ url('/service_accounts/print-ledger') }}" + "/{{ $serviceAccounts->id }}/" + from + "/" + to
                }
            })
        })
    </script>
@endpush --}}