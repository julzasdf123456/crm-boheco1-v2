<?php

use App\Models\MemberConsumers;
use App\Models\ServiceConnections;
use App\Models\IDGenerator;

?>

@if($cond == 'new') 
    <input type="hidden" name="id" id="Membership_Id" value="{{ IDGenerator::generateID() }}">

    <!-- Connectionapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('ConnectionApplicationType', 'Application for: ') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>
                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="New Installation" checked>
                            <label class="form-check-label">New Installation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="Rewiring">
                            <label class="form-check-label">Rewiring</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="Street Lighting">
                            <label class="form-check-label">Street Lighting</label>
                        </div>
                    </div>   
                </div>
            </div>
        </div>  
    </div>

    <!-- Accountapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('AccountApplicationType', 'Application Type') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="permanent" name="AccountApplicationType" value="Permanent" checked>
                            <label class="form-check-label" for="permanent">Permanent</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="temporary" name="AccountApplicationType" value="Temporary">
                            <label class="form-check-label" for="temporary">Temporary</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="temporary-to-permanent" name="AccountApplicationType" value="Temporary to Permanent">
                            <label class="form-check-label" for="temporary-to-permanent">Temporary to Permanent</label>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>  
    </div>

    @push('page_scripts')
        <script type="text/javascript">
            $(document).ready(function(){
                $('#duration').hide();
                $('#TemporaryDurationInMonths').val('');
                $('#existing-acct-no').hide();
                $('#ExistingAccountNumber').val('');
            });
            $("input[name='AccountApplicationType']").change(function() {
                if (this.value == 'Temporary') {
                    // alert('Temporary');
                    $('#duration').show();
                    $('#TemporaryDurationInMonths').val('3');
                    $('#existing-acct-no').hide();
                    $('#ExistingAccountNumber').val('');
                } else if (this.value == 'Temporary to Permanent') {
                    $('#duration').hide();
                    $('#TemporaryDurationInMonths').val('');
                    $('#existing-acct-no').show();
                    $('#ExistingAccountNumber').val('');
                } else {
                    // alert('Permanent');
                    $('#duration').hide();
                    $('#TemporaryDurationInMonths').val('');
                    $('#existing-acct-no').hide();
                    $('#ExistingAccountNumber').val('');
                }
            });
        </script>
    @endpush

    {{-- Temporary Duration Field --}}
    <div class="form-group col-sm-12" id="duration">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('TemporaryDurationInMonths', 'Duration (in Months)') !!}
            </div>
    
            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    {!! Form::text('TemporaryDurationInMonths', null, ['class' => 'form-control form-control-sm','maxlength' => 255,'maxlength' => 255]) !!}
                </div>
            </div>
        </div> 
    </div>

    {{-- ExistingAccountNumber Field --}}
    <div class="form-group col-sm-12" id="existing-acct-no">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('ExistingAccountNumber', 'Existing Account Number') !!}
            </div>
    
            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    {!! Form::text('ExistingAccountNumber', null, ['class' => 'form-control form-control-sm','maxlength' => 255,'maxlength' => 255]) !!}
                </div>
            </div>
        </div> 
    </div>

    <!-- Accounttype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('AccountType', 'Classification of Service') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group">
                        @if ($accountTypes != null)
                            @foreach ($accountTypes as $item)
                            <div class="form-check" style="margin-left: 30px;">
                                <input class="form-check-input" type="radio" name="AccountType" id="{{ $item->id }}" value="{{ $item->id }}" {{ $item->AccountType=='RESIDENTIAL' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="{{ $item->id }}">{{ $item->AccountType }} ({{ $item->Alias }})</label>
                            </div>
                            @endforeach
                        @endif
                    </div> 
                </div>
            </div>
        </div>  
    </div>

    <!-- Phase Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('Phase', 'Phase') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Phase" value="1" checked>
                            <label class="form-check-label">1 (Single Phase)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Phase" value="3">
                            <label class="form-check-label">3 (Three Phase)</label>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>  
    </div>

    <!-- Accountapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('LoadCategory', 'Projected Load') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    {!! Form::number('LoadCategory', null, ['class' => 'form-control form-control-sm','maxlength' => 300,'maxlength' => 300, 'step' => 'any', 'placeholder' => '.25 if residential, greater than that if power loads', 'required' => true]) !!}                  
                </div>
            </div>
        </div>  
    </div>

    <!-- Indigent Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('Indigent', 'Indigent') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Indigent" value="" checked>
                            <label class="form-check-label">No</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Indigent" value="Yes">
                            <label class="form-check-label">Yes</label>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>  
    </div>

@else 
    <!-- Connectionapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('ConnectionApplicationType', 'Application for: ') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>
                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="New Installation" {{ $serviceConnections != null && $serviceConnections->ConnectionApplicationType=='New Installation' ? 'checked' : '' }}>
                            <label class="form-check-label">New Installation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="Rewiring" {{ $serviceConnections != null && $serviceConnections->ConnectionApplicationType=='Rewiring' ? 'checked' : '' }}>
                            <label class="form-check-label">Rewiring</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ConnectionApplicationType" value="Street Lighting" {{ $serviceConnections != null && $serviceConnections->ConnectionApplicationType=='Street Lighting' ? 'checked' : '' }}>
                            <label class="form-check-label">Street Lighting</label>
                        </div>
                    </div>   
                </div>
            </div>
        </div>  
    </div>

    <!-- Accounttype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('AccountType', 'Classification of Service') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group">
                        @if ($accountTypes != null)
                            @foreach ($accountTypes as $item)
                            <div class="form-check" style="margin-left: 30px;">
                                <input class="form-check-input" type="radio" name="AccountType" value="{{ $item->id }}" {{ $item->id==$serviceConnections->AccountType ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $item->AccountType }} ({{ $item->Alias }})</label>
                            </div>
                            @endforeach
                        @endif
                    </div> 
                </div>
            </div>
        </div>  
    </div>

    <!-- Accountapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('AccountApplicationType', 'Application Type') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    <div class="radio-group-horizontal-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="permanent" name="AccountApplicationType" value="Permanent" {{ $serviceConnections != null && $serviceConnections->AccountApplicationType=='Permanent' ? 'checked' : '' }}>
                            <label class="form-check-label" for="permanent">Permanent</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="temporary" name="AccountApplicationType" value="Temporary" {{ $serviceConnections != null && $serviceConnections->AccountApplicationType=='Temporary' ? 'checked' : '' }}>
                            <label class="form-check-label" for="temporary">Temporary</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="temporary-to-permanent" name="AccountApplicationType" value="Temporary to Permanent" {{ $serviceConnections != null && $serviceConnections->AccountApplicationType=='Temporary to Permanent' ? 'checked' : '' }}>
                            <label class="form-check-label" for="temporary-to-permanent">Temporary to Permanent</label>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>  
    </div>

    <!-- Accountapplicationtype Field -->
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                {!! Form::label('LoadCategory', 'Projected Load') !!} <span class="text-danger"><strong> *</strong></span>
            </div>

            <div class="col-lg-9 col-md-7"> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                    </div>

                    {!! Form::number('LoadCategory', $serviceConnections->LoadCategory, ['class' => 'form-control form-control-sm','maxlength' => 300,'maxlength' => 300, 'step' => 'any', 'placeholder' => 'Projected load in kVA', 'required' => true]) !!}                  
                </div>
            </div>
        </div>  
    </div>

    <input type="hidden" name="id" id="Membership_Id" value="{{ $serviceConnections->id }}">
@endif

@push('page_scripts')
    <script type="text/javascript">
        $('#DateOfApplication').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<div class="divider"></div>
<br>

<!-- Memberconsumerid Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('MemberConsumerId', 'Member Consumer ID') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                </div>
                {!! Form::text('MemberConsumerId', $cond=='new' ? ($memberConsumer != null ? $memberConsumer->ConsumerId : '') : $serviceConnections->MemberConsumerId, ['class' => 'form-control form-control-sm','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Serviceaccountname Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ServiceAccountName', 'Service Account Name') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                </div>
                {!! Form::text('ServiceAccountName', $cond=='new' ? MemberConsumers::serializeMemberNameFormal($memberConsumer) : $serviceConnections->ServiceAccountName, ['class' => 'form-control form-control-sm','maxlength' => 255,'maxlength' => 255]) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Accountcount Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountCount', 'Account Count') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search-plus"></i></span>
                </div>
                {!! Form::text('AccountCount', $cond=='new' ? (ServiceConnections::getAccountCount($memberConsumer->ConsumerId)+1) : $serviceConnections->AccountCount, ['class' => 'form-control form-control-sm','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Town Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('Town', 'Town') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                </div>
                {!! Form::select('Town', $towns, $cond=='new' ? ($memberConsumer->TownId != null ? $memberConsumer->TownId : '') : $serviceConnections->TownId, ['class' => 'form-control form-control-sm', 'required' => 'true']) !!}
            </div>
        </div>
    </div>    
</div>

<!-- Barangay Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('Barangay', 'Barangay') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                </div>
                {!! Form::select('Barangay', [], null, ['class' => 'form-control form-control-sm', 'required' => 'true']) !!}
            </div>
        </div>
    </div>    
</div>

<!-- Sitio Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('Sitio', 'Sitio') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                </div>
                {!! Form::text('Sitio', $cond=='new' ? $memberConsumer->Sitio : $serviceConnections->Sitio, ['class' => 'form-control form-control-sm','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'Sitio']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Contactnumbers Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ContactNumber', 'Contact Numbers') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                </div>
                {!! Form::number('ContactNumber', $cond=='new' ? $memberConsumer->ContactNumbers : $serviceConnections->ContactNumber, ['class' => 'form-control form-control-sm','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Contact Numbers']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Emailaddress Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('EmailAddress', 'Email Address') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope-open"></i></span>
                </div>
                {!! Form::text('EmailAddress', $cond=='new' ? $memberConsumer->EmailAddress : $serviceConnections->EmailAddress, ['class' => 'form-control form-control-sm','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Email Address']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Residence Number Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ResidenceNumber', 'Residence Number') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-store"></i></span>
                </div>
                {!! Form::text('ResidenceNumber', null, ['class' => 'form-control form-control-sm','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Residence Number']) !!}
            </div>
        </div>
    </div> 
</div>

<div class="divider"></div>
<br>

<!-- Accountorganization Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountOrganization', 'Account Classification') !!} <span class="text-danger"><strong> *</strong></span>
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                </div>
                {!! Form::select('AccountOrganization', ['Individual' => 'Individual', 'BAPA' => 'BAPA', 'ECA' => 'ECA', 'Clustered' => 'Clustered'], $cond=='new' ? '' : $serviceConnections->AccountOrganization, ['class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Organizationaccountnumber Field -->
<div id="organizationNo" class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('OrganizationAccountNumber', 'Organization Account No') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                </div>
                {!! Form::text('OrganizationAccountNumber', null, ['class' => 'form-control form-control-sm','maxlength' => 100,'maxlength' => 100, 'placeholder' => 'Organization Account Number']) !!}
            </div>
        </div>
    </div> 
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#AccountOrganization').on('change', function() {
            if (this.value == 'Individual') {
                $('#organizationNo').hide();
            } else {
                $('#organizationNo').show();
            }
        });
    </script>
@endpush

<!-- Station Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('StationCrewAssigned', 'Station Crew') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-hard-hat"></i></span>
                </div>
                <select name="StationCrewAssigned" id="StationCrewAssigned" class="form-control form-control-sm">
                    <option value="">-- Select --</option>
                    @foreach ($crew as $item)
                        <option value="{{ $item->id }}">{{ $item->StationName }}</option>
                    @endforeach
                </select>
                {{-- {!! Form::select('StationCrewAssigned', $crew, $cond=='new' ? '' : $serviceConnections->StationCrewAssigned, ['class' => 'form-control form-control-sm']) !!} --}}
            </div>
        </div>
    </div>  
</div>

<!-- BuildingType Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('BuildingType', 'Building Type') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-tools"></i></span>
                </div>
                {!! Form::select('BuildingType', ['Concrete' => 'Concrete', 'Non-Concrete' => 'Non-Concrete'], $cond=='new' ? '' : $serviceConnections->BuildingType, ['class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Dateofapplication Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('DateOfApplication', 'Date of Application') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                </div>
                    {!! Form::text('DateOfApplication', $cond=='new' ? date('Y-m-d') : $serviceConnections->DateOfApplication, ['class' => 'form-control form-control-sm','id'=>'DateOfApplication']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Isnihe Field -->
<input type="hidden" name="IsNIHE" value="NO">

<!-- Notes Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('Notes', 'Notes/Comments') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-comments"></i></span>
                </div>
                {!! Form::text('Notes', null, ['class' => 'form-control form-control-sm','maxlength' => 100,'maxlength' => 100, 'placeholder' => 'Notes or Comments']) !!}
            </div>
        </div>
    </div> 
</div>

{{-- 
<div class="divider"></div>
<br> --}}

{{-- OR UPDATING ON ADMINS --}}
<!-- OR Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ORNumber', 'OR Number') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                </div>
                {!! Form::text('ORNumber', null, ['class' => 'form-control form-control-sm','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'OR Number']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- OR Date Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ORDate', 'Payment Date') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                </div>
                    {!! Form::text('ORDate', null, ['class' => 'form-control form-control-sm','id'=>'ORDate']) !!}
            </div>
        </div>
    </div> 
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#ORDate').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<p id="Def_Brgy" style="display: none;">{{ $cond=='new' ? $memberConsumer->BarangayId : $serviceConnections->Barangay }}</p>