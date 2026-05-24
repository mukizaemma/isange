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
        $accommodationTypes = [Room::TYPE_ROOM, Room::TYPE_APARTMENT];

        return view('admin.rooms', [
            'rooms' => $rooms,
            'amenityOptions' => $amenityOptions,
            'accommodationTypes' => $accommodationTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'roomName' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:64',
            'price_rwf' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:64',
            'maxAdults' => 'nullable|string|max:32',
            'maxChildren' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'accommodation_type' => 'nullable|in:room,apartment',
        ]);

        $path = $request->file('image')->store('public/images/rooms');
        $fileName = basename($path);

        $roomName = trim((string) $request->input('roomName', ''));
        $slug = $this->uniqueRoomSlug($roomName !== '' ? Str::slug($roomName) : 'room');

        $room = Room::create([
            'slug' => $slug,
            'roomName' => $roomName !== '' ? $roomName : 'Untitled room',
            'accommodation_type' => $request->input('accommodation_type', Room::TYPE_ROOM),
            'price' => $request->input('price'),
            'price_rwf' => $request->filled('price_rwf') ? $request->input('price_rwf') : null,
            'size' => $request->input('size'),
            'maxAdults' => $request->input('maxAdults'),
            'maxChildren' => $request->input('maxChildren'),
            'description' => $request->input('description'),
            'image' => $fileName,
        ]);

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
        ]);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $imageRule = empty($room->image) ? 'required|image|max:10240' : 'nullable|image|max:10240';

        $request->validate([
            'image' => $imageRule,
            'roomName' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:64',
            'price_rwf' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:64',
            'maxAdults' => 'nullable|string|max:32',
            'maxChildren' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'accommodation_type' => 'nullable|in:room,apartment',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/rooms');
            $fileName = basename($path);

            if (! empty($room->image)) {
                Storage::delete('public/images/rooms/'.$room->image);
            }

            $room->image = $fileName;
        }

        $roomName = trim((string) $request->input('roomName', ''));
        if ($roomName !== '') {
            if ($roomName !== $room->roomName) {
                $room->slug = $this->uniqueRoomSlug(Str::slug($roomName), $room->id);
            }
            $room->roomName = $roomName;
        }

        if ($request->filled('accommodation_type')) {
            $room->accommodation_type = $request->input('accommodation_type');
        }
        $room->price = $request->input('price');
        $room->price_rwf = $request->filled('price_rwf') ? $request->input('price_rwf') : null;
        $room->size = $request->input('size');
        $room->maxAdults = $request->input('maxAdults');
        $room->maxChildren = $request->input('maxChildren');
        $room->description = $request->input('description');

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

    private function uniqueRoomSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'room';
        $candidate = $slug;
        $suffix = 1;

        while (Room::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = $slug.'-'.$suffix++;
        }

        return $candidate;
    }
}
