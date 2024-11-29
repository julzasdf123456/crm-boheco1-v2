<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="meter-update-logs-table">
            <thead>
            <tr>
                <th>Accountnumber</th>
                <th>Oldmeternumber</th>
                <th>Newmeternumber</th>
                <th>Userupdated</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($meterUpdateLogs as $meterUpdateLogs)
                <tr>
                    <td>{{ $meterUpdateLogs->AccountNumber }}</td>
                    <td>{{ $meterUpdateLogs->OldMeterNumber }}</td>
                    <td>{{ $meterUpdateLogs->NewMeterNumber }}</td>
                    <td>{{ $meterUpdateLogs->UserUpdated }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['meterUpdateLogs.destroy', $meterUpdateLogs->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('meterUpdateLogs.show', [$meterUpdateLogs->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('meterUpdateLogs.edit', [$meterUpdateLogs->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $meterUpdateLogs])
        </div>
    </div>
</div>
