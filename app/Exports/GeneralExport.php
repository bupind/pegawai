<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GeneralExport implements FromCollection, WithHeadings
{
    protected $query;
    protected $columns;
    protected $headers;

    public function __construct($query, $columns, $headers = [])
    {
        $this->query   = $query;
        $this->columns = $columns;
        $this->headers = $headers;
    }

    public function collection()
    {
        return collect($this->query);
    }

    public function headings(): array
    {
        return $this->headers ?: array_map(fn($col) => ucfirst(str_replace('.', ' ', $col)), $this->columns);
    }
}
