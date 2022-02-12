<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Financier;
use App\Models\FinancierNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class NoteController extends Controller
{
    public function index(Request $request)
    {

        $financiers = Financier::all();

        return view('admin.notes.create')->with([
            'financiers' => $financiers,
        ]);
    }

    public function store(Request $request)
    {

        $idList = $request->get('id_list');
        $message = $request->get('message');

        FinancierNote::create([
            'user_id' => Auth::id(),
            'message' => $message,
            'id_list' => $idList
        ]);

        $this->setFlash('success', 'Not Basariyla Eklendi !');

        return Redirect::back();

    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}

