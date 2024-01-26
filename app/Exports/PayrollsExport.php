<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private mixed $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            '#',
            'Transaction ID',
            'Provider Name',
            'Amount',
            'Bank details'
        ];
    }
}
