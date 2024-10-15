<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillsReadings extends Model
{
    public $table = 'Readings';

    protected $connection = 'sqlsrvbilling';

    public $fillable = [
        'AccountNumber',
        'ReadingDate',
        'ReadBy',
        'PowerReadings',
        'DemandReadings',
        'FieldFindings',
        'MissCodes',
        'Remarks'
    ];

    protected $casts = [
        'ServicePeriodEnd' => 'datetime',
        'AccountNumber' => 'string',
        'ReadingDate' => 'datetime',
        'ReadBy' => 'string',
        'PowerReadings' => 'decimal:2',
        'DemandReadings' => 'float',
        'FieldFindings' => 'string',
        'MissCodes' => 'string',
        'Remarks' => 'string'
    ];

    public static array $rules = [
        'AccountNumber' => 'required|string|max:20',
        'ReadingDate' => 'nullable',
        'ReadBy' => 'nullable|string|max:50',
        'PowerReadings' => 'nullable|numeric',
        'DemandReadings' => 'nullable|numeric',
        'FieldFindings' => 'nullable|string|max:50',
        'MissCodes' => 'nullable|string|max:50',
        'Remarks' => 'nullable|string|max:255'
    ];

    
}
