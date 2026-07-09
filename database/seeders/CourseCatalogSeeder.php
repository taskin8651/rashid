<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@rtechcomputer.in'],
            [
                'name' => 'R-Tech Admin',
                'password' => 'password',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Categories + Courses (matches index.html course cards)
        $catalog = [
            [
                'category' => 'Web Development',
                'icon' => '💻',
                'course' => [
                    'name' => 'Web Development',
                    'price' => 15000,
                    'duration_text' => '6 Months',
                    'description' => 'Learn HTML, CSS, JavaScript, Bootstrap, PHP, MySQL, React and WordPress from Beginner to Advanced with live project experience.',
                    'rating' => '4.9',
                    'rating_count' => 128,
                ],
                'modules' => ['HTML & CSS Fundamentals', 'JavaScript Essentials', 'Bootstrap 5', 'PHP & MySQL Backend', 'React Basics', 'Live Project'],
            ],
            [
                'category' => 'Graphic Design',
                'icon' => '🎨',
                'course' => [
                    'name' => 'Graphic Designing',
                    'price' => 15000,
                    'duration_text' => '4 Months',
                    'description' => 'Master professional design tools and create stunning visuals for brands, social media, print materials and digital campaigns.',
                    'rating' => '4.8',
                    'rating_count' => 96,
                ],
                'modules' => ['Photoshop Basics', 'Illustrator Vectors', 'CorelDRAW', 'Canva for Social Media', 'Logo & Branding'],
            ],
            [
                'category' => 'Digital Marketing',
                'icon' => '📈',
                'course' => [
                    'name' => 'Digital Marketing',
                    'price' => 15000,
                    'duration_text' => '4 Months',
                    'description' => 'Complete digital marketing mastery — SEO, paid ads, social media strategy, email campaigns and content marketing for real growth.',
                    'rating' => '4.9',
                    'rating_count' => 74,
                ],
                'modules' => ['SEO Fundamentals', 'Google Ads', 'Meta Ads Manager', 'Content & Email Marketing'],
            ],
            [
                'category' => 'Video Editing',
                'icon' => '🎬',
                'course' => [
                    'name' => 'Video Editing',
                    'price' => 15000,
                    'duration_text' => '3 Months',
                    'description' => 'Professional video editing with industry tools. Create cinematic videos, YouTube content, Reels, motion graphics and more.',
                    'rating' => '4.7',
                    'rating_count' => 52,
                ],
                'modules' => ['Premiere Pro Basics', 'Color Grading', 'After Effects Motion', 'YouTube & Reels Editing'],
            ],
        ];

        $webDevCourse = null;

        foreach ($catalog as $entry) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($entry['category'])],
                ['name' => $entry['category'], 'icon' => $entry['icon'], 'status' => 'active']
            );

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($entry['course']['name'])],
                array_merge($entry['course'], [
                    'category_id' => $category->id,
                    'status' => 'active',
                ])
            );

            foreach ($entry['modules'] as $i => $title) {
                CourseModule::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $title],
                    ['sort_order' => $i]
                );
            }

            $batchTemplates = [
                ['label' => 'Morning Batch', 'schedule_text' => 'Mon-Fri Morning (7:00-9:00 AM)', 'start_date' => '2026-07-14', 'mode' => 'online'],
                ['label' => 'Evening Batch', 'schedule_text' => 'Mon-Fri Evening (6:00-8:00 PM)', 'start_date' => '2026-07-14', 'mode' => 'online'],
                ['label' => 'Weekend Batch', 'schedule_text' => 'Weekend Only (Sat-Sun, 10 AM-1 PM)', 'start_date' => '2026-07-19', 'mode' => 'offline'],
            ];

            foreach ($batchTemplates as $b) {
                Batch::firstOrCreate(
                    ['course_id' => $course->id, 'label' => $b['label']],
                    array_merge($b, ['seats_total' => 30, 'seats_filled' => 0, 'status' => 'upcoming'])
                );
            }

            if ($entry['course']['name'] === 'Web Development') {
                $webDevCourse = $course;
            }
        }

        // Coupons (matches assets/js/main.js applyC() codes object)
        Coupon::firstOrCreate(['code' => 'RTECH10'], ['discount_amount' => 1500, 'valid_until' => '2026-12-31', 'usage_limit' => 200, 'used_count' => 84, 'status' => 'active']);
        Coupon::firstOrCreate(['code' => 'WELCOME'], ['discount_amount' => 2000, 'valid_until' => '2026-12-31', 'usage_limit' => 500, 'used_count' => 156, 'status' => 'active']);
        Coupon::firstOrCreate(['code' => 'SKILL500'], ['discount_amount' => 500, 'valid_until' => null, 'usage_limit' => null, 'used_count' => 312, 'status' => 'active']);

        // Demo student (matches student/dashboard.html "Rahul Kumar")
        $rahul = User::firstOrCreate(
            ['email' => 'rahul@example.com'],
            [
                'name' => 'Rahul Kumar',
                'phone' => '9999900001',
                'password' => 'password',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $rahul->assignRole('student');

        // A few more demo students (matches admin/index.html Students table)
        foreach ([
            ['name' => 'Sneha Patel', 'email' => 'sneha@example.com', 'phone' => '9999900002'],
            ['name' => 'Vikas Yadav', 'email' => 'vikas@example.com', 'phone' => '9999900003'],
            ['name' => 'Anjali Mehta', 'email' => 'anjali@example.com', 'phone' => '9999900004', 'is_active' => false],
        ] as $s) {
            $u = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'phone' => $s['phone'],
                    'password' => 'password',
                    'is_active' => $s['is_active'] ?? true,
                    'email_verified_at' => now(),
                ]
            );
            $u->assignRole('student');
        }

        // Demo enrollment + payment for Rahul (matches static payment history "rzp_pay_abc123")
        if ($webDevCourse) {
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $rahul->id, 'course_id' => $webDevCourse->id],
                [
                    'batch_id' => $webDevCourse->batches()->first()?->id,
                    'base_price' => 15000,
                    'discount_amount' => 0,
                    'final_amount' => 15000,
                    'status' => 'paid',
                    'enrolled_at' => '2026-01-15 10:00:00',
                ]
            );

            Payment::firstOrCreate(
                ['razorpay_payment_id' => 'rzp_pay_abc123'],
                [
                    'payable_type' => 'course_enrollment',
                    'payable_id' => $enrollment->id,
                    'user_id' => $rahul->id,
                    'razorpay_order_id' => 'order_demo_abc123',
                    'amount' => 15000,
                    'currency' => 'INR',
                    'method' => 'UPI',
                    'status' => 'paid',
                ]
            );
        }
    }
}
