<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Financier;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProfileController extends Controller
{


    public function profile()
    {
        return view('admin.profile.index');
    }

    public function profileUpdate(Request $request)
    {
        $checkUsername = User::where('username', $request->get('username'))
            ->where('id', '!=', Auth::id())
            ->first();

        if ($checkUsername) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz');
            return Redirect::back();
        }

        $user = Auth::user();

        $user->update([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => $request->get('password') ? Hash::make($request->get('password')) : $user->password
        ]);
        $this->setFlash('success', 'Profil Guncellendi');
        return Redirect::back();
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }


}
