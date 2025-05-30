@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Member Consumers</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($memberConsumers, ['route' => ['memberConsumers.update', $memberConsumers->Id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="Id" id="Membership_Id" value="{{ $memberConsumers->Id }}">
                    <input type="hidden" name="Office" id="Office" value="{{ $memberConsumers->Office }}">
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
@endsection
