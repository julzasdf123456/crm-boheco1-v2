@php
    use App\Models\IDGenerator;
    use App\Models\TicketsRepository;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4>Create Ticket</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    {!! Form::open(['route' => 'tickets.store']) !!}
                    <div class="card-header">
                        <div class="form-group row">
                            <label for="DateTimeComplainLogged" class="col-md-4">Date and Time Complaint Logged/Called</label>
                            {!! Form::text('DateTimeComplainLogged', date('Y-m-d h:i:s'), ['id' => 'DateTimeComplainLogged', 'class' => 'form-control form-control-sm col-md-4', 'placeholder' => 'Datetime Log']) !!}

                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#DateTimeComplainLogged').datetimepicker({
                                        format: 'YYYY-MM-DD HH:mm:ss',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>
                    </div>
                    <div class="card-body">                
                        <div class="row">                 

                            @include('tickets.fields')

                            {{-- HIDDEN FIELDS --}}
                            <input type="hidden" name="id" value="{{ IDGenerator::generateID(); }}">

                            <input type="hidden" value="{{ Auth::id(); }}" name="UserId">

                            <input type="hidden" value="Received" name="Status">

                            <input type="hidden" value="{{ env("APP_LOCATION") }}" name="Office">

                            @if ($serviceAccount != null)  
                                <input type="hidden" value="{{ $serviceAccount->AccountNumber }}" name="AccountNumber">
                                <input type="hidden" value="{{ $serviceAccount->Pole }}" name="PoleNumber">
                            @endif  
                            
                        </div>
                    </div>

                    <div class="card-footer">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('tickets.index') }}" class="btn btn-default">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <span class="card-title">Ticket History <i class="text-muted">(newest to oldest)</i></span>
                    </div>

                    <div class="card-body">
                        @if ($history != null)
                            <div id="accordion">
                                @foreach ($history as $item)
                                    @php
                                        $parent = TicketsRepository::find($item->ParentTicket);
                                    @endphp
                                    <div class="card mb-0">
                                        <div class="card-header" id="heading-{{ $item->id }}">
                                            <h5 class="card-title mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#id-{{ $item->id }}" aria-expanded="true" aria-controls="id-{{ $item->id }}">
                                                    @if ($parent != null)
                                                        {{ $parent->Name }} - {{ $item->Name }}
                                                    @else
                                                        {{ $item->Name }}
                                                    @endif
                                                    
                                                </button>
                                            </h5>
                                            <div class="card-tools">
                                                <a href="{{ route('tickets.show', [$item->id]) }}" class="btn btn-tool"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    
                                        <div id="id-{{ $item->id }}" class="collapse" aria-labelledby="heading-{{ $item->id }}" data-parent="#accordion">
                                            <div class="card-body">
                                                <table class="table table-sm table-hover">
                                                    <tr>
                                                        <th>Address</th>
                                                        <td>{{ $item->Barangay }}, {{ $item->Town }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Reason</th>
                                                        <td>{{ $item->Reason }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>{{ $item->Status }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Date Filed</th>
                                                        <td>{{ date('F d, Y', strtotime($item->created_at)) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                  </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center"><i>No recorded history</i></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
