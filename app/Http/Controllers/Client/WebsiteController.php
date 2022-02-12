<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function index()
    {
        $websites = Website::where('client_id', Auth::user()->id)
            ->get();

        return view('client.websites.index')->with([
            'websites' => $websites
        ]);
    }

    public function create()
    {
        return view('client.websites.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|url',
        ]);
        if ($validator->fails()) {
            $this->setFlash('error', 'Gecerli bir domain adresi giriniz !');
            return Redirect::back();
        }

        Website::create([
            'client_id' => Auth::id(),
            'domain' => $request->get('domain'),
            'sid' => Str::random(12),
            'key' => Str::random(12),
        ]);

        $this->setFlash('success', 'Site Basariyla Olusturuldu !');
        return Redirect::route('client.websites.index');

    }

    public function edit(Request $request, $id)
    {
        $website = Website::find($id);

        if (!$website || $website->client_id !== Auth::id()) {
            $this->setFlash('error', 'Site Bulunamadi !');
            return Redirect::back();
        }

        return view('client.websites.edit')->with([
            'website' => $website
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|url',
        ]);
        if ($validator->fails()) {
            $this->setFlash('error', 'Gecerli bir domain adresi giriniz !');
            return Redirect::back();
        }


        $website = Website::find($id);
        if (!$website || $website->client_id !== Auth::id()) {
            $this->setFlash('error', 'Site Bulunamadi !');
            return Redirect::back();
        }

        $website->update([
            'domain' => $request->get('domain'),
        ]);

        return Redirect::route('client.websites.index');
    }

    public function destroy(Request $request, $id)
    {
        $website = Website::find($id);
        if (!$website || $website->client_id !== Auth::id()) {
            $this->setFlash('error', 'Site Bulunamadi !');
            return Redirect::back();
        }

        $website->delete();

        $this->setFlash('success', 'Site Basariyla Silindi !');

        return Redirect::route('client.websites.index');

    }


    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}
