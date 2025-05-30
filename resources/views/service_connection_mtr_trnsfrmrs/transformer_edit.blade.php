@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Service Connection Transformer</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3 edit-service-con-mtr-trnsfrmr">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($serviceConnectionMtrTrnsfrmr, ['route' => ['serviceConnectionMtrTrnsfrmrs.update', $serviceConnectionMtrTrnsfrmr->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="id" value="{{ $serviceConnectionMtrTrnsfrmr->id }}">

                    <input type="hidden" name="ServiceConnectionId" value="{{ $serviceConnectionMtrTrnsfrmr->ServiceConnectionId }}">

                    @include('service_connection_mtr_trnsfrmrs.transformer_fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
