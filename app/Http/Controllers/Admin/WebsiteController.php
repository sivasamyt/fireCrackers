<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function edit(): View
    {
        return view('admin.website.edit', [
            'theme' => Setting::theme(),
            'logoUrl' => Setting::logoUrl(),
            'heroImageUrl' => Setting::heroImageUrl(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(Setting::THEMES)],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'hero_image' => ['nullable', 'image', 'max:5120'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ]);

        Setting::set('theme', $data['theme']);

        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            $disk = Storage::disk(config('filesystems.media'));
            $oldPath = Setting::get('logo_path');

            if ($oldPath) {
                $disk->delete($oldPath);
            }

            Setting::set('logo_path', $request->hasFile('logo')
                ? $request->file('logo')->store('branding', config('filesystems.media'))
                : null);
        }

        if ($request->hasFile('hero_image') || $request->boolean('remove_hero_image')) {
            $disk = Storage::disk(config('filesystems.media'));
            $oldPath = Setting::get('hero_image_path');

            if ($oldPath) {
                $disk->delete($oldPath);
            }

            Setting::set('hero_image_path', $request->hasFile('hero_image')
                ? $request->file('hero_image')->store('branding', config('filesystems.media'))
                : null);
        }

        return back()->with('success', 'Website settings updated.');
    }
}
