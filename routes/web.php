<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Admin\FranchiseResourceController as AdminFranchiseResourceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\DocumentController as FranchiseDocumentController;
use App\Http\Controllers\Franchise\ProfileController as FranchiseProfileController;
use App\Http\Controllers\Franchise\ResourceController as FranchiseResourceController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\WishlistController as StudentWishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Every route below is handled by a Controller that talks to Eloquent
| Models directly and returns a Blade view. There is no JS/AJAX call to
| any of these routes — forms submit normally and pages redirect. The
| only JS on the site is UI-only (modals, nav toggle) plus Razorpay's
| own checkout widget, which is required by Razorpay itself.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/free-demo', [PageController::class, 'freeDemo'])->name('free-demo');
Route::get('/why-rtech', [PageController::class, 'whyRtech'])->name('why-rtech');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

Route::get('/franchise', [FranchiseController::class, 'index'])->name('franchise');
Route::get('/franchises/{franchiseBooking}', [FranchiseController::class, 'show'])->name('franchises.show');
Route::post('/franchise/inquiry', [FranchiseController::class, 'inquiry'])->name('franchise.inquiry');
Route::post('/franchise/booking', [FranchiseController::class, 'bookingCreate'])->name('franchise.booking.create');
Route::post('/franchise/booking/verify', [FranchiseController::class, 'bookingVerify'])->name('franchise.booking.verify');

Route::get('/login', fn () => redirect()->route('home'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/enroll/{course:slug}', [EnrollmentController::class, 'create'])->name('enroll.create');
    Route::post('/enroll/{course:slug}', [EnrollmentController::class, 'store'])->name('enroll.store');
    Route::post('/enroll/{course:slug}/verify', [EnrollmentController::class, 'verify'])->name('enroll.verify');

    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');

        Route::get('/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');

        Route::get('/wishlist', [StudentWishlistController::class, 'index'])->name('student.wishlist.index');
        Route::post('/wishlist/toggle', [StudentWishlistController::class, 'toggle'])->name('student.wishlist.toggle');

        Route::get('/payments', [StudentPaymentController::class, 'index'])->name('student.payments.index');

        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
        Route::post('/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/change-password', [StudentProfileController::class, 'changePassword'])->name('student.password.update');
    });

    Route::prefix('franchise')->middleware('role:franchisee')->group(function () {
        Route::get('/dashboard', [FranchiseDashboardController::class, 'index'])->name('franchise.dashboard');

        Route::get('/documents', [FranchiseDocumentController::class, 'index'])->name('franchise.documents.index');
        Route::post('/documents', [FranchiseDocumentController::class, 'store'])->name('franchise.documents.store');
        Route::get('/documents/{document}/download', [FranchiseDocumentController::class, 'download'])->name('franchise.documents.download');

        Route::get('/resources', [FranchiseResourceController::class, 'index'])->name('franchise.resources.index');
        Route::get('/resources/{resource}/download', [FranchiseResourceController::class, 'download'])->name('franchise.resources.download');

        Route::get('/courses', [FranchiseCourseController::class, 'index'])->name('franchise.courses.index');
        Route::post('/courses', [FranchiseCourseController::class, 'store'])->name('franchise.courses.store');
        Route::post('/courses/{course}', [FranchiseCourseController::class, 'update'])->name('franchise.courses.update');
        Route::delete('/courses/{course}', [FranchiseCourseController::class, 'destroy'])->name('franchise.courses.destroy');

        Route::get('/students', [FranchiseStudentController::class, 'index'])->name('franchise.students.index');

        Route::get('/profile', [FranchiseProfileController::class, 'edit'])->name('franchise.profile.edit');
        Route::post('/profile', [FranchiseProfileController::class, 'update'])->name('franchise.profile.update');
        Route::post('/change-password', [FranchiseProfileController::class, 'changePassword'])->name('franchise.password.update');
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
        Route::post('/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

        Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
        Route::post('/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
        Route::post('/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('admin.coupons.index');
        Route::post('/coupons', [AdminCouponController::class, 'store'])->name('admin.coupons.store');
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('admin.coupons.destroy');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');

        Route::get('/franchise', [AdminFranchiseController::class, 'index'])->name('admin.franchise.index');
        Route::post('/franchise/leads/{lead}', [AdminFranchiseController::class, 'updateLead'])->name('admin.franchise.leads.update');
        Route::post('/franchise/bookings/{booking}', [AdminFranchiseController::class, 'updateBooking'])->name('admin.franchise.bookings.update');
        Route::post('/franchise/bookings/{booking}/documents', [AdminFranchiseController::class, 'uploadAgreement'])->name('admin.franchise.bookings.upload');
        Route::get('/franchise/documents/{document}/download', [AdminFranchiseController::class, 'downloadDocument'])->name('admin.franchise.documents.download');

        Route::get('/franchise/resources', [AdminFranchiseResourceController::class, 'index'])->name('admin.franchise.resources.index');
        Route::post('/franchise/resources', [AdminFranchiseResourceController::class, 'store'])->name('admin.franchise.resources.store');
        Route::delete('/franchise/resources/{resource}', [AdminFranchiseResourceController::class, 'destroy'])->name('admin.franchise.resources.destroy');
    });
});
