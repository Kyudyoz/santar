<?php

namespace App\Livewire;

use App\Models\Surat;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class ValidasiTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $surats2 = Surat::where('rt_id', auth()->user()->rt_id)
        ->orderBy('status')
        ->simplePaginate(5);
        return view('livewire.validasi-table',[
            'surats2' => $surats2,
        ]);
    }
}
