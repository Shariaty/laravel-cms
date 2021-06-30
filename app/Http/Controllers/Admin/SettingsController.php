<?php

namespace App\Http\Controllers\Admin;

use App\Facade\Facades\OilSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SettingsController extends Controller
{
    public function settingsView()
    {
        $siteSettings = OilSettings::get(['site', 'social', 'map', 'credit']);
        return view('admin.settings' , compact('siteSettings'))->with('title' , 'Site Settings');
    }

    public function settingsUpdate(Request $request)
    {
        dd(1);
        $validator = Validator::make($request->all() , [
            'title' => 'required|min:2',
            'description' => 'max:200',
            'dirhamRate' => 'nullable|numeric',
            'email' => 'required|email',
            'phone' => 'nullable|min:6',
            'mobile' => 'nullable|min:6|numeric',
            'lat' => 'min:3',
            'lng' => 'min:3',
            'profile_telegram' => 'max:200',
            'profile_facebook' => 'max:200',
            'profile_twitter' => 'max:200',
            'profile_linkedin' => 'max:200',
            'profile_instagram' => 'max:200',
            'profile_google_plus' => 'max:200'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('_token');

        foreach ($data as $key => $value ){
            DB::table('settings')->where('key' , $key)->update(['value' => $value]);
        }

        $request->session()->flash('success', 'Site settings has been updated');
        return redirect(route('admin.site.settings'));
    }

}
