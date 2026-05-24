<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Models\ServiceImage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();
        return view('admin.services',['services'=>$services]);
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $fileName = '';
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/services');
            $fileName = basename($path);
        }

        // Generate the slug
        $slug = Str::of($request->input('title'))->slug();

        // Check if a blog post with the same slug already exists
        $blog = Service::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $fileName,
                'slug' => $slug
            ]
        );
        return redirect('getServices')->with('success', 'New Facility has been added successfuly');
    }


    public function edit($id)
    {
        $service = Service::find($id);
        return view('admin.serviceUpdate',['service'=>$service]);
    }

    public function update(Request $request, $id)
    {
        $post = Service::findOrFail($id);

        // Update image if a new one is uploaded
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/services');
            $fileName = basename($path);

            // Delete the old image file
            Storage::delete('public/images/services/' . $post->image);

            $post->image = $fileName;
        }

        // Update other fields
        $post->title = $request->input('title');
        $post->description = $request->input('description');

        // Update the slug if the title has changed
        if($post->title !== $request->input('title')){
            $slug = Str::of($request->input('title'))->slug();
            // Check if a blog post with the same slug already exists
            $existingpost = Service::where('slug', $slug)->first();
            if($existingpost && $existingpost->id !== $post->id){
                $suffix = 1;
                do{
                    $newSlug = $slug . '-' . $suffix++;
                    $existingpost = Service::where('slug', $newSlug)->first();
                }while($existingpost);
                $slug = $newSlug;
            }
            $post->slug = $slug;
        }

        $post->save();

        return redirect('getServices')->with('success', 'L\'élément a été mis à jour avec succès');
    }


    public function destroy($id)
    {
        $post = Service::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/services/' . $post->image);

        // Delete the post
        $post->delete();

        return redirect('getServices')->with('success', 'Data deleted successfully');


    }

    public function serviceImages($pid)
    {
        $service = Service::find($pid);
        $images = DB::table('service_images')->where('service_id', $pid)->get();
        return view('admin.images.serviceGallery', ['service' => $service, 'images' => $images]);
    }

    public function savServiceImage(Request $request, $pid)
    {
        $data = new ServiceImage();
        $data->service_id = $pid;
        // $data->caption = $request->caption;

        // Uploading image
        if ($request->hasFile('image')) {
            $dir = 'public/images/services';
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

    public function destroyServiceImage($id)
    {
        $image = ServiceImage::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/services/' . $image->image);

        // Delete the event
        $image->delete();

        return redirect()->back()->with('success', 'Image has been deleted');


    }

}
