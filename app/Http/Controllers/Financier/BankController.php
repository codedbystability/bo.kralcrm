<?php

namespace App\Http\Controllers\Financier;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BankController extends Controller
{
    public function index()
    {
        $data = Bank::where('is_active', true)
            ->orderBy('id', 'desc')
            ->paginate(20);


        return view('financier.banks.index')->with([
            'data' => $data
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $banks = Bank::findOrFail($id);

        $banks->update([
            'is_active' => !$banks->is_active
        ]);

        return Redirect::back();
    }
}
