<?php

use App\Http\Controllers\api\admin\AboutController;
use App\Http\Controllers\api\admin\AdminController;
use App\Http\Controllers\api\admin\CategoryController;
use App\Http\Controllers\api\admin\CityController;
use App\Http\Controllers\api\admin\ContactController;
use App\Http\Controllers\api\admin\CouponController;
use App\Http\Controllers\api\admin\HomeController;
use App\Http\Controllers\api\admin\OrderController;
use App\Http\Controllers\api\admin\PaymentMethodController;
use App\Http\Controllers\api\admin\ProductController;
use App\Http\Controllers\api\admin\ServiceController;
use App\Http\Controllers\api\admin\SettingController;
use App\Http\Controllers\api\admin\UserController;
use App\Http\Controllers\api\admin\ZoneController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\StripeWebhookController;
use App\Http\Controllers\api\user\UserAboutController;
use App\Http\Controllers\api\user\UserAddressController;
use App\Http\Controllers\api\user\UserCartController;
use App\Http\Controllers\api\user\UserContactController;
use App\Http\Controllers\api\user\UserHomeController;
use App\Http\Controllers\api\user\UserOrderController;
use App\Http\Controllers\api\user\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('main_settings', [AuthController::class, 'main_settings']);
Route::post('login', [AuthController::class, 'login']);
Route::post('check_code', [AuthController::class, 'check_code']);
Route::post('sign_up', [AuthController::class, 'sign_up']);

// Forget Password (public — no auth required)
Route::post('forget_password', [AuthController::class, 'forget_password']);
Route::post('check_code_forget_password', [AuthController::class, 'check_code_forget_password']);
Route::post('new_password_forget_password', [AuthController::class, 'new_password_forget_password']);

// Stripe Webhook — NO auth middleware (Stripe sends raw POST with signature)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);

// Public (no auth)
Route::prefix('user')->group(function () {
    Route::get('home/all_products', [UserHomeController::class, 'all_products']);
    Route::get('home/parent-categories', [UserHomeController::class, 'parent_categories']);
    Route::get('home/sub-categories', [UserHomeController::class, 'sub_categories']);
    Route::get('home/products', [UserHomeController::class, 'products']);
    Route::get('home/product/{id}', [UserHomeController::class, 'product_details']);
    Route::get('orders/lists', [UserOrderController::class, 'lists']);
    Route::get('footer', [UserHomeController::class, 'footer']);
    Route::get('about', [UserAboutController::class, 'about']);
    Route::get('services', [UserAboutController::class, 'services']);
    Route::post('contact', [UserContactController::class, 'contact']);
});


