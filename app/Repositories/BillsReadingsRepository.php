<?php

namespace App\Repositories;

use App\Models\BillsReadings;
use App\Repositories\BaseRepository;

class BillsReadingsRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'AccountNumber',
        'ReadingDate',
        'ReadBy',
        'PowerReadings',
        'DemandReadings',
        'FieldFindings',
        'MissCodes',
        'Remarks'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return BillsReadings::class;
    }
}
