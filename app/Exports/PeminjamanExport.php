<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PeminjamanExport implements FromView
{
    protected $peminjaman;

    public function __construct($peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function view(): View
    {
        return view('exports.histori-peminjaman-excel', [
            'peminjaman' => $this->peminjaman
        ]);
    }
}
