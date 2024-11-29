<!-- Accountnumber Field -->
<div class="col-sm-12">
    {!! Form::label('AccountNumber', 'Accountnumber:') !!}
    <p>{{ $meterUpdateLogs->AccountNumber }}</p>
</div>

<!-- Oldmeternumber Field -->
<div class="col-sm-12">
    {!! Form::label('OldMeterNumber', 'Oldmeternumber:') !!}
    <p>{{ $meterUpdateLogs->OldMeterNumber }}</p>
</div>

<!-- Newmeternumber Field -->
<div class="col-sm-12">
    {!! Form::label('NewMeterNumber', 'Newmeternumber:') !!}
    <p>{{ $meterUpdateLogs->NewMeterNumber }}</p>
</div>

<!-- Userupdated Field -->
<div class="col-sm-12">
    {!! Form::label('UserUpdated', 'Userupdated:') !!}
    <p>{{ $meterUpdateLogs->UserUpdated }}</p>
</div>

