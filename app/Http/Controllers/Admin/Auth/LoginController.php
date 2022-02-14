<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
//    use AuthenticatesUsers;

    protected $redirectTo = '/admin/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);



        if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')])) {
            $request->session()->regenerate();
            return redirect()->route('admin.home');
        } else {
            $this->setFlash('error', 'Bilgilerinizi Kontrol Ediniz !');
            return redirect()->route('admin.login');
        }

    }

    public function loginUI(Request $request)
    {
        if (Auth::user()) {
            dd('here');
            return Redirect::route('home');
        }

        return view('admin.auth.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }
}

