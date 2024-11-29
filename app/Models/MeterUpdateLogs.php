<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterUpdateLogs extends Model
{
    public $table = 'MeterNumberUpdateLogs';

    protected $connection = 'sqlsrvbilling';

    public $fillable = [
        'id',
        'AccountNumber',
        'OldMeterNumber',
        'NewMeterNumber',
        'UserUpdated'
    ];

    protected $casts = [
        'id' => 'string',
        'AccountNumber' => 'string',
        'OldMeterNumber' => 'string',
        'NewMeterNumber' => 'string',
        'UserUpdated' => 'string'
    ];

    public static array $rules = [
        'AccountNumber' => 'nullable|string|max:50',
        'OldMeterNumber' => 'nullable|string|max:100',
        'NewMeterNumber' => 'nullable|string|max:150',
        'UserUpdated' => 'nullable|string|max:50',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    
}
