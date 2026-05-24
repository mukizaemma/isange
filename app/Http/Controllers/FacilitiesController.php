<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FacilityImage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class FacilitiesController extends Controller
{
    public function index()
    {
        $facilities = Facility::latest()->get();
        return view('admin.facilities',['facilities'=>$facilities]);
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $fileName = '';
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/facilities');
            $fileName = basename($path);
        }

        // Generate the slug
        $slug = Str::of($request->input('title'))->slug();

        // Check if a blog post with the same slug already exists
        $blog = Facility::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $request->input('title'),
                'category' => $request->input('category'),
                'description' => $request->input('description'),
                'image' => $fileName,
                'slug' => $slug
            ]
        );
        return redirect('getFacilities')->with('success', 'New Facility has been added successfuly');
    }


    public function edit($id)
    {
        $facility = Facility::find($id);
        return view('admin.facilityUpdate',['facility'=>$facility]);
    }

    public function update(Request $request, $id)
    {
        $post = Facility::findOrFail($id);

        // Update image if a new one is uploaded
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/facilities');
            $fileName = basename($path);

            // Delete the old image file
            Storage::delete('public/images/facilities/' . $post->image);

            $post->image = $fileName;
        }

        // Update other fields
        $post->title = $request->input('title');
        $post->description = $request->input('description');

        // Update the slug if the title has changed
        if($post->title !== $request->input('title')){
            $slug = Str::of($request->input('title'))->slug();
            // Check if a blog post with the same slug already exists
            $existingpost = Facility::where('slug', $slug)->first();
            if($existingpost && $existingpost->id !== $post->id){
                $suffix = 1;
                do{
                    $newSlug = $slug . '-' . $suffix++;
                    $existingpost = Facility::where('slug', $newSlug)->first();
                }while($existingpost);
                $slug = $newSlug;
            }
            $post->slug = $slug;
        }

        $post->save();

        return redirect('getFacilities')->with('success', 'L\'élément a été mis à jour avec succès');
    }


    public function destroy($id)
    {
        $post = Facility::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/facilities/' . $post->image);

        // Delete the post
        $post->delete();

        return redirect('getFacilities')->with('success', 'L\'actualite a été supprimé avec succès');


    }

    public function facilityImages($pid)
    {
        $facility = Facility::find($pid);
        $images = DB::table('facility_images')->where('facility_id', $pid)->get();
        return view('admin.images.facilityGallery', ['facility' => $facility, 'images' => $images]);
    }

    public function savFacImage(Request $request, $pid)
    {
        $data = new FacilityImage();
        $data->facility_id = $pid;
        // $data->caption = $request->caption;

        // Uploading image
        if ($request->hasFile('image')) {
            $dir = 'public/images/facilities';
            $path = $request->file('image')->store($dir);
            $fileName = str_replace($dir, '', $path);
            $data->image = $fileName;
        }

        $stored = $data->save();

        if($stored){
            return redirect()->back()->with('success', 'Image has been saved successfuly');
        }

        return redirect()->back()->with('error','Failed to add new Image');
    }

    public function destroyFacImage($id)
    {
        $image = FacilityImage::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/facilities/' . $image->image);

        // Delete the event
        $image->delete();

        return redirect()->back()->with('success', 'Image has been deleted');


    }
}
