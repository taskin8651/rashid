<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceLocationController as AdminAttendanceLocationController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CourseAssignmentController as AdminCourseAssignmentController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseNoteController as AdminCourseNoteController;
use App\Http\Controllers\Admin\CourseQuizController as AdminCourseQuizController;
use App\Http\Controllers\Admin\CourseVideoController as AdminCourseVideoController;
use App\Http\Controllers\Admin\DailyReportController as AdminDailyReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\FranchiseController as AdminFranchiseController;
use App\Http\Controllers\Admin\FranchiseResourceController as AdminFranchiseResourceController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PlacementController as AdminPlacementController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\TeamMemberController as AdminTeamMemberController;
use App\Http\Controllers\Admin\CertificateApplicationController as AdminCertificateApplicationController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\Admin\JobPostingController as AdminJobPostingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CertificateApplicationController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LeadCaptureController;
use App\Http\Controllers\Franchise\AttendanceController as FranchiseAttendanceController;
use App\Http\Controllers\Franchise\CertificateController as FranchiseCertificateController;
use App\Http\Controllers\Franchise\CourseAssignmentController as FranchiseCourseAssignmentController;
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\CourseNoteController as FranchiseCourseNoteController;
use App\Http\Controllers\Franchise\CourseQuizController as FranchiseCourseQuizController;
use App\Http\Controllers\Franchise\CourseVideoController as FranchiseCourseVideoController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\DocumentController as FranchiseDocumentController;
use App\Http\Controllers\Franchise\ExpenseController as FranchiseExpenseController;
use App\Http\Controllers\Franchise\GalleryController as FranchiseGalleryController;
use App\Http\Controllers\Franchise\LeadController as FranchiseLeadController;
use App\Http\Controllers\Franchise\PaymentController as FranchisePaymentController;
use App\Http\Controllers\Franchise\PlacementController as FranchisePlacementController;
use App\Http\Controllers\Franchise\ProfileController as FranchiseProfileController;
use App\Http\Controllers\Franchise\ResourceController as FranchiseResourceController;
use App\Http\Controllers\Franchise\ReviewController as FranchiseReviewController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\Franchise\TeamController as FranchiseTeamController;
use App\Http\Controllers\Franchise\TeamMemberController as FranchiseTeamMemberController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NoteDownloadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\Portal\DailyReportController as PortalDailyReportController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\IdCardController as StudentIdCardController;
use App\Http\Controllers\Student\NoteController as StudentNoteController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\PlacementController as StudentPlacementController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ReferralController as StudentReferralController;
use App\Http\Controllers\Student\ReviewController as StudentReviewController;
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
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/free-demo', [PageController::class, 'freeDemo'])->name('free-demo');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/placements', [PlacementController::class, 'index'])->name('placements');

Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/careers/{career}', [CareerController::class, 'show'])->name('careers.show');
Route::post('/careers/{career}/apply', [CareerController::class, 'apply'])->name('careers.apply')->middleware('throttle:5,1');

Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [PageController::class, 'refundPolicy'])->name('refund-policy');

Route::get('/certificates/verify', [CertificateVerificationController::class, 'show'])->name('certificates.verify');

Route::get('/apply-certificate', [CertificateApplicationController::class, 'create'])->name('certificate-applications.create');
Route::post('/apply-certificate', [CertificateApplicationController::class, 'store'])->name('certificate-applications.store')->middleware('throttle:5,1');

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
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit')->middleware('throttle:5,1');

Route::get('/leads/apply/{token?}', [LeadCaptureController::class, 'show'])->name('leads.apply');
Route::post('/leads/apply', [LeadCaptureController::class, 'store'])->name('leads.apply.submit')->middleware('throttle:10,1');

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

