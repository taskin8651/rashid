<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CourseAssignmentController as AdminCourseAssignmentController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseNoteController as AdminCourseNoteController;
use App\Http\Controllers\Admin\CourseQuizController as AdminCourseQuizController;
use App\Http\Controllers\Admin\CourseVideoController as AdminCourseVideoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Admin\FranchiseResourceController as AdminFranchiseResourceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Franchise\CourseAssignmentController as FranchiseCourseAssignmentController;
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\CourseNoteController as FranchiseCourseNoteController;
use App\Http\Controllers\Franchise\CourseQuizController as FranchiseCourseQuizController;
use App\Http\Controllers\Franchise\CourseVideoController as FranchiseCourseVideoController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\DocumentController as FranchiseDocumentController;
use App\Http\Controllers\Franchise\ProfileController as FranchiseProfileController;
use App\Http\Controllers\Franchise\ResourceController as FranchiseResourceController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteDownloadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\IdCardController as StudentIdCardController;
use App\Http\Controllers\Student\NoteController as StudentNoteController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\WishlistController as StudentWishlistController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubmissionDownloadController;
use App\Http\Controllers\VideoStreamController;
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

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/free-demo', [PageController::class, 'freeDemo'])->name('free-demo');

// URL must be a valid, unexpired signature (see Video::fileUrl()) AND, for
// non-demo videos, the requester must be authorized — see VideoStreamController.
Route::get('/videos/{video}/stream', [VideoStreamController::class, 'stream'])
    ->name('videos.stream')
    ->middleware('signed');

Route::get('/submissions/{submission}/download', [SubmissionDownloadController::class, 'download'])
    ->name('submissions.download')
    ->middleware('auth');

Route::get('/notes/{note}/download', [NoteDownloadController::class, 'download'])
    ->name('notes.download')
    ->middleware('auth');
Route::get('/why-rtech', [PageController::class, 'whyRtech'])->name('why-rtech');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit')->middleware('throttle:5,1');

Route::get('/franchise', [FranchiseController::class, 'index'])->name('franchise');
Route::get('/franchises/{franchiseBooking}', [FranchiseController::class, 'show'])->name('franchises.show');
Route::post('/franchise/inquiry', [FranchiseController::class, 'inquiry'])->name('franchise.inquiry')->middleware('throttle:5,1');
Route::post('/franchise/booking', [FranchiseController::class, 'bookingCreate'])->name('franchise.booking.create')->middleware('throttle:5,1');
Route::post('/franchise/booking/verify', [FranchiseController::class, 'bookingVerify'])->name('franchise.booking.verify');

