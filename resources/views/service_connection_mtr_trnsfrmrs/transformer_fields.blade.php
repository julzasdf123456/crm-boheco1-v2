<!-- Transformer -->
<div class="col-lg-12">
    <div class="card  card-danger card-outline">
        <div class="card-header">
            <span class="card-title">Transformer</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 pr-25">
                    <div class="row">
                        <!-- Transformerbrand Field -->
                        <div class="form-group col-sm-12">
                            <div class="row">
                                <div class="col-lg-4 col-md-5">
                                    {!! Form::label('TransformerBrand', 'Transformer Brand') !!}
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-charging-station"></i></span>
                                        </div>
                                        {!! Form::select('TransformerBrand', [
                                            'Dong-A'    =>  'Dong-A', 
                                            'Dong-Mi'   =>  'Dong-Mi',
                                            'Shihlin'   =>  'Shihlin',
                                            'Wagner'    =>  'Wagner',
                                            'Wagner'    =>  'Wagner',
                                            'FBF'       =>  'FBF',
                                            'JFI'       =>  'JFI',
                                            'Eaglerise' =>  'Eaglerise',
                                            'Frank Lin' =>  'Frank Lin',
                                            'ABB'       =>  'ABB',
                                            'Ever Power'=>  'Ever Power',
                                            'Ekko'      =>  'Ekko',
                                            ], null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Transformerbrand Field -->
                    </div>
                </div>
                <div class="col-sm-6 pl-25">
                    <div class="row">
                        <!-- TransformerRating Field -->
                        <div class="form-group col-sm-12">
                            <div class="row">
                                <div class="col-lg-4 col-md-5">
                                    {!! Form::label('TransformerRating', 'Transformer Rating') !!}
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-charging-station"></i></span>
                                        </div>
                                        
                                        {!! Form::select('TransformerRating', [
                                            '5 KVA'    =>  '5 KVA',
                                            '10 KVA'   =>  '10 KVA',
                                            '15 KVA'   =>  '15 KVA',
                                            '25 KVA'   =>  '25 KVA',
                                            '37.5 KVA' =>  '37.5 KVA',
                                            '50 KVA'   =>  '50 KVA',
                                            '75 KVA'   =>  '75 KVA',
                                            '100 KVA'  =>  '100 KVA',
                                            '167 KVA'  =>  '167 KVA',
                                            '250 KVA'  =>  '250 KVA',
                                            '330 KVA'  =>  '330 KVA',
                                            '500 KVA'  =>  '500 KVA',
                                            ], null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div><!-- End TransformerRating Field -->
                    </div>
                </div>
            </div>

            <!-- transformer table form row-->
            <div class="row">
                <!-- TABLE FORM -->
                <table class="table border-top-none bottom-bordered" style="width: 100%;">
                    <tr>
                        <th style="width:15.6%"></th>
                        <th>Phase A</th>
                        <th>Phase B</th>
                        <th>Phase C</th>
                    </tr>
                    <tr>
                        <th>Serial Number</th>
                        <td>
                            {!! Form::text('TransformerNumberA', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                        <td>
                            {!! Form::text('TransformerNumberB', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                        <td>
                            {!! Form::text('TransformerNumberC', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th>Coreloss</th>
                        <td>
                            {!! Form::text('CorelossPhaseA', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                        <td>
                            {!! Form::text('CorelossPhaseB', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                        <td>
                            {!! Form::text('CorelossPhaseC', null, ['class' => 'form-control', 'maxlength' => 150, 'maxlength' => 150]) !!}
                        </td>
                    </tr>
                </table>
            </div>

            
            <div class="row">
                <div class="col-sm-6 pr-25">
                    <div class="row">
                        <!-- Transformerownership Field -->
                        <div class="form-group col-sm-12">
                            <div class="row">
                                <div class="col-lg-4 col-md-5">
                                    {!! Form::label('TransformerOwnership', 'Ownership') !!}
                                </div>

                                <div class="col-lg-8 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-charging-station"></i></span>
                                        </div>
                                        {!! Form::select(
                                            'TransformerOwnership',
                                            [env('APP_COMPANY_ABRV') => env('APP_COMPANY_ABRV'), 'Privately Owned' => 'Privately Owned'],
                                            null,
                                            ['class' => 'form-control'],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 pl-25">
                    <div class="row">
                        <!-- Transformerownershiptype Field -->
                        <div class="form-group col-sm-12">
                            <div class="row">
                                <div class="col-lg-4 col-md-5">
                                    {!! Form::label('TransformerOwnershipType', 'Ownership Category') !!}
                                </div>

                                <div class="col-lg-8 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-charging-station"></i></span>
                                        </div>
                                        {!! Form::select('TransformerOwnershipType', ['Shared' => 'Shared', 'Solo' => 'Solo'], null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




