<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Booking;
use App\Models\DiningGalleryImage;
use App\Models\DiningMenuItem;
use App\Models\Facility;
use App\Models\Image;
use App\Models\MenuCategory;
use App\Models\Message;
use App\Models\Room;
use App\Models\Service;
use App\Models\Slide;
use App\Support\DiningMenuPresenter;
use App\Support\FrontendPageCache;
use App\Support\SpamProtection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    use Concerns\RendersSpaFragment;

    public function index()
    {
        $data = Cache::remember(FrontendPageCache::HOME_PAGE_DATA, 120, function () {
            $slides = Slide::query()->ordered()->get();
            $rooms = Room::query()
                ->select(['id', 'roomName', 'slug', 'price', 'price_rwf', 'image'])
                ->oldest()
                ->get();

            $latestBlogs = Blog::query()
                ->published()
                ->withCount('comments')
                ->latest('published_at')
                ->latest('id')
                ->take(3)
                ->get();

            return [
                'slides' => $slides,
                'rooms' => $rooms,
                'homeTodaysMenu' => DiningMenuPresenter::todaysMenuItems(12),
                'latestBlogs' => $latestBlogs,
            ];
        });

        return $this->spaView('frontend.index', $data, 'Home');
    }

    public function contact()
    {
        return $this->spaView('frontend.contact', [], 'Contact');
    }

    public function terms()
    {
        return $this->spaView('frontend.terms', [], 'Terms');
    }

    public function aboutUs()
    {
        return $this->spaView('frontend.about', [], 'About');
    }

    public function experiences()
    {
        return $this->spaView('frontend.experiences', [], 'Experiences');
    }

    public function future4kids()
    {
        return $this->spaView('frontend.future4kids', [], 'Future 4 Kids');
    }

    public function facilities()
    {
        $facilities = Facility::orderBy('created_at', 'asc')->get();
        $gallery = Image::latest()->get();
        $diningGallery = DiningGalleryImage::orderBy('sort_order')->orderBy('id')->get();

        return $this->spaView('frontend.facilities', [
            'facilities' => $facilities,
            'gallery' => $gallery,
            'diningGallery' => $diningGallery,
        ], 'Facilities');
    }

    public function dining()
    {
        $diningMenuColumns = Cache::remember(FrontendPageCache::DINING_MENU_COLUMNS, 180, function () {
            return DiningMenuPresenter::fullMenuColumns();
        });
        $todaysMenu = DiningMenuPresenter::todaysMenuItems();

        return $this->spaView('frontend.dining', compact(
            'diningMenuColumns',
            'todaysMenu',
        ), 'Bar & Restaurant');
    }

    public function facilitySingle($slug)
    {
        $facility = Facility::where('slug', $slug)->firstOrFail();
        $images = DB::table('facility_images')->where('facility_id', $facility->id)->latest()->get();
        $reletedFacilities = Facility::where('id', '!=', $facility->id)
            ->orderBy('created_at')
            ->take(12)
            ->get();

        return $this->spaView('frontend.facilitySingle', [
            'facility' => $facility,
            'images' => $images,
            'reletedFacilities' => $reletedFacilities,
        ], $facility->title ?? 'Facility');
    }

    public function services()
    {
        $services = Service::oldest()->get();
        $gallery = Image::latest()->take(12)->get();

        return $this->spaView('frontend.services', [
            'services' => $services,
            'gallery' => $gallery,
        ], 'Services');
    }

    public function singleService($slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $images = DB::table('service_images')->where('service_id', $service->id)->latest()->get();
        $gallery = Image::latest()->take(12)->get();

        return $this->spaView('frontend.serviceSingle', [
            'service' => $service,
            'images' => $images,
            'gallery' => $gallery,
        ], $service->title ?? 'Service');
    }

    public function gallery()
    {
        $categories = Image::select('category')->distinct()->pluck('category');
        $images = Image::latest()->get();

        return $this->spaView('frontend.gallery', [
            'images' => $images,
            'categories' => $categories,
        ], 'Gallery');
    }

    public function blogs()
    {
        $blogs = Blog::query()
            ->published()
            ->withCount('comments')
            ->latest('published_at')
            ->latest('id')
            ->paginate(9);

        return $this->spaView('frontend.blogs', [
            'blogs' => $blogs,
        ], 'Updates');
    }

    public function blog(string $slug)
    {
        $post = Blog::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->recordPageView();

        $post->load(['comments']);

        $related = Blog::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return $this->spaView('frontend.blog', [
            'post' => $post,
            'related' => $related,
        ], $post->title ?? 'Update');
    }

    public function reservationPage()
    {
        // $reservation = Reservepolocy::all();
        $rooms = Room::all();

        return $this->spaView('frontend.reservation', [
            // 'reservation' => $reservation,
            'rooms' => $rooms,
        ], 'Reservation');
    }

    public function rooms(Request $request)
    {
        $allRooms = Room::with('images')->oldest()->get();
        $activeTab = $request->query('tab', 'rooms');
        if (! in_array($activeTab, ['rooms', 'apartments'], true)) {
            $activeTab = 'rooms';
        }

        $pageTitle = $activeTab === 'apartments' ? 'Apartments' : 'Rooms';

        return $this->spaView('frontend.rooms', [
            'rooms' => $allRooms,
            'activeTab' => $activeTab,
        ], $pageTitle);
    }

    public function singleRoom($slug)
    {
        $room = Room::with('amenityOptions')->where('slug', $slug)->firstOrFail();
        $images = DB::table('room_images')->where('room_id', $room->id)->paginate(3);

        return $this->spaView('frontend.roomSingle', [
            'images' => $images,
            'room' => $room,
        ], $room->roomName ?? 'Room');
    }

    public function SendMessage(Request $request)
    {
        SpamProtection::validateRequest($request);

        $comment = Message::create([
            'names' => $request->input('names'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
        ]);

        if ($comment) {
            // Mail::to('mukizaemma34@gmail.com')->send(new BlogCommentsNotofications($comment));
            return redirect()->back()->with('success', 'Your Message is successfully submitted. We will get back to you soon!');
        } else {
            return redirect()->back()->with('error', 'Failed to add the comment. Please try again.');
        }
    }

    public function reserveNow(Request $request)
    {
        SpamProtection::validateRequest($request);

        $today = now()->toDateString();
        $bookingCount = Booking::where('email', $request->email)
            ->whereDate('created_at', $today)
            ->count();

        if ($bookingCount >= 2) {
            return redirect()->back()->with('error', 'You cannot make more than 2 bookings in a day with the same email.');
        }

        $nights = now()->parse($request->checkout)->diffInDays($request->checkin);

        $room = Room::find($request->room_id);
        $price = $room ? $room->price : 0;

        $total = $nights * $price;

        $booking = Booking::create([
            'names' => $request->names,
            'phone' => $request->phone,
            'email' => $request->email,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'adults' => $request->adults,
            'rooms' => $request->rooms,
            'children' => $request->input('children', 0),
            'nights' => $nights,
            'total' => $total,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'Pending',
            'room_id' => $request->room_id,
        ]);
        if ($booking) {
            return redirect()->back()->with('success', 'Booking successfully created. Thank you!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later');
        }

    }

    public function checkAvailability(Request $request)
    {
        $itemType = $request->input('item');
        $checkinDate = date('Y-m-d', strtotime($request->input('checkin')));
        $checkoutDate = date('Y-m-d', strtotime($request->input('checkout')));

        if ($itemType == 'Room') {
            $rooms = Room::with('roomBookings')->get();

            $availableRooms = collect(); // Initialize an empty collection

            $startDate = strtotime($checkinDate);
            $endDate = strtotime($checkoutDate);

            // Loop through each date within the selected date range
            while ($startDate <= $endDate) {
                $currentDate = date('Y-m-d', $startDate);

                // Check availability for each room on the current date
                foreach ($rooms as $room) {
                    $roomAvailability = $room->quantity;

                    // Get the bookings for the current date
                    $bookings = $room->roomBookings()
                        ->whereDate('checkin', '<=', $currentDate)
                        ->whereDate('checkout', '>=', $currentDate)
                        ->get();

                    // Subtract the booked quantity for the current date
                    foreach ($bookings as $booking) {
                        $roomAvailability -= $booking->room_quantity;
                    }

                    // If the room is available, add it to the available rooms collection
                    if ($roomAvailability > 0) {
                        $availableRooms->push([
                            'date' => $currentDate,
                            'room' => $room,
                        ]);
                    }
                }

                // Move to the next date
                $startDate = strtotime('+1 day', $startDate);
            }

            return $this->spaView('frontend.availableRooms', compact('availableRooms', 'checkinDate', 'checkoutDate'), 'Availability');
        } elseif ($itemType == 'Table') {
            // Call function to check available rooms
            $availableTables = DB::table('tables')
                ->where('is_available', true)
                ->whereNotIn('id', function ($query) use ($checkinDate, $checkoutDate) {
                    $query->select('table_id')
                        ->from('tablebookings')
                        ->where(function ($query) use ($checkinDate, $checkoutDate) {
                            $query->where('checkin', '<', $checkoutDate)
                                ->where('checkout', '>', $checkinDate);
                        })
                        ->orWhere(function ($query) use ($checkinDate, $checkoutDate) {
                            $query->where('checkin', '=', $checkinDate)
                                ->where('checkout', '=', $checkoutDate);
                        })
                        ->pluck('table_id');
                })
                ->get();

            return $this->spaView('frontend.availableTables', compact('availableTables', 'checkinDate', 'checkoutDate'), 'Availability');
        }
    }

    public function confirmReservation(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        // Check if there are available rooms
        if ($room->quantity > 0) {
            $nights = now()->parse($request->checkout)->diffInDays($request->checkin);

            Booking::create([
                'room_id' => $room->id,
                'names' => $request->names,
                'email' => $request->email,
                'phone' => $request->phone,
                'checkin' => $request->checkin,
                'checkout' => $request->checkout,
                'adults' => $request->adults,
                'children' => $request->input('children', 0),
                'rooms' => $request->input('room', 1),
                'nights' => $nights,
                'total' => $request->input('price', $room->price) * max(1, $nights),
                'description' => $request->description,
                'status' => 'Pending',
            ]);

            return redirect()->back()->with('success', 'Thank you for booking with us. Remember we can pick you from Kigali at only $10');
        } else {
            // No available rooms
            return redirect()->back()->with('error', 'Sorry, the selected room is no longer available.');
        }
    }

    public function confirmTableReservation(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $booking = new Tablebooking;
        $booking->table_id = $table->id;
        $booking->tableName = $table->name;
        $booking->names = $request->names;
        // $booking->email = $request->email;
        $booking->phone = $request->phone;
        $booking->checkin = $request->checkin;
        $booking->checkout = $request->checkout;
        $booking->people = $request->people;
        $booking->description = $request->description;
        $booking->save();

        // Update room availability based on booked dates
        $startDate = date('Y-m-d H:i:s', strtotime($request->checkin));
        $endDate = date('Y-m-d H:i:s', strtotime($request->checkout));

        DB::table('tables')
            ->where('id', $id)
            ->whereBetween('dateAvailable', [$startDate, $endDate])
            ->update(['is_available' => false]);

        return redirect()->back()->with('success', 'Thank you for booking with us. Remember we can pick you from Kigali at only $10');
    }

    public function restaurant()
    {

        $gallery = Facility::where('category', 'Restaurant')->get();

        return $this->spaView('frontend.restaurant', ['gallery' => $gallery], 'Restaurant');
    }

    /**
     * @return array<int, array{label: string, items: array<int, mixed>}>
     */
    private function buildHomeDiningTwoColumns(): array
    {
        $menuCategoriesForHome = MenuCategory::with(['items' => function ($q) {
            $q->orderBy('sort_order')->orderBy('title');
        }])->orderBy('sort_order')->orderBy('name')->get();

        $mapHomeDiningItem = static function (DiningMenuItem $i): array {
            $rawDesc = $i->description ? strip_tags($i->description) : '';
            $rawDesc = html_entity_decode($rawDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $rawDesc = trim(preg_replace('/\s+/u', ' ', $rawDesc));
            $short = Str::limit($rawDesc, 120);

            return [
                'title' => $i->title,
                'description' => $short,
                'descriptionTitle' => $rawDesc,
                'priceHtml' => Currency::formatUsdWithLocal($i->price_usd, $i->price_rwf),
            ];
        };

        $withItems = $menuCategoriesForHome->filter(fn ($c) => $c->items->isNotEmpty())->values();

        $foodCat = $withItems->first(function (MenuCategory $c) {
            $n = mb_strtolower(trim($c->name));

            return $n === 'food' || $n === 'foods' || str_contains($n, 'food');
        });
        $drinksCat = $withItems->first(function (MenuCategory $c) use ($foodCat) {
            if ($foodCat && $c->id === $foodCat->id) {
                return false;
            }
            $n = mb_strtolower(trim($c->name));

            return (bool) preg_match('/beverage|drink|bar|wine|liquor|juice|soda|tea|coffee|beer|cocktail/', $n);
        });

        $homeDiningTwoColumns = [];
        if ($foodCat) {
            $homeDiningTwoColumns[] = [
                'label' => $foodCat->name,
                'items' => $foodCat->items->map($mapHomeDiningItem)->values()->all(),
            ];
        }
        if ($drinksCat) {
            $homeDiningTwoColumns[] = [
                'label' => $drinksCat->name,
                'items' => $drinksCat->items->map($mapHomeDiningItem)->values()->all(),
            ];
        }

        $usedIds = collect([$foodCat?->id, $drinksCat?->id])->filter();

        foreach ($withItems as $c) {
            if (count($homeDiningTwoColumns) >= 2) {
                break;
            }
            if ($usedIds->contains($c->id)) {
                continue;
            }
            if ($c->items->isEmpty()) {
                continue;
            }
            $homeDiningTwoColumns[] = [
                'label' => $c->name,
                'items' => $c->items->map($mapHomeDiningItem)->values()->all(),
            ];
            $usedIds->push($c->id);
        }

        return $homeDiningTwoColumns;
    }

    /**
     * @return array<int, array{label: string, items: array<int, mixed>}>
     */
    private function buildDiningMenuColumns(): array
    {
        $allMenuItems = DiningMenuItem::query()
            ->with('category')
            ->leftJoin('menu_categories as mc', 'dining_menu_items.menu_category_id', '=', 'mc.id')
            ->orderByRaw('COALESCE(mc.sort_order, 999999)')
            ->orderBy('mc.name')
            ->orderBy('dining_menu_items.sort_order')
            ->orderBy('dining_menu_items.title')
            ->select('dining_menu_items.*')
            ->get();

        $menuCategoriesForDining = MenuCategory::orderBy('sort_order')->orderBy('name')->get();

        $serializeDiningItem = static function (DiningMenuItem $i): array {
            $rawDesc = $i->description ? strip_tags($i->description) : '';
            $rawDesc = html_entity_decode($rawDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $rawDesc = trim(preg_replace('/\s+/u', ' ', $rawDesc));
            $short = Str::limit($rawDesc, 160);

            $rwfAttr = $i->price_rwf && (float) $i->price_rwf > 0
                ? (string) (int) round((float) $i->price_rwf)
                : '';

            return [
                'id' => $i->id,
                'title' => $i->title,
                'description' => $short,
                'descriptionTitle' => $rawDesc,
                'priceHtmlUsd' => Currency::formatDiningPrice($i->price_usd, $i->price_rwf, 'usd'),
                'priceHtmlRwf' => Currency::formatDiningPrice($i->price_usd, $i->price_rwf, 'rwf'),
                'priceUsd' => number_format((float) $i->price_usd, 2, '.', ''),
                'priceRwfAttr' => $rwfAttr,
                'prepMinutes' => $i->prep_minutes ? (int) $i->prep_minutes : null,
            ];
        };

        $diningMenuColumns = [];
        foreach ($menuCategoriesForDining as $cat) {
            $catItems = $allMenuItems->where('menu_category_id', $cat->id)->values();
            if ($catItems->isEmpty()) {
                continue;
            }
            $diningMenuColumns[] = [
                'label' => $cat->name,
                'items' => $catItems->map($serializeDiningItem)->values()->all(),
            ];
        }
        $uncatItems = $allMenuItems->whereNull('menu_category_id')->values();
        if ($menuCategoriesForDining->isNotEmpty() && $uncatItems->isNotEmpty()) {
            $diningMenuColumns[] = [
                'label' => 'Other',
                'items' => $uncatItems->map($serializeDiningItem)->values()->all(),
            ];
        }
        if ($diningMenuColumns === [] && $allMenuItems->isNotEmpty()) {
            $diningMenuColumns[] = [
                'label' => 'Menu',
                'items' => $allMenuItems->map($serializeDiningItem)->values()->all(),
            ];
        }

        return $diningMenuColumns;
    }

    /**
     * Legacy route: redirect to the unified booking form.
     */
    public function reserveRoom(string $slug)
    {
        return redirect()->route('room.booking', ['room' => $slug]);
    }

    /**
     * Legacy route: redirect to the unified booking form.
     */
    public function saveBookings(Request $request)
    {
        return redirect()->route('room.booking')->with('info', 'Please use the booking form to send your room request.');
    }
}
