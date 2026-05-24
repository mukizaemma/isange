<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Partner;

class PartnersController extends Controller
{

    public function index()
    {
        $partners = partner::latest()->get();
        return view('admin.partners',['partners'=>$partners]);
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $fileName = '';
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/partners');
            $fileName = basename($path);
        }

        // Generate the slug
        $slug = Str::of($request->input('title'))->slug();

        // Check if a blog partner with the same slug already exists
        $blog = partner::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'website' => $request->input('website'),
                'image' => $fileName,
                'slug' => $slug
            ]
        );
        return redirect('partnerCrud')->with('success', 'Le nouvel élément a été ajouté avec succès');
    }


    public function edit($id)
    {
        $partner = partner::find($id);
        return view('admin.partnerUpdate',['partner'=>$partner]);
    }

    public function update(Request $request, $id)
    {
        $partner = partner::findOrFail($id);

        // Update image if a new one is uploaded
        if($request->hasFile('image')){
            $file = $request->file('image');

            $path = $file->store('public/images/partners');
            $fileName = basename($path);

            // Delete the old image file
            Storage::delete('public/images/partners/' . $partner->image);

            $partner->image = $fileName;
        }

        // Update other fields
        $partner->title = $request->input('title');
        $partner->description = $request->input('description');
        $partner->website = $request->input('website');

        // Update the slug if the title has changed
        if($partner->title !== $request->input('title')){
            $slug = Str::of($request->input('title'))->slug();
            // Check if a blog partner with the same slug already exists
            $existingpartner = partner::where('slug', $slug)->first();
            if($existingpartner && $existingpartner->id !== $partner->id){
                $suffix = 1;
                do{
                    $newSlug = $slug . '-' . $suffix++;
                    $existingpartner = partner::where('slug', $newSlug)->first();
                }while($existingpartner);
                $slug = $newSlug;
            }
            $partner->slug = $slug;
        }

        $partner->save();

        return redirect('partnerCrud')->with('success', 'L\'élément a été mis à jour avec succès');
    }


    public function destroy($id)
    {
        $partner = partner::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/partners/' . $partner->image);

        // Delete the partner
        $partner->delete();

        return redirect('partnerCrud')->with('success', 'L\'actualite a été supprimé avec succès');


    }
}