Route::middleware(['auth', 'track.active'])->group(function () {
    Route::get('/enroll/{course:slug}', [EnrollmentController::class, 'create'])->name('enroll.create');
    Route::post('/enroll/{course:slug}', [EnrollmentController::class, 'store'])->name('enroll.store');
    Route::post('/enroll/{course:slug}/verify', [EnrollmentController::class, 'verify'])->name('enroll.verify');
    Route::post('/enroll/{course:slug}/upi-submit', [EnrollmentController::class, 'submitUpiPayment'])->name('enroll.upi.submit');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/certificate-applications', [CertificateApplicationController::class, 'status'])->name('certificate-applications.status');

    Route::middleware('role:student')->group(function () {
        Route::get('/attendance/scan/{qrToken}', [AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/attendance/mark', [AttendanceController::class, 'store'])->name('attendance.mark');
    });

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
        Route::get('/certificates/{certificate}/view', [StudentCertificateController::class, 'view'])->name('student.certificates.view');
        Route::get('/certificates/{certificate}/marksheet', [StudentCertificateController::class, 'downloadMarksheet'])->name('student.certificates.marksheet');
        Route::get('/certificates/{certificate}/marksheet/view', [StudentCertificateController::class, 'viewMarksheet'])->name('student.certificates.marksheet.view');

        Route::get('/wishlist', [StudentWishlistController::class, 'index'])->name('student.wishlist.index');
        Route::post('/wishlist/toggle', [StudentWishlistController::class, 'toggle'])->name('student.wishlist.toggle');

        Route::get('/payments', [StudentPaymentController::class, 'index'])->name('student.payments.index');

        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
        Route::post('/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/change-password', [StudentProfileController::class, 'changePassword'])->name('student.password.update');

        Route::get('/id-card', [StudentIdCardController::class, 'view'])->name('student.id-card.view');
        Route::get('/id-card/download', [StudentIdCardController::class, 'download'])->name('student.id-card.download');

        Route::post('/courses/{course}/review', [StudentReviewController::class, 'store'])->name('student.courses.review.store');

        Route::get('/referrals', [StudentReferralController::class, 'index'])->name('student.referrals.index');

        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('student.attendance.index');

        Route::get('/placements', [StudentPlacementController::class, 'index'])->name('student.placements.index');
        Route::post('/placements', [StudentPlacementController::class, 'store'])->name('student.placements.store');
        Route::delete('/placements/{placement}', [StudentPlacementController::class, 'destroy'])->name('student.placements.destroy');
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

        Route::get('/leads', [FranchiseLeadController::class, 'index'])->name('franchise.leads.index');
        Route::get('/leads/export', [FranchiseLeadController::class, 'export'])->name('franchise.leads.export');
        Route::post('/leads', [FranchiseLeadController::class, 'store'])->name('franchise.leads.store');
        Route::get('/leads/{lead}', [FranchiseLeadController::class, 'show'])->name('franchise.leads.show');
        Route::post('/leads/{lead}', [FranchiseLeadController::class, 'update'])->name('franchise.leads.update');
        Route::post('/leads/{lead}/status', [FranchiseLeadController::class, 'updateStatus'])->name('franchise.leads.status.update');
        Route::post('/leads/{lead}/notes', [FranchiseLeadController::class, 'addNote'])->name('franchise.leads.notes.store');
        Route::post('/leads/{lead}/convert', [FranchiseLeadController::class, 'convert'])->name('franchise.leads.convert');
        Route::delete('/leads/{lead}', [FranchiseLeadController::class, 'destroy'])->name('franchise.leads.destroy');

        Route::get('/students', [FranchiseStudentController::class, 'index'])->name('franchise.students.index');
        Route::get('/students/export', [FranchiseStudentController::class, 'export'])->name('franchise.students.export');
        Route::post('/students/offline-enroll', [FranchiseStudentController::class, 'storeOffline'])->name('franchise.students.offline-enroll');
        Route::post('/students/{student}/allot-course', [FranchiseStudentController::class, 'allotCourse'])->name('franchise.students.allot-course');
        Route::get('/students/{student}', [FranchiseStudentController::class, 'show'])->name('franchise.students.show');
        Route::get('/students/{student}/id-card', [FranchiseStudentController::class, 'idCardView'])->name('franchise.students.id-card.view');
        Route::get('/students/{student}/id-card/download', [FranchiseStudentController::class, 'idCardDownload'])->name('franchise.students.id-card.download');
        Route::post('/students/{student}', [FranchiseStudentController::class, 'update'])->name('franchise.students.update');

        Route::get('/payments', [FranchisePaymentController::class, 'index'])->name('franchise.payments.index');
        Route::get('/payments/export', [FranchisePaymentController::class, 'export'])->name('franchise.payments.export');

        Route::get('/certificates', [FranchiseCertificateController::class, 'index'])->name('franchise.certificates.index');
        Route::get('/certificates/{certificate}/download', [FranchiseCertificateController::class, 'download'])->name('franchise.certificates.download');
        Route::get('/certificates/{certificate}/view', [FranchiseCertificateController::class, 'view'])->name('franchise.certificates.view');
        Route::get('/certificates/{certificate}/marksheet', [FranchiseCertificateController::class, 'downloadMarksheet'])->name('franchise.certificates.marksheet');
        Route::get('/certificates/{certificate}/marksheet/view', [FranchiseCertificateController::class, 'viewMarksheet'])->name('franchise.certificates.marksheet.view');
        Route::get('/certificate-applications/{application}/proof', [FranchiseCertificateController::class, 'downloadProof'])->name('franchise.certificate-applications.proof');

        Route::get('/reviews', [FranchiseReviewController::class, 'index'])->name('franchise.reviews.index');

        Route::get('/expenses', [FranchiseExpenseController::class, 'index'])->name('franchise.expenses.index');
        Route::get('/expenses/export', [FranchiseExpenseController::class, 'export'])->name('franchise.expenses.export');
        Route::post('/expenses', [FranchiseExpenseController::class, 'store'])->name('franchise.expenses.store');
        Route::get('/expenses/{expense}/receipt', [FranchiseExpenseController::class, 'downloadReceipt'])->name('franchise.expenses.receipt');
        Route::delete('/expenses/{expense}', [FranchiseExpenseController::class, 'destroy'])->name('franchise.expenses.destroy');
        Route::post('/enrollments/{enrollment}/payments', [FranchiseStudentController::class, 'storePayment'])->name('franchise.enrollments.payments.store');
        Route::post('/enrollments/{enrollment}/fee', [FranchiseStudentController::class, 'updateFee'])->name('franchise.enrollments.fee.update');

        Route::get('/gallery', [FranchiseGalleryController::class, 'index'])->name('franchise.gallery.index');
        Route::post('/gallery', [FranchiseGalleryController::class, 'store'])->name('franchise.gallery.store');
        Route::delete('/gallery/{gallery}', [FranchiseGalleryController::class, 'destroy'])->name('franchise.gallery.destroy');

        Route::get('/placements', [FranchisePlacementController::class, 'index'])->name('franchise.placements.index');
        Route::post('/placements', [FranchisePlacementController::class, 'store'])->name('franchise.placements.store');
        Route::delete('/placements/{placement}', [FranchisePlacementController::class, 'destroy'])->name('franchise.placements.destroy');

        Route::get('/attendance', [FranchiseAttendanceController::class, 'manage'])->name('franchise.attendance.manage');
        Route::get('/attendance/records', [FranchiseAttendanceController::class, 'records'])->name('franchise.attendance.records');
        Route::get('/attendance/export', [FranchiseAttendanceController::class, 'export'])->name('franchise.attendance.export');
        Route::post('/attendance/{location}', [FranchiseAttendanceController::class, 'update'])->name('franchise.attendance.update');

        Route::get('/team', [FranchiseTeamController::class, 'index'])->name('franchise.team.index');
        Route::post('/team/roles', [FranchiseTeamController::class, 'storeRole'])->name('franchise.team.roles.store');
        Route::post('/team/roles/{role}', [FranchiseTeamController::class, 'updateRole'])->name('franchise.team.roles.update');
        Route::delete('/team/roles/{role}', [FranchiseTeamController::class, 'destroyRole'])->name('franchise.team.roles.destroy');
        Route::post('/team/members', [FranchiseTeamController::class, 'storeMember'])->name('franchise.team.members.store');
        Route::post('/team/members/{member}', [FranchiseTeamController::class, 'updateMember'])->name('franchise.team.members.update');
        Route::delete('/team/members/{member}', [FranchiseTeamController::class, 'destroyMember'])->name('franchise.team.members.destroy');

        Route::get('/team-members', [FranchiseTeamMemberController::class, 'index'])->name('franchise.team-members.index');
        Route::post('/team-members', [FranchiseTeamMemberController::class, 'store'])->name('franchise.team-members.store');
        Route::post('/team-members/{teamMember}', [FranchiseTeamMemberController::class, 'update'])->name('franchise.team-members.update');
        Route::delete('/team-members/{teamMember}', [FranchiseTeamMemberController::class, 'destroy'])->name('franchise.team-members.destroy');

        Route::get('/profile', [FranchiseProfileController::class, 'edit'])->name('franchise.profile.edit');
        Route::post('/profile', [FranchiseProfileController::class, 'update'])->name('franchise.profile.update');
        Route::post('/change-password', [FranchiseProfileController::class, 'changePassword'])->name('franchise.password.update');
    });

    Route::prefix('staff')->middleware('role:staff')->group(function () {
        Route::get('/reports', [PortalDailyReportController::class, 'index'])->name('staff.reports.index');
        Route::get('/reports/create', [PortalDailyReportController::class, 'create'])->name('staff.reports.create');
        Route::post('/reports', [PortalDailyReportController::class, 'store'])->name('staff.reports.store');
        Route::get('/reports/{dailyReport}/edit', [PortalDailyReportController::class, 'edit'])->name('staff.reports.edit');
        Route::put('/reports/{dailyReport}', [PortalDailyReportController::class, 'update'])->name('staff.reports.update');
        Route::get('/reports/{dailyReport}/attachment', [PortalDailyReportController::class, 'download'])->name('staff.reports.attachment');
    });

    Route::prefix('teacher')->middleware('role:teacher')->group(function () {
        Route::get('/reports', [PortalDailyReportController::class, 'index'])->name('teacher.reports.index');
        Route::get('/reports/create', [PortalDailyReportController::class, 'create'])->name('teacher.reports.create');
        Route::post('/reports', [PortalDailyReportController::class, 'store'])->name('teacher.reports.store');
        Route::get('/reports/{dailyReport}/edit', [PortalDailyReportController::class, 'edit'])->name('teacher.reports.edit');
        Route::put('/reports/{dailyReport}', [PortalDailyReportController::class, 'update'])->name('teacher.reports.update');
        Route::get('/reports/{dailyReport}/attachment', [PortalDailyReportController::class, 'download'])->name('teacher.reports.attachment');
    });

    Route::prefix('admin')->middleware('permission:access-admin-panel')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::post('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::post('/change-password', [AdminProfileController::class, 'changePassword'])->name('admin.password.update');

        Route::middleware('permission:leads-index')->group(function () {
            Route::get('/leads/export', [AdminLeadController::class, 'export'])->name('admin.leads.export');
        });
        Route::middleware('permission:leads-create')->group(function () {
            Route::post('/leads', [AdminLeadController::class, 'store'])->name('admin.leads.store');
        });

        // Viewing + follow-up (notes, status) is open to the lighter
        // leads-follow-up permission too — e.g. a telecaller role. Editing
        // core details, converting, and deleting stay leads-edit/-delete only.
        Route::middleware('permission:leads-index|leads-follow-up')->group(function () {
            Route::get('/leads', [AdminLeadController::class, 'index'])->name('admin.leads.index');
        });
        Route::middleware('permission:leads-show|leads-follow-up')->group(function () {
            Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('admin.leads.show');
        });
        Route::middleware('permission:leads-edit|leads-follow-up')->group(function () {
            Route::post('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('admin.leads.status.update');
            Route::post('/leads/{lead}/notes', [AdminLeadController::class, 'addNote'])->name('admin.leads.notes.store');
        });

        Route::middleware('permission:leads-edit')->group(function () {
            Route::post('/leads/{lead}', [AdminLeadController::class, 'update'])->name('admin.leads.update');
            Route::post('/leads/{lead}/convert', [AdminLeadController::class, 'convert'])->name('admin.leads.convert');
        });
        Route::middleware('permission:leads-delete')->group(function () {
            Route::delete('/leads/{lead}', [AdminLeadController::class, 'destroy'])->name('admin.leads.destroy');
        });

        Route::middleware('permission:students-index')->group(function () {
            Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
            Route::get('/students/export', [AdminStudentController::class, 'export'])->name('admin.students.export');
        });
        Route::middleware('permission:students-create')->group(function () {
            Route::post('/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
            Route::post('/students/offline-enroll', [AdminStudentController::class, 'storeOffline'])->name('admin.students.offline-enroll');
        });
        Route::middleware('permission:students-show')->group(function () {
            Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('admin.students.show');
            Route::get('/students/{student}/id-card', [AdminStudentController::class, 'idCardView'])->name('admin.students.id-card.view');
            Route::get('/students/{student}/id-card/download', [AdminStudentController::class, 'idCardDownload'])->name('admin.students.id-card.download');
        });
        Route::middleware('permission:students-edit')->group(function () {
            Route::post('/students/{student}/allot-course', [AdminStudentController::class, 'allotCourse'])->name('admin.students.allot-course');
            Route::post('/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
            Route::post('/students/{student}/feature', [AdminStudentController::class, 'toggleFeature'])->name('admin.students.feature');
            Route::post('/enrollments/{enrollment}/payments', [AdminStudentController::class, 'storePayment'])->name('admin.enrollments.payments.store');
            Route::post('/enrollments/{enrollment}/fee', [AdminStudentController::class, 'updateFee'])->name('admin.enrollments.fee.update');
        });
        Route::middleware('permission:students-delete')->group(function () {
            Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');
        });

        Route::middleware('permission:courses-index')->group(function () {
            Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
            Route::get('/courses/{course}/videos', [AdminCourseVideoController::class, 'index'])->name('admin.courses.videos.index');
            Route::get('/courses/{course}/quiz', [AdminCourseQuizController::class, 'index'])->name('admin.courses.quiz.index');
            Route::get('/courses/{course}/assignments', [AdminCourseAssignmentController::class, 'index'])->name('admin.courses.assignments.index');
            Route::get('/courses/{course}/notes', [AdminCourseNoteController::class, 'index'])->name('admin.courses.notes.index');
        });
        Route::middleware('permission:courses-create')->group(function () {
            Route::post('/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
            Route::post('/courses/{course}/videos', [AdminCourseVideoController::class, 'store'])->name('admin.courses.videos.store');
            Route::post('/courses/{course}/quiz', [AdminCourseQuizController::class, 'store'])->name('admin.courses.quiz.store');
            Route::post('/courses/{course}/assignments', [AdminCourseAssignmentController::class, 'store'])->name('admin.courses.assignments.store');
            Route::post('/courses/{course}/notes', [AdminCourseNoteController::class, 'store'])->name('admin.courses.notes.store');
        });
        Route::middleware('permission:courses-edit')->group(function () {
            Route::post('/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
            Route::post('/courses/{course}/videos/{video}', [AdminCourseVideoController::class, 'update'])->name('admin.courses.videos.update');
            Route::post('/courses/{course}/quiz/{question}', [AdminCourseQuizController::class, 'update'])->name('admin.courses.quiz.update');
            Route::post('/courses/{course}/assignments/{assignment}', [AdminCourseAssignmentController::class, 'update'])->name('admin.courses.assignments.update');
            Route::post('/courses/{course}/submissions/{submission}/grade', [AdminCourseAssignmentController::class, 'grade'])->name('admin.courses.assignments.grade');
            Route::post('/courses/{course}/notes/{note}', [AdminCourseNoteController::class, 'update'])->name('admin.courses.notes.update');
        });
        Route::middleware('permission:courses-delete')->group(function () {
            Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');
            Route::delete('/courses/{course}/videos/{video}', [AdminCourseVideoController::class, 'destroy'])->name('admin.courses.videos.destroy');
            Route::delete('/courses/{course}/quiz/{question}', [AdminCourseQuizController::class, 'destroy'])->name('admin.courses.quiz.destroy');
            Route::delete('/courses/{course}/assignments/{assignment}', [AdminCourseAssignmentController::class, 'destroy'])->name('admin.courses.assignments.destroy');
            Route::delete('/courses/{course}/notes/{note}', [AdminCourseNoteController::class, 'destroy'])->name('admin.courses.notes.destroy');
        });

        Route::middleware('permission:categories-index')->group(function () {
            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        });
        Route::middleware('permission:categories-create')->group(function () {
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        });
        Route::middleware('permission:categories-delete')->group(function () {
            Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        });

        Route::middleware('permission:coupons-index')->group(function () {
            Route::get('/coupons', [AdminCouponController::class, 'index'])->name('admin.coupons.index');
        });
        Route::middleware('permission:coupons-create')->group(function () {
            Route::post('/coupons', [AdminCouponController::class, 'store'])->name('admin.coupons.store');
        });
        Route::middleware('permission:coupons-delete')->group(function () {
            Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('admin.coupons.destroy');
        });

        Route::middleware('permission:payments-index')->group(function () {
            Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
            Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('admin.payments.export');
        });
        Route::middleware('permission:payments-edit')->group(function () {
            Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('admin.payments.refund');
            Route::post('/payments/{payment}/verify-upi', [AdminPaymentController::class, 'verifyUpi'])->name('admin.payments.verify-upi');
            Route::post('/payments/{payment}/reject-upi', [AdminPaymentController::class, 'rejectUpi'])->name('admin.payments.reject-upi');
        });
        Route::middleware('permission:expenses-index')->group(function () {
            Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('admin.expenses.index');
            Route::get('/expenses/export', [AdminExpenseController::class, 'export'])->name('admin.expenses.export');
        });
        Route::middleware('permission:expenses-create')->group(function () {
            Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('admin.expenses.store');
        });
        Route::middleware('permission:expenses-show')->group(function () {
            Route::get('/expenses/{expense}/receipt', [AdminExpenseController::class, 'downloadReceipt'])->name('admin.expenses.receipt');
        });
        Route::middleware('permission:expenses-delete')->group(function () {
            Route::delete('/expenses/{expense}', [AdminExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
        });

        Route::middleware('permission:franchise-leads-index')->group(function () {
            Route::get('/franchise', [AdminFranchiseController::class, 'index'])->name('admin.franchise.index');
            Route::get('/franchise/export', [AdminFranchiseController::class, 'export'])->name('admin.franchise.export');
        });
        Route::middleware('permission:franchise-leads-show')->group(function () {
            Route::get('/franchise/documents/{document}/download', [AdminFranchiseController::class, 'downloadDocument'])->name('admin.franchise.documents.download');
        });
        Route::middleware('permission:franchise-leads-edit')->group(function () {
            Route::post('/franchise/leads/{lead}', [AdminFranchiseController::class, 'updateLead'])->name('admin.franchise.leads.update');
            Route::post('/franchise/bookings/{booking}', [AdminFranchiseController::class, 'updateBooking'])->name('admin.franchise.bookings.update');
            Route::post('/franchise/bookings/{booking}/documents', [AdminFranchiseController::class, 'uploadAgreement'])->name('admin.franchise.bookings.upload');
        });

        Route::middleware('permission:franchise-resources-index')->group(function () {
            Route::get('/franchise/resources', [AdminFranchiseResourceController::class, 'index'])->name('admin.franchise.resources.index');
        });
        Route::middleware('permission:franchise-resources-create')->group(function () {
            Route::post('/franchise/resources', [AdminFranchiseResourceController::class, 'store'])->name('admin.franchise.resources.store');
        });
        Route::middleware('permission:franchise-resources-delete')->group(function () {
            Route::delete('/franchise/resources/{resource}', [AdminFranchiseResourceController::class, 'destroy'])->name('admin.franchise.resources.destroy');
        });

        Route::middleware('permission:gallery-index')->group(function () {
            Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('admin.gallery.index');
        });
        Route::middleware('permission:gallery-create')->group(function () {
            Route::post('/gallery', [AdminGalleryController::class, 'store'])->name('admin.gallery.store');
        });
        Route::middleware('permission:gallery-edit')->group(function () {
            Route::post('/gallery/{gallery}/approve', [AdminGalleryController::class, 'approve'])->name('admin.gallery.approve');
            Route::post('/gallery/{gallery}/reject', [AdminGalleryController::class, 'reject'])->name('admin.gallery.reject');
        });
        Route::middleware('permission:gallery-delete')->group(function () {
            Route::delete('/gallery/{gallery}', [AdminGalleryController::class, 'destroy'])->name('admin.gallery.destroy');
        });

        Route::middleware('permission:reviews-index')->group(function () {
            Route::get('/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
        });
        Route::middleware('permission:reviews-edit')->group(function () {
            Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
            Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
            Route::post('/reviews/{review}/feature', [AdminReviewController::class, 'toggleFeature'])->name('admin.reviews.feature');
        });
        Route::middleware('permission:reviews-delete')->group(function () {
            Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
        });

        Route::middleware('permission:certificate-applications-index')->group(function () {
            Route::get('/certificate-applications', [AdminCertificateApplicationController::class, 'index'])->name('admin.certificate-applications.index');
        });
        Route::middleware('permission:certificate-applications-show')->group(function () {
            Route::get('/certificate-applications/{application}/proof', [AdminCertificateApplicationController::class, 'downloadProof'])->name('admin.certificate-applications.proof');
        });
        Route::middleware('permission:certificate-applications-edit')->group(function () {
            Route::post('/certificate-applications/{application}/approve', [AdminCertificateApplicationController::class, 'approve'])->name('admin.certificate-applications.approve');
            Route::post('/certificate-applications/{application}/reject', [AdminCertificateApplicationController::class, 'reject'])->name('admin.certificate-applications.reject');
        });
        Route::middleware('permission:certificates-index')->group(function () {
            Route::get('/certificates', [AdminCertificateApplicationController::class, 'certificatesIndex'])->name('admin.certificates.index');
        });
        Route::middleware('permission:certificates-show')->group(function () {
            Route::get('/certificates/{certificate}/download', [AdminCertificateApplicationController::class, 'download'])->name('admin.certificates.download');
            Route::get('/certificates/{certificate}/view', [AdminCertificateApplicationController::class, 'view'])->name('admin.certificates.view');
            Route::get('/certificates/{certificate}/marksheet', [AdminCertificateApplicationController::class, 'downloadMarksheet'])->name('admin.certificates.marksheet');
            Route::get('/certificates/{certificate}/marksheet/view', [AdminCertificateApplicationController::class, 'viewMarksheet'])->name('admin.certificates.marksheet.view');
        });
        Route::middleware('permission:certificates-create')->group(function () {
            Route::post('/certificate-applications/manual', [AdminCertificateApplicationController::class, 'storeManual'])->name('admin.certificate-applications.manual-store');
        });
        Route::middleware('permission:certificates-edit')->group(function () {
            Route::post('/certificates/{certificate}/documents', [AdminCertificateApplicationController::class, 'updateDocuments'])->name('admin.certificates.documents.update');
        });

        Route::middleware('permission:careers-index')->group(function () {
            Route::get('/careers', [AdminJobPostingController::class, 'index'])->name('admin.careers.index');
        });
        Route::middleware('permission:careers-create')->group(function () {
            Route::post('/careers', [AdminJobPostingController::class, 'store'])->name('admin.careers.store');
        });
        Route::middleware('permission:careers-edit')->group(function () {
            Route::post('/careers/{career}', [AdminJobPostingController::class, 'update'])->name('admin.careers.update');
            Route::post('/careers/{career}/toggle', [AdminJobPostingController::class, 'toggleStatus'])->name('admin.careers.toggle');
        });
        Route::middleware('permission:careers-delete')->group(function () {
            Route::delete('/careers/{career}', [AdminJobPostingController::class, 'destroy'])->name('admin.careers.destroy');
        });

        Route::middleware('permission:job-applications-index')->group(function () {
            Route::get('/job-applications', [AdminJobApplicationController::class, 'index'])->name('admin.job-applications.index');
        });
        Route::middleware('permission:job-applications-show')->group(function () {
            Route::get('/job-applications/{application}/resume', [AdminJobApplicationController::class, 'downloadResume'])->name('admin.job-applications.resume');
        });
        Route::middleware('permission:job-applications-edit')->group(function () {
            Route::post('/job-applications/{application}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('admin.job-applications.status');
        });
        Route::middleware('permission:job-applications-delete')->group(function () {
            Route::delete('/job-applications/{application}', [AdminJobApplicationController::class, 'destroy'])->name('admin.job-applications.destroy');
        });

        Route::middleware('permission:placements-index')->group(function () {
            Route::get('/placements', [AdminPlacementController::class, 'index'])->name('admin.placements.index');
        });
        Route::middleware('permission:placements-show')->group(function () {
            Route::get('/placements/{placement}/proof', [AdminPlacementController::class, 'downloadProof'])->name('admin.placements.proof');
        });
        Route::middleware('permission:placements-edit')->group(function () {
            Route::post('/placements/{placement}/approve', [AdminPlacementController::class, 'approve'])->name('admin.placements.approve');
            Route::post('/placements/{placement}/reject', [AdminPlacementController::class, 'reject'])->name('admin.placements.reject');
            Route::post('/placements/{placement}/update', [AdminPlacementController::class, 'update'])->name('admin.placements.update');
            Route::post('/placements/{placement}/feature', [AdminPlacementController::class, 'toggleFeatured'])->name('admin.placements.feature');
        });
        Route::middleware('permission:placements-delete')->group(function () {
            Route::delete('/placements/{placement}', [AdminPlacementController::class, 'destroy'])->name('admin.placements.destroy');
        });

        Route::middleware('permission:attendance-locations-index')->group(function () {
            Route::get('/attendance-locations', [AdminAttendanceLocationController::class, 'index'])->name('admin.attendance-locations.index');
        });
        Route::middleware('permission:attendance-locations-create')->group(function () {
            Route::post('/attendance-locations', [AdminAttendanceLocationController::class, 'store'])->name('admin.attendance-locations.store');
        });
        Route::middleware('permission:attendance-locations-edit')->group(function () {
            Route::post('/attendance-locations/{location}', [AdminAttendanceLocationController::class, 'update'])->name('admin.attendance-locations.update');
        });
        Route::middleware('permission:attendance-locations-delete')->group(function () {
            Route::delete('/attendance-locations/{location}', [AdminAttendanceLocationController::class, 'destroy'])->name('admin.attendance-locations.destroy');
        });

        Route::middleware('permission:attendance-index')->group(function () {
            Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
            Route::get('/attendance/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
        });

        Route::middleware('permission:daily-reports-index')->group(function () {
            Route::get('/daily-reports', [AdminDailyReportController::class, 'index'])->name('admin.daily-reports.index');
            Route::get('/daily-reports/export', [AdminDailyReportController::class, 'export'])->name('admin.daily-reports.export');
            Route::get('/daily-reports/performance/{member}', [AdminDailyReportController::class, 'performance'])->name('admin.daily-reports.performance');
        });
        Route::middleware('permission:daily-reports-edit')->group(function () {
            Route::post('/daily-reports/{dailyReport}/approve', [AdminDailyReportController::class, 'approve'])->name('admin.daily-reports.approve');
            Route::post('/daily-reports/{dailyReport}/reject', [AdminDailyReportController::class, 'reject'])->name('admin.daily-reports.reject');
        });

        Route::middleware('permission:staff-index')->group(function () {
            Route::get('/staff', [AdminStaffController::class, 'index'])->name('admin.staff.index');
        });
        Route::middleware('permission:staff-create')->group(function () {
            Route::post('/staff', [AdminStaffController::class, 'store'])->name('admin.staff.store');
        });
        Route::middleware('permission:staff-edit')->group(function () {
            Route::put('/staff/{member}', [AdminStaffController::class, 'update'])->name('admin.staff.update');
        });
        Route::middleware('permission:staff-delete')->group(function () {
            Route::delete('/staff/{member}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');
        });

        Route::middleware('permission:faqs-index')->group(function () {
            Route::get('/faqs', [AdminFaqController::class, 'index'])->name('admin.faqs.index');
        });
        Route::middleware('permission:faqs-create')->group(function () {
            Route::post('/faqs', [AdminFaqController::class, 'store'])->name('admin.faqs.store');
        });
        Route::middleware('permission:faqs-edit')->group(function () {
            Route::post('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('admin.faqs.update');
        });
        Route::middleware('permission:faqs-delete')->group(function () {
            Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('admin.faqs.destroy');
        });

        Route::middleware('permission:blog-index')->group(function () {
            Route::get('/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
        });
        Route::middleware('permission:blog-create')->group(function () {
            Route::post('/posts', [AdminPostController::class, 'store'])->name('admin.posts.store');
        });
        Route::middleware('permission:blog-edit')->group(function () {
            Route::post('/posts/{post}', [AdminPostController::class, 'update'])->name('admin.posts.update');
        });
        Route::middleware('permission:blog-delete')->group(function () {
            Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');
        });

        Route::middleware('permission:team-index')->group(function () {
            Route::get('/team', [AdminTeamController::class, 'index'])->name('admin.team.index');
        });
        Route::middleware('permission:team-create')->group(function () {
            Route::post('/team/roles', [AdminTeamController::class, 'storeRole'])->name('admin.team.roles.store');
            Route::post('/team/members', [AdminTeamController::class, 'storeMember'])->name('admin.team.members.store');
        });
        Route::middleware('permission:team-edit')->group(function () {
            Route::post('/team/roles/{role}', [AdminTeamController::class, 'updateRole'])->name('admin.team.roles.update');
            Route::post('/team/members/{member}', [AdminTeamController::class, 'updateMember'])->name('admin.team.members.update');
        });
        Route::middleware('permission:team-delete')->group(function () {
            Route::delete('/team/roles/{role}', [AdminTeamController::class, 'destroyRole'])->name('admin.team.roles.destroy');
            Route::delete('/team/members/{member}', [AdminTeamController::class, 'destroyMember'])->name('admin.team.members.destroy');
        });

        Route::middleware('permission:team-members-index')->group(function () {
            Route::get('/team-members', [AdminTeamMemberController::class, 'index'])->name('admin.team-members.index');
        });
        Route::middleware('permission:team-members-create')->group(function () {
            Route::post('/team-members', [AdminTeamMemberController::class, 'store'])->name('admin.team-members.store');
        });
        Route::middleware('permission:team-members-edit')->group(function () {
            Route::post('/team-members/{teamMember}', [AdminTeamMemberController::class, 'update'])->name('admin.team-members.update');
            Route::post('/team-members/{teamMember}/approve', [AdminTeamMemberController::class, 'approve'])->name('admin.team-members.approve');
            Route::post('/team-members/{teamMember}/reject', [AdminTeamMemberController::class, 'reject'])->name('admin.team-members.reject');
        });
        Route::middleware('permission:team-members-delete')->group(function () {
            Route::delete('/team-members/{teamMember}', [AdminTeamMemberController::class, 'destroy'])->name('admin.team-members.destroy');
        });
    });
});
