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
        //where('is_active', true)
        //            ->
        $data = Bank::orderBy('id', 'desc')
            ->paginate(20);


        return view('financier.banks.index')->with([
            'data' => $data
        ]);
    }


    public function deactivate($id)
    {
        $banks = Bank::findOrFail($id);

        $banks->update([
            'is_active' => !$banks->is_active
        ]);

        return Redirect::back();
    }

    public function destroy(Request $request, $id)
    {
        $banks = Bank::findOrFail($id);

        $banks->delete();

        return Redirect::back();
    }
}
