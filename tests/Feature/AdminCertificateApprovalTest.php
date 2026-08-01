<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\CertificateApplicationController;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminCertificateApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_certificate_manually_for_non_registered_student(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create();

        $category = Category::create([
            'name' => 'Manual Category',
            'slug' => 'manual-category',
            'status' => 'active',
        ]);

        $course = Course::create([
            'category_id' => $category->id,
            'name' => 'Manual Create Course',
            'slug' => 'manual-create-course',
            'price' => 1500,
            'duration_text' => '4 Months',
            'status' => 'active',
        ]);

        $request = Request::create('/admin/certificate-applications/manual', 'POST', [
            'student_name' => 'Manual Guest',
            'student_email' => 'manual-guest@example.com',
            'student_phone' => '1234567890',
            'course_id' => $course->id,
            'course_name' => 'Manual Create Course',
            'course_duration_text' => '4 Months',
            'roll_no' => 'RTC-100',
            'father_name' => 'Father',
            'batch_name' => 'Evening',
            'subjects' => [
                ['subject' => 'Theory', 'max_marks' => 100, 'marks_obtained' => 88],
            ],
        ]);
        $request->setUserResolver(fn () => $admin);

        $controller = app(CertificateApplicationController::class);
        $response = $controller->storeManual($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/admin/certificate-applications', $response->headers->get('Location'));

        $certificate = CertificateApplication::latest()->first()->certificate;
        $this->assertNotNull($certificate);
        $this->assertSame('Manual Guest', $certificate->student_name);
        $this->assertSame('manual-guest@example.com', $certificate->student_email);
        $this->assertSame('issued', $certificate->status);
        $this->assertCount(0, $certificate->subjects);
    }

    public function test_manual_certificate_creation_does_not_create_marksheet_subjects_immediately(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create();

        $category = Category::create([
            'name' => 'No Marksheet Category',
            'slug' => 'no-marksheet-category',
            'status' => 'active',
        ]);

        $course = Course::create([
            'category_id' => $category->id,
            'name' => 'No Marksheet Course',
            'slug' => 'no-marksheet-course',
            'price' => 1500,
            'duration_text' => '4 Months',
            'status' => 'active',
        ]);

        $request = Request::create('/admin/certificate-applications/manual', 'POST', [
            'student_name' => 'Manual Guest No Marksheet',
            'student_email' => 'manual-no-marksheet@example.com',
            'student_phone' => '1234567890',
            'course_id' => $course->id,
            'course_name' => 'No Marksheet Course',
            'course_duration_text' => '4 Months',
            'roll_no' => 'RTC-200',
            'father_name' => 'Father',
            'batch_name' => 'Evening',
            'subjects' => [
                ['subject' => 'Theory', 'max_marks' => 100, 'marks_obtained' => 88],
            ],
        ]);
        $request->setUserResolver(fn () => $admin);

        $controller = app(CertificateApplicationController::class);
        $response = $controller->storeManual($request);

        $this->assertEquals(302, $response->getStatusCode());

        $certificate = CertificateApplication::latest()->first()->certificate;
        $this->assertNotNull($certificate);
        $this->assertCount(0, $certificate->subjects);
    }

    public function test_certificate_pdf_view_embeds_signature_image_data_uri(): void
    {
        $admin = User::factory()->create();
        $category = Category::create([
            'name' => 'Signature Category',
            'slug' => 'signature-category',
            'status' => 'active',
        ]);
        $course = Course::create([
            'category_id' => $category->id,
            'name' => 'Signature Course',
            'slug' => 'signature-course',
            'price' => 1000,
            'duration_text' => '2 Months',
            'status' => 'active',
        ]);
        $certificate = Certificate::create([
            'user_id' => $admin->id,
            'course_id' => $course->id,
            'student_name' => 'Signature Student',
            'student_email' => 'signature@example.com',
            'course_name' => 'Signature Course',
            'course_duration_text' => '2 Months',
            'cert_code' => Certificate::generateCode(),
            'status' => 'issued',
            'issued_date' => now(),
            'source' => 'manual',
        ]);

        $html = view('certificates.pdf', [
            'certificate' => $certificate,
            'qrDataUri' => 'data:image/png;base64,abc123',
            'signatureImageDataUri' => 'data:image/png;base64,signature-abc',
        ])->render();

        $this->assertStringContainsString('data:image/png;base64,signature-abc', $html);
    }

    public function test_admin_can_download_issued_certificate(): void
    {
        $admin = User::factory()->create();
        $category = Category::create([
            'name' => 'Download Category',
            'slug' => 'download-category',
            'status' => 'active',
        ]);
        $course = Course::create([
            'category_id' => $category->id,
            'name' => 'Download Course',
            'slug' => 'download-course',
            'price' => 1000,
            'duration_text' => '2 Months',
            'status' => 'active',
        ]);
        $certificate = Certificate::create([
            'user_id' => $admin->id,
            'course_id' => $course->id,
            'student_name' => 'Download Student',
            'student_email' => 'download@example.com',
            'course_name' => 'Download Course',
            'course_duration_text' => '2 Months',
            'cert_code' => Certificate::generateCode(),
            'status' => 'issued',
            'issued_date' => now(),
            'source' => 'manual',
        ]);

        $controller = app(CertificateApplicationController::class);
        $response = $controller->download($certificate);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_admin_can_issue_certificate_with_manual_student_details(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create();
        $applicant = User::factory()->create([
            'name' => 'Guest Student',
            'email' => 'guest@example.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'status' => 'active',
        ]);

        $course = Course::create([
            'category_id' => $category->id,
            'name' => 'Manual Course',
            'slug' => 'manual-course',
            'price' => 1200,
            'duration_text' => '3 Months',
            'status' => 'active',
        ]);

        $application = CertificateApplication::create([
            'user_id' => $applicant->id,
            'course_id' => $course->id,
            'completion_date' => now()->subDays(5),
            'status' => 'pending',
        ]);

        $request = Request::create('/admin/certificate-applications/' . $application->id . '/approve', 'POST', [
            'student_name' => 'Manual Student',
            'student_email' => 'manual@example.com',
            'student_phone' => '9876543210',
            'course_name' => 'Manual Course',
            'course_duration_text' => '3 Months',
            'roll_no' => 'RTC-001',
            'father_name' => 'Father Name',
            'batch_name' => 'Morning Batch',
            'subjects' => [
                ['subject' => 'Math', 'max_marks' => 100, 'marks_obtained' => 90],
            ],
        ]);
        $request->setUserResolver(fn () => $admin);

        $controller = app(CertificateApplicationController::class);
        $response = $controller->approve($request, $application);

        $this->assertEquals(302, $response->getStatusCode());

        $certificate = $application->fresh()->certificate;
        $this->assertNotNull($certificate);
        $this->assertSame('Manual Student', $certificate->student_name);
        $this->assertSame('manual@example.com', $certificate->student_email);
        $this->assertSame('9876543210', $certificate->student_phone);
        $this->assertSame('Manual Course', $certificate->course_name);
        $this->assertSame('3 Months', $certificate->course_duration_text);
        $this->assertSame('issued', $certificate->status);
        $this->assertCount(1, $certificate->subjects);
    }
}