Route::middleware(['auth:sanctum', "admin"])
->prefix("admin")->group(function () {

    // Home
    Route::get('home', [HomeController::class, 'index']);

    // Admins
    Route::get('admins', [AdminController::class, 'index']);
    Route::get('admins/list', [AdminController::class, 'list']);
    Route::post('admins', [AdminController::class, 'store']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::post('admins/{id}', [AdminController::class, 'update']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
    Route::post('admins/{id}/change-status', [AdminController::class, 'changeStatus']);

    // Users
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/list', [UserController::class, 'list']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{id}/change-status', [UserController::class, 'changeStatus']);

    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/list', [CategoryController::class, 'list']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    Route::post('categories/{id}/change-status', [CategoryController::class, 'changeStatus']);

    // Cities
    Route::get('cities', [CityController::class, 'index']);
    Route::get('cities/list', [CityController::class, 'list']);
    Route::post('cities', [CityController::class, 'store']);
    Route::get('cities/{id}', [CityController::class, 'show']);
    Route::post('cities/{id}', [CityController::class, 'update']);
    Route::delete('cities/{id}', [CityController::class, 'destroy']);
    Route::post('cities/{id}/change-status', [CityController::class, 'changeStatus']);

    // Contact 
    Route::get('contact', [ContactController::class, 'index']);
    Route::get('contact/history', [ContactController::class, 'history']);
    Route::get('contact/read/{id}', [ContactController::class, 'read']);

    // Zones
    Route::get('zones', [ZoneController::class, 'index']);
    Route::get('zones/list', [ZoneController::class, 'list']);
    Route::post('zones', [ZoneController::class, 'store']);
    Route::get('zones/{id}', [ZoneController::class, 'show']);
    Route::post('zones/{id}', [ZoneController::class, 'update']);
    Route::delete('zones/{id}', [ZoneController::class, 'destroy']);
    Route::post('zones/{id}/change-status', [ZoneController::class, 'changeStatus']);

    // Payment Methods
    Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('payment-methods/list', [PaymentMethodController::class, 'list']);
    Route::post('payment-methods', [PaymentMethodController::class, 'store']);
    Route::get('payment-methods/{id}', [PaymentMethodController::class, 'show']);
    Route::post('payment-methods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('payment-methods/{id}', [PaymentMethodController::class, 'destroy']);
    Route::post('payment-methods/{id}/change-status', [PaymentMethodController::class, 'changeStatus']);

    // About
    Route::get('about', [AboutController::class, 'index']);
    Route::post('about', [AboutController::class, 'update']);

    // Services
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('services', [ServiceController::class, 'store']);
    Route::get('services/{id}', [ServiceController::class, 'show']);
    Route::post('services/{id}', [ServiceController::class, 'update']);
    Route::delete('services/{id}', [ServiceController::class, 'destroy']);

    // Settings
    Route::get('settings', [SettingController::class, 'show']);
    Route::post('settings', [SettingController::class, 'update']);

    // Coupons
    Route::get('coupons', [CouponController::class, 'index']);
    Route::get('coupons/list', [CouponController::class, 'list']);
    Route::post('coupons', [CouponController::class, 'store']);
    Route::get('coupons/{id}', [CouponController::class, 'show']);
    Route::post('coupons/{id}', [CouponController::class, 'update']);
    Route::delete('coupons/{id}', [CouponController::class, 'destroy']);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders/{id}/payment-status', [OrderController::class, 'changePaymentStatus']);
    Route::post('orders/{id}/status', [OrderController::class, 'changeStatus']);

    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/list', [ProductController::class, 'list']);
    Route::get('products/categories', [ProductController::class, 'categories']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::post('products/{id}/change-status', [ProductController::class, 'changeStatus']);
    // Gallery
    Route::post('products/{id}/gallery', [ProductController::class, 'addGallery']);
    Route::delete('products/gallery/{id}', [ProductController::class, 'deleteGalleryImage']);
    // Variations
    Route::post('products/{id}/variations', [ProductController::class, 'addVariation']);
    Route::delete('products/variations/{id}', [ProductController::class, 'deleteVariation']);
    // Options
    Route::post('products/variations/{variationId}/options', [ProductController::class, 'addOption']);
    Route::delete('products/options/{id}', [ProductController::class, 'deleteOption']);

});

Route::middleware(['auth:sanctum', "user"])
->prefix("user")->group(function () {

    // Profile lists 
    Route::get('profile', [UserProfileController::class, 'profile']);
    Route::post('update_profile', [UserProfileController::class, 'update_profile']);

    // Address lists 
    Route::get('addresses/cities', [UserAddressController::class, 'cities']);
    Route::get('addresses/zones', [UserAddressController::class, 'zones']);
    

    // Address CRUD
    Route::get('addresses', [UserAddressController::class, 'index']);
    Route::post('addresses', [UserAddressController::class, 'store']);
    Route::get('addresses/{id}', [UserAddressController::class, 'show']);
    Route::post('addresses/{id}', [UserAddressController::class, 'update']);
    Route::delete('addresses/{id}', [UserAddressController::class, 'destroy']);

    // Cart
    Route::get('cart', [UserCartController::class, 'index']);
    Route::post('cart', [UserCartController::class, 'store']);
    Route::post('cart/{id}', [UserCartController::class, 'update']);
    Route::delete('cart/clear', [UserCartController::class, 'clear']);
    Route::delete('cart/{id}', [UserCartController::class, 'destroy']);

    // Orders
    Route::post('orders/make', [UserOrderController::class, 'make_order']);
    Route::post('orders/check-coupon', [UserOrderController::class, 'check_coupon']);
    Route::get('orders', [UserOrderController::class, 'order_history']);
    Route::get('orders/{id}', [UserOrderController::class, 'order_details']);

});