Route::get('/login', fn () => redirect()->route('home'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:10,1');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.attempt')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:10,1');

Route::middleware('auth')->group(function () {
    Route::get('/enroll/{course:slug}', [EnrollmentController::class, 'create'])->name('enroll.create');
    Route::post('/enroll/{course:slug}', [EnrollmentController::class, 'store'])->name('enroll.store');
    Route::post('/enroll/{course:slug}/verify', [EnrollmentController::class, 'verify'])->name('enroll.verify');

    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');

        Route::get('/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');
        Route::get('/courses/{course}/learn', [StudentCourseController::class, 'learn'])->name('student.courses.learn');
        Route::post('/videos/{video}/watched', [StudentCourseController::class, 'markWatched'])->name('student.videos.watched');

        Route::get('/courses/{course}/quiz', [StudentQuizController::class, 'show'])->name('student.courses.quiz.show');
        Route::post('/courses/{course}/quiz', [StudentQuizController::class, 'submit'])->name('student.courses.quiz.submit');
        Route::get('/courses/{course}/quiz/{attempt}', [StudentQuizController::class, 'result'])->name('student.courses.quiz.result');

        Route::get('/courses/{course}/assignments', [StudentAssignmentController::class, 'index'])->name('student.courses.assignments.index');
        Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');

        Route::get('/courses/{course}/notes', [StudentNoteController::class, 'index'])->name('student.courses.notes.index');

        Route::get('/certificates', [StudentCertificateController::class, 'index'])->name('student.certificates.index');
        Route::get('/certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('student.certificates.download');

        Route::get('/wishlist', [StudentWishlistController::class, 'index'])->name('student.wishlist.index');
        Route::post('/wishlist/toggle', [StudentWishlistController::class, 'toggle'])->name('student.wishlist.toggle');

        Route::get('/payments', [StudentPaymentController::class, 'index'])->name('student.payments.index');

        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
        Route::post('/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/change-password', [StudentProfileController::class, 'changePassword'])->name('student.password.update');

        Route::get('/id-card', [StudentIdCardController::class, 'view'])->name('student.id-card.view');
        Route::get('/id-card/download', [StudentIdCardController::class, 'download'])->name('student.id-card.download');
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

        Route::get('/courses/{course}/videos', [FranchiseCourseVideoController::class, 'index'])->name('franchise.courses.videos.index');
        Route::post('/courses/{course}/videos', [FranchiseCourseVideoController::class, 'store'])->name('franchise.courses.videos.store');
        Route::post('/courses/{course}/videos/{video}', [FranchiseCourseVideoController::class, 'update'])->name('franchise.courses.videos.update');
        Route::delete('/courses/{course}/videos/{video}', [FranchiseCourseVideoController::class, 'destroy'])->name('franchise.courses.videos.destroy');

        Route::get('/courses/{course}/quiz', [FranchiseCourseQuizController::class, 'index'])->name('franchise.courses.quiz.index');
        Route::post('/courses/{course}/quiz', [FranchiseCourseQuizController::class, 'store'])->name('franchise.courses.quiz.store');
        Route::post('/courses/{course}/quiz/{question}', [FranchiseCourseQuizController::class, 'update'])->name('franchise.courses.quiz.update');
        Route::delete('/courses/{course}/quiz/{question}', [FranchiseCourseQuizController::class, 'destroy'])->name('franchise.courses.quiz.destroy');

        Route::get('/courses/{course}/assignments', [FranchiseCourseAssignmentController::class, 'index'])->name('franchise.courses.assignments.index');
        Route::post('/courses/{course}/assignments', [FranchiseCourseAssignmentController::class, 'store'])->name('franchise.courses.assignments.store');
        Route::post('/courses/{course}/assignments/{assignment}', [FranchiseCourseAssignmentController::class, 'update'])->name('franchise.courses.assignments.update');
        Route::delete('/courses/{course}/assignments/{assignment}', [FranchiseCourseAssignmentController::class, 'destroy'])->name('franchise.courses.assignments.destroy');
        Route::post('/courses/{course}/submissions/{submission}/grade', [FranchiseCourseAssignmentController::class, 'grade'])->name('franchise.courses.assignments.grade');

        Route::get('/courses/{course}/notes', [FranchiseCourseNoteController::class, 'index'])->name('franchise.courses.notes.index');
        Route::post('/courses/{course}/notes', [FranchiseCourseNoteController::class, 'store'])->name('franchise.courses.notes.store');
        Route::post('/courses/{course}/notes/{note}', [FranchiseCourseNoteController::class, 'update'])->name('franchise.courses.notes.update');
        Route::delete('/courses/{course}/notes/{note}', [FranchiseCourseNoteController::class, 'destroy'])->name('franchise.courses.notes.destroy');

        Route::get('/students', [FranchiseStudentController::class, 'index'])->name('franchise.students.index');

        Route::get('/profile', [FranchiseProfileController::class, 'edit'])->name('franchise.profile.edit');
        Route::post('/profile', [FranchiseProfileController::class, 'update'])->name('franchise.profile.update');
        Route::post('/change-password', [FranchiseProfileController::class, 'changePassword'])->name('franchise.password.update');
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
        Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('admin.students.show');
        Route::get('/students/{student}/id-card', [AdminStudentController::class, 'idCardView'])->name('admin.students.id-card.view');
        Route::get('/students/{student}/id-card/download', [AdminStudentController::class, 'idCardDownload'])->name('admin.students.id-card.download');
        Route::post('/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

        Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
        Route::post('/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
        Route::post('/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

        Route::get('/courses/{course}/videos', [AdminCourseVideoController::class, 'index'])->name('admin.courses.videos.index');
        Route::post('/courses/{course}/videos', [AdminCourseVideoController::class, 'store'])->name('admin.courses.videos.store');
        Route::post('/courses/{course}/videos/{video}', [AdminCourseVideoController::class, 'update'])->name('admin.courses.videos.update');
        Route::delete('/courses/{course}/videos/{video}', [AdminCourseVideoController::class, 'destroy'])->name('admin.courses.videos.destroy');

        Route::get('/courses/{course}/quiz', [AdminCourseQuizController::class, 'index'])->name('admin.courses.quiz.index');
        Route::post('/courses/{course}/quiz', [AdminCourseQuizController::class, 'store'])->name('admin.courses.quiz.store');
        Route::post('/courses/{course}/quiz/{question}', [AdminCourseQuizController::class, 'update'])->name('admin.courses.quiz.update');
        Route::delete('/courses/{course}/quiz/{question}', [AdminCourseQuizController::class, 'destroy'])->name('admin.courses.quiz.destroy');

        Route::get('/courses/{course}/assignments', [AdminCourseAssignmentController::class, 'index'])->name('admin.courses.assignments.index');
        Route::post('/courses/{course}/assignments', [AdminCourseAssignmentController::class, 'store'])->name('admin.courses.assignments.store');
        Route::post('/courses/{course}/assignments/{assignment}', [AdminCourseAssignmentController::class, 'update'])->name('admin.courses.assignments.update');
        Route::delete('/courses/{course}/assignments/{assignment}', [AdminCourseAssignmentController::class, 'destroy'])->name('admin.courses.assignments.destroy');
        Route::post('/courses/{course}/submissions/{submission}/grade', [AdminCourseAssignmentController::class, 'grade'])->name('admin.courses.assignments.grade');

        Route::get('/courses/{course}/notes', [AdminCourseNoteController::class, 'index'])->name('admin.courses.notes.index');
        Route::post('/courses/{course}/notes', [AdminCourseNoteController::class, 'store'])->name('admin.courses.notes.store');
        Route::post('/courses/{course}/notes/{note}', [AdminCourseNoteController::class, 'update'])->name('admin.courses.notes.update');
        Route::delete('/courses/{course}/notes/{note}', [AdminCourseNoteController::class, 'destroy'])->name('admin.courses.notes.destroy');

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
