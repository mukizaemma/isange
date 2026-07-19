<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slide;

class SlidesController extends Controller
{
    public function index()
    {

        $slides = Slide::ordered()->get();

        return view('admin.slides', ['slides' => $slides]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'nullable|string|max:500',
            'subheading' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        $data = new Slide();
        $data->heading = $request->heading;
        $data->subheading = $request->subheading;
        if ($request->filled('sort_order')) {
            $data->sort_order = (int) $request->input('sort_order');
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/slides');
            $data->image = '/'.basename($path);
        }

        $stored = $data->save();

        if ($stored) {
            return redirect('slides')->with('success', 'New Image has been added successfuly');
        }

        return redirect()->back()->with('error', 'Failed to add new Image');
    }

    public function edit($id)
    {
        $data = Slide::find($id);
        return view('admin.slideUpdate', ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $data = Slide::find($id);

        if (! $data) {
            return back()->with('Error', 'Image Not Found');
        }

        $request->validate([
            'heading' => 'nullable|string|max:500',
            'subheading' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        $data->heading = $request->input('heading');
        $data->subheading = $request->input('subheading');
        if ($request->filled('sort_order')) {
            $data->sort_order = (int) $request->input('sort_order');
        }

        if ($request->hasFile('image')) {
            $oldPath = 'public/images/slides/'.ltrim((string) $data->image, '/');
            if ($data->image && Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }

            $path = $request->file('image')->store('public/images/slides');
            $data->image = '/'.basename($path);
        }

        $data->save();

        return redirect('slides')->with('success','Image has been updated');
    }

    public function destroy($id)
    {
        $slide = Slide::findOrFail($id);
        $filePath = 'public/images/slides/'.ltrim((string) $slide->image, '/');
        if ($slide->image && Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
        $slide->delete();

        return redirect()->back()->with('warning', 'Item has been deleted');
    }
}
