<?php

namespace App\Http\Controllers\Financier\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    private $guard = 'financier';

    protected $redirectTo = '/financier/home';

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


        if (Auth::guard($this->guard)->attempt(['username' => $request->get('username'), 'password' => $request->get('password')])) {

            $request->session()->regenerate();

            return redirect()->route('financier.home');
        } else {
            $this->setFlash('error', 'Bilgilerinizi Kontrol Ediniz !');
            return redirect()->route('financier.login');
        }

    }

    public function loginUI(Request $request)
    {
        if (Auth::user()) {
            dd('here financier');
            return Redirect::route('home');
        }

        return view('financier.auth.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('financier')->logout();
        Session::flush();
        return Redirect::route('financier.loginUI');
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }
}

