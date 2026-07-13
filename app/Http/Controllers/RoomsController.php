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
        $validated = $request->validate(
            $this->roomRules($request),
            $this->roomMessages()
        );

        $path = $request->file('image')->store('public/images/rooms');
        $fileName = basename($path);

        $roomName = trim($validated['roomName']);
        $slug = $this->uniqueRoomSlug(Str::slug($roomName));

        $room = Room::create([
            'slug' => $slug,
            'roomName' => $roomName,
            'accommodation_type' => $validated['accommodation_type'],
            'price' => $validated['price'] ?? null,
            'price_rwf' => $validated['price_rwf'] ?? null,
            'discount_enabled' => $request->boolean('discount_enabled'),
            'discount_type' => $validated['discount_type'] ?? null,
            'discount_value' => $validated['discount_value'] ?? null,
            'size' => $validated['size'] ?? null,
            'maxAdults' => $validated['maxAdults'] ?? null,
            'maxChildren' => $validated['maxChildren'] ?? null,
            'description' => $validated['description'] ?? '',
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

        $validated = $request->validate(
            $this->roomRules($request, $room),
            $this->roomMessages()
        );

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/rooms');
            $fileName = basename($path);

            if (! empty($room->image)) {
                Storage::delete('public/images/rooms/'.$room->image);
            }

            $room->image = $fileName;
        }

        $roomName = trim($validated['roomName']);
        if ($roomName !== $room->roomName) {
            $room->slug = $this->uniqueRoomSlug(Str::slug($roomName), $room->id);
        }
        $room->roomName = $roomName;
        $room->accommodation_type = $validated['accommodation_type'];
        $room->price = $validated['price'] ?? null;
        $room->price_rwf = $validated['price_rwf'] ?? null;
        $room->discount_enabled = $request->boolean('discount_enabled');
        $room->discount_type = $validated['discount_type'] ?? null;
        $room->discount_value = $validated['discount_value'] ?? null;
        $room->size = $validated['size'] ?? null;
        $room->maxAdults = $validated['maxAdults'] ?? null;
        $room->maxChildren = $validated['maxChildren'] ?? null;
        $room->description = $validated['description'] ?? '';

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
        Room::findOrFail($pid);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ], [
            'image.required' => 'Please select a gallery image to upload.',
            'image.image' => 'The gallery file must be an image.',
            'image.max' => 'The gallery image may not be larger than 10 MB.',
        ]);

        $path = $request->file('image')->store('public/images/rooms');
        $fileName = basename($path);

        $data = new roomImage;
        $data->room_id = $pid;
        $data->image = $fileName;
        $data->save();

        return redirect()->back()->with('success', 'Image has been saved successfuly');
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

    /**
     * @return array<string, mixed>
     */
    private function roomRules(Request $request, ?Room $room = null): array
    {
        $needsImage = $room === null || empty($room->image);

        $discountOn = $request->boolean('discount_enabled');

        return [
            'image' => ($needsImage ? 'required' : 'nullable').'|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'roomName' => 'required|string|max:255',
            'accommodation_type' => 'required|in:'.Room::TYPE_ROOM.','.Room::TYPE_APARTMENT,
            'price' => 'nullable|string|max:64',
            'price_rwf' => 'nullable|numeric|min:0',
            'discount_enabled' => 'sometimes|boolean',
            'discount_type' => [$discountOn ? 'required' : 'nullable', 'in:'.Room::DISCOUNT_PERCENT.','.Room::DISCOUNT_FIXED],
            'discount_value' => [
                $discountOn ? 'required' : 'nullable',
                'numeric',
                'min:0.01',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if (! $request->boolean('discount_enabled')) {
                        return;
                    }
                    $type = $request->input('discount_type');
                    $amount = (float) $value;
                    if ($type === Room::DISCOUNT_PERCENT && $amount > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                    if ($type === Room::DISCOUNT_FIXED) {
                        $price = (float) $request->input('price', 0);
                        if ($price <= 0) {
                            $fail('Set a USD room price before applying a fixed discount.');
                        } elseif ($amount >= $price) {
                            $fail('Fixed discount must be less than the USD room price.');
                        }
                    }
                },
            ],
            'size' => 'nullable|string|max:64',
            'maxAdults' => 'nullable|string|max:32',
            'maxChildren' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'amenity_options' => 'nullable|array',
            'amenity_options.*' => 'integer|exists:hotel_amenity_options,id',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function roomMessages(): array
    {
        return [
            'image.required' => 'A cover image is required.',
            'image.image' => 'The cover file must be an image (JPEG, PNG, GIF, or WebP).',
            'image.mimes' => 'The cover image must be JPEG, PNG, GIF, or WebP.',
            'image.max' => 'The cover image may not be larger than 10 MB.',
            'roomName.required' => 'Please enter a room name.',
            'accommodation_type.required' => 'Please choose a listing type (room or apartment).',
            'accommodation_type.in' => 'Listing type must be room or apartment.',
        ];
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
