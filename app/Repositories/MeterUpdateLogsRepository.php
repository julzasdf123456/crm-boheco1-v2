<?php

namespace App\Repositories;

use App\Models\MeterUpdateLogs;
use App\Repositories\BaseRepository;

class MeterUpdateLogsRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'AccountNumber',
        'OldMeterNumber',
        'NewMeterNumber',
        'UserUpdated'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return MeterUpdateLogs::class;
    }
}
