<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\GuestController as AdminGuestController;
use App\Http\Controllers\Admin\GuestInsightsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DiningController;
use App\Http\Controllers\DirectPayPlaceholderController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuestAccountController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\GuestDiningSubmissionController;
use App\Http\Controllers\GuestDiscountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteAnalyticsController;
use App\Http\Controllers\SiteContentController;
use App\Http\Controllers\SlidesController;
use App\Http\Controllers\StayBookingController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'aboutUs'])->name('aboutUs');
Route::get('/accommodation', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/experiences', [HomeController::class, 'experiences'])->name('experiences');
Route::get('/future-4-kids', [HomeController::class, 'future4kids'])->name('future4kids');
Route::redirect('/about-delta-resort-hotel', '/about', 301);
Route::redirect('/accommodation-at-delta-resort', '/accommodation', 301);
Route::get('/rooms/{slug}', [HomeController::class, 'singleRoom'])->name('singleRoom');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/service/{slug}', [HomeController::class, 'singleService'])->name('singleService');
Route::post('/reserveNow', [HomeController::class, 'reserveNow'])->name('reserveNow');
Route::get('/reserve/{slug}', [HomeController::class, 'reserveRoom'])->name('reserveRoom');
Route::post('/saveBookings', [HomeController::class, 'saveBookings'])->name('saveBookings');
Route::get('/facilities', [HomeController::class, 'facilities'])->name('facilities');
Route::get('/dining', [HomeController::class, 'dining'])->name('dining');

Route::get('/book-room', [StayBookingController::class, 'checkout'])->name('room.booking');
Route::get('/booking/checkout', [StayBookingController::class, 'checkout'])->name('booking.checkout');
Route::post('/booking/checkout', [StayBookingController::class, 'store'])->middleware('throttle:25,1')->name('booking.checkout.store');
Route::post('/book-room', [StayBookingController::class, 'store'])->middleware('throttle:25,1')->name('room.booking.store');
Route::get('/booking/catalog.json', [StayBookingController::class, 'catalog'])->name('booking.catalog');
Route::post('/book-room/legacy', [GuestBookingController::class, 'store'])->middleware('throttle:25,1')->name('room.booking.legacy.store');
Route::get('/book-room/confirmation/{publicId}', [GuestBookingController::class, 'confirmation'])->name('room.booking.confirmation');
Route::get('/book-room/open-whatsapp/{publicId}', [GuestBookingController::class, 'openWhatsapp'])->name('room.booking.whatsapp');
Route::get('/book-room/email-instructions/{publicId}', [GuestBookingController::class, 'emailInstructions'])->name('room.booking.email');
Route::get('/book-room/ota/{publicId}/{which}', [GuestBookingController::class, 'otaRedirect'])->name('room.booking.ota');
Route::get('/pay/dpo', DirectPayPlaceholderController::class)->name('pay.dpo');
Route::post('/track/analytics', [SiteAnalyticsController::class, 'store'])->middleware('throttle:180,1')->name('track.analytics');
Route::post('/guest/dining-submission', [GuestDiningSubmissionController::class, 'store'])->middleware('throttle:60,1')->name('guest.dining.store');

Route::get('/unlock-booking-discount', [GuestDiscountController::class, 'show'])->name('guest.discount');
Route::post('/unlock-booking-discount/request-code', [GuestDiscountController::class, 'requestCode'])->middleware('throttle:5,1')->name('guest.discount.code.request');
Route::post('/unlock-booking-discount/verify-code', [GuestDiscountController::class, 'verifyCode'])->middleware('throttle:10,1')->name('guest.discount.code.verify');
Route::post('/unlock-booking-discount/register', [GuestDiscountController::class, 'register'])->middleware('throttle:5,1')->name('guest.discount.register');
Route::post('/unlock-booking-discount/login', [GuestDiscountController::class, 'login'])->middleware('throttle:5,1')->name('guest.discount.login');
Route::get('/guest-updates/unsubscribe/{token}', [GuestAccountController::class, 'unsubscribe'])->name('guest.updates.unsubscribe');

Route::get('/facilities/{slug}', [HomeController::class, 'facilitySingle'])->name('facilitySingle');
Route::get('/restaurant', [HomeController::class, 'restaurant'])->name('restaurant');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::redirect('/Gallery', '/gallery', 301);
Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs');
Route::get('/blogs/{slug}', [HomeController::class, 'blog'])->name('blog');
Route::post('/blogs/{blog}/comments', [BlogCommentController::class, 'store'])->middleware('throttle:15,1')->name('blog.comments.store');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::redirect('/Contact', '/contact', 301);
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

Route::get('/airport-transfer', function () {
    return redirect()->to(route('contact').'#airport-transfer');
})->name('airportTransfer');

Route::post('/sendMessage', [HomeController::class, 'SendMessage'])->name('sendMessage');

// Route::middleware(['normalUser'])->group(function () {
Route::get('/MyCart', [HomeController::class, 'showCart'])->name('showCart');
Route::get('/MyCart/{id}', [HomeController::class, 'removeFood'])->name('removeFood');
Route::post('/addCart/{id}', [HomeController::class, 'addCart'])->name('addCart');
Route::post('confirmOrder', [HomeController::class, 'confirmOrder'])->name('confirmOrder');

Route::middleware(['auth'])->group(function () {
    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/unlock-booking-discount/verify', [GuestDiscountController::class, 'verifyForm'])->name('guest.discount.verify');
    Route::post('/unlock-booking-discount/verify', [GuestDiscountController::class, 'verify'])->middleware('throttle:10,1')->name('guest.discount.verify.store');
    Route::post('/unlock-booking-discount/resend', [GuestDiscountController::class, 'resend'])->middleware('throttle:2,1')->name('guest.discount.resend');
    Route::get('/my-bookings', [GuestAccountController::class, 'bookings'])->name('guest.bookings');
    Route::get('/my-updates', [GuestAccountController::class, 'updates'])->name('guest.updates');
});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/guest-insights', [GuestInsightsController::class, 'index'])->name('guestInsights');
    Route::post('/guest-insights/bookings/{publicId}/status', [GuestInsightsController::class, 'updateBookingStatus'])->name('guestInsights.booking.status');
    Route::get('/guests', [AdminGuestController::class, 'index'])->name('admin.guests.index');
    Route::post('/guests/updates', [AdminGuestController::class, 'sendUpdate'])->name('admin.guests.updates.send');

    Route::get('/updates', [BlogController::class, 'index'])->name('admin.blogs.index');
    Route::get('/updates/create', [BlogController::class, 'create'])->name('admin.blogs.create');
    Route::post('/updates', [BlogController::class, 'store'])->name('admin.blogs.store');
    Route::get('/updates/{blog}/edit', [BlogController::class, 'edit'])->name('admin.blogs.edit');
    Route::put('/updates/{blog}', [BlogController::class, 'update'])->name('admin.blogs.update');
    Route::delete('/updates/{blog}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy');
    Route::delete('/updates/{blog}/comments', [BlogController::class, 'destroyAllComments'])->name('admin.blogs.comments.destroy-all');
    Route::delete('/updates/{blog}/comments/{comment}', [BlogController::class, 'destroyComment'])->name('admin.blogs.comments.destroy');

    Route::get('/setting', [SettingController::class, 'setting'])->name('setting');
    Route::post('/saveSetting', [SettingController::class, 'saveSetting'])->name('saveSetting');
    Route::get('/dashboard/about', [SettingController::class, 'about'])->name('about');
    Route::post('/saveAbout', [SettingController::class, 'saveAbout'])->name('saveAbout');

    Route::get('/dining-menu', [DiningController::class, 'index'])->name('diningMenu');
    Route::get('/dining-menu/manage', [DiningController::class, 'menuManage'])->name('diningMenu.manage');
    Route::get('/dining-menu/categories/manage', [DiningController::class, 'menuCategoriesManage'])->name('diningMenu.categories.manage');
    Route::post('/dining-menu/page', [DiningController::class, 'savePage'])->name('diningMenu.page');
    Route::post('/dining-menu/categories', [DiningController::class, 'storeCategory'])->name('diningMenu.categories.store');
    Route::post('/dining-menu/categories/{category}', [DiningController::class, 'updateCategory'])->name('diningMenu.categories.update');
    Route::delete('/dining-menu/categories/{category}', [DiningController::class, 'destroyCategory'])->name('diningMenu.categories.destroy');
    Route::post('/dining-menu/ai-images', [DiningController::class, 'aiSuggestImages'])->name('diningMenu.aiImages');
    Route::post('/dining-menu/categories/{category}/cover-from-url', [DiningController::class, 'saveCategoryCoverFromUrl'])->name('diningMenu.categories.coverUrl');
    Route::post('/dining-menu/items', [DiningController::class, 'storeMenuItem'])->name('diningMenu.items.store');
    Route::post('/dining-menu/items/{item}', [DiningController::class, 'updateMenuItem'])->name('diningMenu.items.update');
    Route::delete('/dining-menu/items/{item}', [DiningController::class, 'destroyMenuItem'])->name('diningMenu.items.destroy');
    Route::post('/dining-gallery', [DiningController::class, 'storeGallery'])->name('diningGallery.store');
    Route::delete('/dining-gallery/{diningGalleryImage}', [DiningController::class, 'destroyGallery'])->name('diningGallery.destroy');

    // Facilities
    Route::get('/roomType', [RoomsController::class, 'roomType'])->name('roomType');
    Route::post('/roomTypeCreate', [RoomsController::class, 'roomTypeCreate'])->name('roomTypeCreate');
    Route::get('/roomTypeDelete/{id}', [RoomsController::class, 'roomTypeDelete'])->name('roomTypeDelete');
    Route::post('/amenityCreate', [RoomsController::class, 'amenityCreate'])->name('amenityCreate');
    Route::get('/amenityDelete/{id}', [RoomsController::class, 'amenityDelete'])->name('amenityDelete');

    Route::get('/roomCrud', [RoomsController::class, 'index'])->name('roomCrud');
    Route::get('/roomCrud', [RoomsController::class, 'index'])->name('roomCrud');
    Route::post('/saveRoom', [RoomsController::class, 'store'])->name('saveRoom');
    Route::get('/editRoom/{id}', [RoomsController::class, 'edit'])->name('editRoom');
    Route::post('/updateRoom/{id}', [RoomsController::class, 'update'])->name('updateRoom');
    Route::get('/destroyRoom/{id}', [RoomsController::class, 'destroy'])->name('destroyRoom');

    // Facilities
    Route::get('/getFacilities', [FacilitiesController::class, 'index'])->name('facilityCrud');
    Route::post('/saveFacility', [FacilitiesController::class, 'store'])->name('saveFacility');
    Route::get('/editFacility/{id}', [FacilitiesController::class, 'edit'])->name('editFacility');
    Route::post('/updateFacility/{id}', [FacilitiesController::class, 'update'])->name('updateFacility');
    Route::get('/destroyFacility/{id}', [FacilitiesController::class, 'destroy'])->name('destroyFacility');

    Route::get('/facilityImages/{pid}', [FacilitiesController::class, 'facilityImages'])->name('facilityImages');
    Route::post('/savFacilityImage/{pid}', [FacilitiesController::class, 'savFacImage'])->name('savFacImage');
    Route::get('/destroyFacilityImage/{pid}/{id}', [FacilitiesController::class, 'destroyFacImage'])->name('destroyFacImage');

    // Services
    Route::get('/getServices', [ServicesController::class, 'index'])->name('getServices');
    Route::post('/saveService', [ServicesController::class, 'store'])->name('saveService');
    Route::get('/editService/{id}', [ServicesController::class, 'edit'])->name('editService');
    Route::post('/updateService/{id}', [ServicesController::class, 'update'])->name('updateService');
    Route::get('/destroyService/{id}', [ServicesController::class, 'destroy'])->name('destroyService');

    Route::get('/serviceImages/{pid}', [ServicesController::class, 'serviceImages'])->name('serviceImages');
    Route::post('/savServiceImage/{pid}', [ServicesController::class, 'savServiceImage'])->name('savServiceImage');
    Route::get('/destroyServiceImage/{pid}/{id}', [ServicesController::class, 'destroyServiceImage'])->name('destroyServiceImage');

    // Rooms
    Route::get('/getRooms', [RoomsController::class, 'index'])->name('getRooms');
    Route::post('/saveRoom', [RoomsController::class, 'store'])->name('saveRoom');
    Route::get('/editRoom/{id}', [RoomsController::class, 'edit'])->name('editRoom');
    Route::post('/updateRoom/{id}', [RoomsController::class, 'update'])->name('updateRoom');
    Route::get('/destroyRoom/{id}', [RoomsController::class, 'destroy'])->name('destroyRoom');

    Route::get('/roomImages/{pid}', [RoomsController::class, 'roomImages'])->name('roomImages');
    Route::post('/savRoomImage/{pid}', [RoomsController::class, 'savRoomImage'])->name('savRoomImage');
    Route::get('/destroyRoomImage/{pid}/{id}', [RoomsController::class, 'destroyRoomImage'])->name('destroyRoomImage');
    // Partners
    Route::get('/partnerCrud', [PartnersController::class, 'index'])->name('partnerCrud');
    Route::post('/savePartner', [PartnersController::class, 'store'])->name('savePartner');
    Route::get('/editPartner/{id}', [PartnersController::class, 'edit'])->name('editPartner');
    Route::post('/updatePartner/{id}', [PartnersController::class, 'update'])->name('updatePartner');
    Route::get('/destroyPartner/{id}', [PartnersController::class, 'destroy'])->name('destroyPartner');

    // Gallery
    Route::get('/slides', [SlidesController::class, 'index'])->name('slides');
    Route::get('/site-content', [SiteContentController::class, 'index'])->name('siteContent');
    Route::post('/site-content', [SiteContentController::class, 'save'])->name('siteContent.save');
    Route::redirect('/page-headers', '/site-content', 301);
    Route::post('/saveSlide', [SlidesController::class, 'store'])->name('saveSlide');
    Route::get('/editSlide/{id}', [SlidesController::class, 'edit'])->name('editSlide');
    Route::post('/updateSlide/{id}', [SlidesController::class, 'update'])->name('updateSlide');
    Route::get('/destroySlide/{id}', [SlidesController::class, 'destroy'])->name('destroySlide');

    // Gallery
    Route::get('/getGalleries', [GalleryController::class, 'index'])->name('getGalleries');
    Route::post('/saveGallery', [GalleryController::class, 'store'])->name('saveGallery');
    Route::get('/editGallery/{id}', [GalleryController::class, 'edit'])->name('editGallery');
    Route::post('/updateGallery/{id}', [GalleryController::class, 'update'])->name('updateGallery');
    Route::get('/destroyGallery/{id}', [GalleryController::class, 'destroy'])->name('destroyGallery');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/search', [BookingController::class, 'search'])->name('searchBookings');
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::get('/TablesBookings', [BookingController::class, 'TablesBookings'])->name('TablesBookings');
    Route::get('/testBooking', [BookingController::class, 'create'])->name('testBooking');
    Route::post('/saveBooking', [BookingController::class, 'store'])->name('saveBooking');
    Route::get('/viewBooking/{id}', [BookingController::class, 'viewBooking'])->name('viewBooking');
    Route::get('/editBooking/{id}', [BookingController::class, 'edit'])->name('editBooking');
    // Route::post('/updateBooking/{id}', [App\Http\Controllers\BookingController::class, 'updateBooking'])->name('updateSlide');
    Route::get('/destroyBooking/{id}', [BookingController::class, 'destroy'])->name('destroyBooking');

    Route::get('/roomBookings/availableRooms/{checkinDate}', [BookingController::class, 'availableRooms'])->name('availableRooms');

    Route::get('/FoodOrders', [BookingController::class, 'FoodOrders'])->name('FoodOrders');
    Route::get('/Print', [BookingController::class, 'print'])->name('print');

    Route::middleware('superadmin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users');
        Route::post('/users/{user}/verify', [UserManagementController::class, 'verify'])->name('admin.users.verify');
        Route::post('/users/{user}/unverify', [UserManagementController::class, 'unverify'])->name('admin.users.unverify');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.role');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');
    });

});
