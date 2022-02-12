<?php

namespace App\Http\Controllers\Financier;

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
    public function mark(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $financierNote = FinancierNote::find($id);

        if (!$financierNote) {
            $this->setFlash('error', 'Not bulunamadi !');
            return Redirect::back();
        }

        if ($financierNote->read_list) {
            $readList = array_merge($financierNote->read_list, [strval(Auth::id())]);
        } else {
            $readList = [strval(Auth::id())];
        }

        $financierNote->update([
            'read_list' => $readList
        ]);

        return Redirect::route('financier.home');
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}

