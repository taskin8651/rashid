<?php

use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\FranchiseLeadController as AdminFranchiseLeadController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\FranchiseController;
use App\Http\Controllers\Api\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Api\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Api\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Api\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Api\Student\WishlistController as StudentWishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/auth/signup', [AuthController::class, 'signup'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}/batches', [CourseController::class, 'batches']);
Route::get('/courses/{slug}', [CourseController::class, 'show']);

Route::post('/coupons/validate', [CouponController::class, 'validateCode']);
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:10,1');

Route::post('/franchise/inquiry', [FranchiseController::class, 'inquiry'])->middleware('throttle:10,1');
Route::post('/franchise/booking/create-order', [FranchiseController::class, 'bookingCreateOrder']);
Route::post('/franchise/booking/verify-payment', [FranchiseController::class, 'bookingVerifyPayment']);

/*
|--------------------------------------------------------------------------
| Authenticated routes (any logged-in user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/enroll/create-order', [EnrollmentController::class, 'createOrder']);
    Route::post('/enroll/verify-payment', [EnrollmentController::class, 'verifyPayment']);

    /*
    |----------------------------------------------------------------------
    | Student routes (role: student)
    |----------------------------------------------------------------------
    */
    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard-summary', [StudentDashboardController::class, 'summary']);
        Route::get('/my-courses', [StudentCourseController::class, 'myCourses']);
        Route::get('/payment-history', [StudentPaymentController::class, 'history']);
        Route::get('/profile', [StudentProfileController::class, 'show']);
        Route::post('/profile', [StudentProfileController::class, 'update']);
        Route::post('/change-password', [StudentProfileController::class, 'changePassword']);
        Route::get('/wishlist', [StudentWishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [StudentWishlistController::class, 'toggle']);
    });

    /*
    |----------------------------------------------------------------------
    | Admin routes (role: admin)
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard-stats', [AdminDashboardController::class, 'stats']);

        Route::get('/students', [AdminStudentController::class, 'index']);
        Route::get('/students/{student}', [AdminStudentController::class, 'show']);
        Route::post('/students/{student}', [AdminStudentController::class, 'update']);
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy']);

        Route::get('/courses', [AdminCourseController::class, 'index']);
        Route::post('/courses', [AdminCourseController::class, 'store']);
        Route::post('/courses/{course}', [AdminCourseController::class, 'update']);
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy']);

        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::post('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        Route::get('/coupons', [AdminCouponController::class, 'index']);
        Route::post('/coupons', [AdminCouponController::class, 'store']);
        Route::post('/coupons/{coupon}', [AdminCouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy']);

        Route::get('/payments', [AdminPaymentController::class, 'index']);
        Route::post('/payments/{payment}/status', [AdminPaymentController::class, 'updateStatus']);

        Route::get('/franchise-leads', [AdminFranchiseLeadController::class, 'index']);
        Route::post('/franchise-leads/{lead}', [AdminFranchiseLeadController::class, 'update']);

        Route::get('/contact-messages', [AdminContactMessageController::class, 'index']);
        Route::post('/contact-messages/{message}', [AdminContactMessageController::class, 'update']);
    });
});
