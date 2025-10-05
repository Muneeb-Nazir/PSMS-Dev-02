<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $categories = ['general', 'tax', 'email', 'alerts', 'backup', 'security'];
        $settings = [];
        
        foreach ($categories as $category) {
            $settings[$category] = Setting::where('category', $category)->get()->pluck('value', 'key');
        }
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $category = $request->input('category');
        $data = $request->except(['_token', '_method', 'category']);
        
        foreach ($data as $key => $value) {
            Setting::set($category, $key, $value);
        }
        
        return redirect()->route('settings.index')
                       ->with('success', ucfirst($category) . ' settings updated successfully!');
    }
}
