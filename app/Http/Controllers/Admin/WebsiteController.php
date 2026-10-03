<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function edit(): View
    {
        return view('admin.website.edit', [
            'theme' => Setting::theme(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(Setting::THEMES)],
        ]);

        Setting::set('theme', $data['theme']);

        return back()->with('success', 'Website theme updated to '.ucfirst($data['theme']).'.');
    }
}
