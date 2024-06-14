@php
    use App\Models\ServiceConnections;
@endphp
@extends('layouts.app')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4 style="display: inline; margin-right: 15px;">New Energized BAPA Accounts</h4>
                    <i class="text-muted">Energized BAPA service connection accounts for activation</i>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    
    <div class="row">
        @if(session()->has('message'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            </div>
        @endif
        <div class="col-lg-12">
            <table class="table table-hover table-sm">
                <thead>
                    <th width="3%"></th>
                    <th>Account No.</th>
                    <th>Account Name</th>
                    <th>Account Address</th>
                    <th>Account Type</th>
                    <th>Application</th>
                    <th width="8%"></th>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($serviceConnections as $item)
                        <tr>
                            <th>{{ $i }}</th>
                            <td><a href="{{ route('serviceConnections.show', [$item->id]) }}" target="_blank">{{ $item->id }}</a></td>
                            <td>{{ $item->ServiceAccountName }} ({{ $item->AccountCount }})<i class="fas fa-check-circle text-primary" style="font-size: .75em;"></i></td>
                            <td>{{ ServiceConnections::getAddress($item) }}</td>
                            <td>{{ $item->AccountType }} ({{ $item->Alias }})</td>
                            <td>{{ $item->ConnectionApplicationType }}</td>
                            <td class="text-right" >
                                @if ($item->ConnectionApplicationType == 'Relocation')
                                    {{-- <a href="{{ route('serviceAccounts.relocation-form', [$item->AccountNumber, $item->id]) }}" title="Proceed relocating {{ $item->ServiceAccountName }}" ><i class="fas fa-arrow-circle-right text-success"></i></a> --}}
                                @elseif ($item->ConnectionApplicationType == 'Change Name')
                                    {{-- <a href="{{ route('serviceAccounts.confirm-change-name', [$item->id]) }}" title="Proceed Change Name"><i class="fas fa-arrow-circle-right text-success"></i></a> --}}
                                @else
                                    {{-- <a href="{{ route('accountMasters.account-migration-step-one', [$item->id]) }}" title="Proceed activating {{ $item->ServiceAccountName }}" ><i class="fas fa-arrow-circle-right text-success"></i></a> --}}
                                @endif                                
                            </td>
                        </tr>
                    @php
                        $i++;
                    @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection