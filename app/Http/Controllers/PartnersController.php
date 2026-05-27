<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PartnersController extends Controller
{
    public function index()
    {
        $partners = Partner::query()->latest()->get();

        return view('admin.partners', ['partners' => $partners]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:5120',
            'title' => 'nullable|string|max:120',
            'website' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
        ]);

        $fileName = basename($request->file('image')->store('public/images/partners'));

        Partner::create([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'website' => $this->normalizeWebsite($validated['website'] ?? null),
            'image' => $fileName,
            'slug' => $this->uniqueSlug($validated['title'] ?? null),
        ]);

        $this->clearPartnersCache();

        return redirect()->route('partnerCrud')->with('success', 'Partner added successfully.');
    }

    public function edit($id)
    {
        $partner = Partner::findOrFail($id);

        return view('admin.partnerUpdate', ['partner' => $partner]);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'image' => 'nullable|image|max:5120',
            'title' => 'nullable|string|max:120',
            'website' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('image')) {
            if ($partner->image) {
                Storage::delete('public/images/partners/'.$partner->image);
            }
            $partner->image = basename($request->file('image')->store('public/images/partners'));
        }

        $partner->title = $validated['title'] ?? null;
        $partner->description = $validated['description'] ?? null;
        $partner->website = $this->normalizeWebsite($validated['website'] ?? null);
        $partner->save();

        $this->clearPartnersCache();

        return redirect()->route('partnerCrud')->with('success', 'Partner updated successfully.');
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);

        if ($partner->image) {
            Storage::delete('public/images/partners/'.$partner->image);
        }

        $partner->delete();

        $this->clearPartnersCache();

        return redirect()->route('partnerCrud')->with('success', 'Partner removed successfully.');
    }

    private function normalizeWebsite(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }

    private function uniqueSlug(?string $title): string
    {
        $base = $title ? Str::slug($title) : 'partner';
        if ($base === '') {
            $base = 'partner';
        }

        $slug = $base;
        $suffix = 1;
        while (Partner::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function clearPartnersCache(): void
    {
        Cache::forget('front_layout.partners');
    }
}
