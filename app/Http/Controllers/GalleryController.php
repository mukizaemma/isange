<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {

    
        $types = Image::select('type')->distinct()->get();
        $images = Image::latest()->get();
        return view('admin.gallery', [
            'images'=>$images,
            'types'=>$types,
            ]);
    }


    public function store(Request $request)
    {
        $data = new Image();
        $data->category = $request->category;
        $data->type = $request->type;
        $data->caption = $request->input('caption');

        // Uploading image
        if ($request->hasFile('image')) {
            $dir = 'public/images/gallery';
            $path = $request->file('image')->store($dir);
            $fileName = str_replace($dir, '', $path);
            $data->image = $fileName;
        }

        $stored = $data->save();

        if($stored){
            return redirect('getGalleries')->with('success', 'New Image has been added successfuly');
        }

        return redirect()->back()->with('error','Failed to add new Image');
    }

    public function edit($id)
    {
        $data = Image::find($id);
        return view('admin.galleryUpdate', ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $data = Image::find($id);
        $data->category = $request->input('category');
        // $data->subheading = $request->input('subheading');

        if(!$data){
            return back()->with('Error','Image Not Found');
        }

        if ($request->hasFile('image') && request('image') != '') {
            $dir = 'public/images/gallery';

            if (File::exists($dir)) {
                unlink($dir);
            }
            $path = $request->file('image')->store($dir);
            $fileName = str_replace($dir, '', $path);

            $data->image = $fileName;
        }

        $data->update();

        return redirect('getGalleries')->with('success','Image has been updated');
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        // delete the image file
        Storage::delete('public/images/gallery/'.$image);
        $image->delete();
        return redirect()->back()->with('warning', 'Item has been deleted');
    }
}
