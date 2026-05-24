<?php

namespace App\Http\Controllers;

use App\Models\HotelAmenityOption;
use App\Models\Room;
use App\Models\roomImage;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class RoomsController extends Controller
{
    public function roomType()
    {
        $rooms = RoomType::latest()->get();

        return view('admin.rooms', ['rooms' => $rooms]);
    }

    public function roomTypeCreate(Request $request)
    {

        $room = RoomType::firstOrCreate(
            [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]
        );

        return redirect('roomCrud')->with('success', 'New Room has been added successfuly');
    }

    public function amenityCreate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        HotelAmenityOption::firstOrCreate(
            ['label' => $request->input('name')],
            ['sort_order' => ((int) HotelAmenityOption::max('sort_order')) + 1]
        );

        return redirect()->route('getRooms')->with('success', 'Amenity option added.');
    }

    public function index()
    {
        $rooms = Room::with('amenityOptions')->get();
        $amenityOptions = HotelAmenityOption::orderBy('sort_order')->orderBy('label')->get();
        $categories = ['single', 'double', 'tween', 'apartment'];
        $accommodationTypes = [Room::TYPE_ROOM, Room::TYPE_APARTMENT];

        return view('admin.rooms', [
            'rooms' => $rooms,
            'amenityOptions' => $amenityOptions,
            'categories' => $categories,
            'accommodationTypes' => $accommodationTypes,
        ]);
    }

    public function store(Request $request)
    {

        $fileName = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = $file->store('public/images/rooms');
            $fileName = basename($path);
        }

        // Generate the slug
        $slug = Str::of($request->input('roomName'))->slug();

        // Check if a blog post with the same slug already exists
        $room = Room::firstOrCreate(
            ['slug' => $slug],
            [
                'roomName' => $request->input('roomName'),
                'category' => $request->filled('category') ? $request->input('category') : null,
                'accommodation_type' => $request->input('accommodation_type', Room::TYPE_ROOM),
                'price' => $request->input('price'),
                'price_rwf' => $request->filled('price_rwf') ? $request->input('price_rwf') : null,
                'size' => $request->input('size'),
                'maxAdults' => $request->input('maxAdults'),
                'maxChildren' => $request->input('maxChildren'),
                'description' => $request->input('description'),
                'image' => $fileName,
                'slug' => $slug,
            ]
        );

        $room->amenityOptions()->sync($request->input('amenity_options', []));

        return redirect('getRooms')->with('success', 'New Room has been added successfuly');
    }

    public function edit($id)
    {
        $room = Room::with('amenityOptions')->findOrFail($id);
        $amenityOptions = HotelAmenityOption::orderBy('sort_order')->orderBy('label')->get();

        return view('admin.roomUpdate', [
            'room' => $room,
            'amenityOptions' => $amenityOptions,
            'accommodationTypes' => [Room::TYPE_ROOM, Room::TYPE_APARTMENT],
            'categories' => ['single', 'double', 'tween', 'apartment'],
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        // Update image if a new one is uploaded
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = $file->store('public/images/rooms');
            $fileName = basename($path);

            // Delete the old image file
            Storage::delete('public/images/rooms/'.$room->image);

            $room->image = $fileName;
        }

        // Update other fields
        $room->roomName = $request->input('roomName');
        if ($request->filled('accommodation_type')) {
            $room->accommodation_type = $request->input('accommodation_type');
        }
        if ($request->filled('category')) {
            $room->category = $request->input('category');
        }
        $room->price = $request->input('price');
        $room->price_rwf = $request->filled('price_rwf') ? $request->input('price_rwf') : null;
        $room->size = $request->input('size');
        $room->maxAdults = $request->input('maxAdults');
        $room->maxChildren = $request->input('maxChildren');
        $room->description = $request->input('description');
        // $room->status = $request->input('status');

        // Update the slug if the title has changed
        if ($room->roomName !== $request->input('roomName')) {
            $slug = Str::of($request->input('roomName'))->slug();
            // Check if a blog post with the same slug already exists
            $existingpost = Room::where('slug', $slug)->first();
            if ($existingpost && $existingpost->id !== $room->id) {
                $suffix = 1;
                do {
                    $newSlug = $slug.'-'.$suffix++;
                    $existingpost = Room::where('slug', $newSlug)->first();
                } while ($existingpost);
                $slug = $newSlug;
            }
            $room->slug = $slug;
        }

        $room->save();

        $room->amenityOptions()->sync($request->input('amenity_options', []));

        return redirect('getRooms')->with('success', 'Room has been updated successfuly');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/rooms/'.$room->image);

        // Delete the post
        $room->delete();

        return redirect('getRooms')->with('success', 'Room has been deleted');

    }

    public function roomImages($pid)
    {
        $room = Room::find($pid);
        $images = DB::table('room_images')->where('room_id', $pid)->get();

        return view('admin.images.roomImages', ['room' => $room, 'images' => $images]);
    }

    public function savRoomImage(Request $request, $pid)
    {
        $data = new roomImage;
        $data->room_id = $pid;
        if ($request->hasFile('image')) {
            $dir = 'public/images/rooms';
            $path = $request->file('image')->store($dir);
            $fileName = str_replace($dir, '', $path);
            $data->image = $fileName;
        }

        $stored = $data->save();

        if ($stored) {
            return redirect()->back()->with('success', 'Image has been saved successfuly');
        }

        return redirect()->back()->with('error', 'Failed to add new Image');
    }

    public function destroyRoomImage($id)
    {
        $room = roomImage::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/rooms/'.$room->image);

        // Delete the post
        $room->delete();

        return redirect()->Back()->with('success', 'Image has been deleted');

    }

    public function roomTypeDelete($id)
    {
        $room = RoomType::findOrFail($id);

        // Delete the image file
        Storage::delete('public/images/rooms/'.$room->image);

        // Delete the post
        $room->delete();

        return redirect('roomType')->with('success', 'Room type has been deleted');

    }

    public function amenityDelete($id)
    {
        $amenity = HotelAmenityOption::findOrFail($id);
        $amenity->delete();

        return redirect()->route('getRooms')->with('success', 'Amenity option removed.');
    }
}
