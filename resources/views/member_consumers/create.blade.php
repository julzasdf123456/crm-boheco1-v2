@php 
    use App\Models\IDGenerator;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <p><strong><span class="badge-lg bg-success">Step 1</span>Register Member Consumer</strong></p>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-12">
            <div class="content px-3">

                @include('adminlte-templates::common.errors')

                <div class="card">

                    {!! Form::open(['route' => 'memberConsumers.store']) !!}

                    <div class="card-body">

                        <div class="row">
                            <input type="hidden" name="Id" id="Membership_Id" value="{{ IDGenerator::generateID() }}">
                            <input type="hidden" name="Office" id="Office" value="{{ env('APP_LOCATION') }}">
                            @include('member_consumers.fields')
                        </div>

                    </div>

                    <div class="card-footer">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('memberConsumers.index') }}" class="btn btn-default">Cancel</a>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
    
@endsection
