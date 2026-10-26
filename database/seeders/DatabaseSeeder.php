<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Models\Barangay;
use App\Models\CommunityZone;
use App\Models\FoundPetDetail;
use App\Models\FoundPropertyDetail;
use App\Models\LostPropertyDetail;
use App\Models\MissingPetDetail;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportStatusHistory;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedBarangays();
        $this->seedCategories();
        $this->seedUsers();
        $this->seedSystemSettings();
        $this->seedReports();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'Super Administrator', 'slug' => 'super_admin', 'description' => 'Full system access and configuration'],
            ['name' => 'Barangay Administrator', 'slug' => 'barangay_admin', 'description' => 'Community-level management and moderation'],
            ['name' => 'Registered Resident', 'slug' => 'resident', 'description' => 'Report, claim, and messaging access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }

    private function seedBarangays(): void
    {
        $barangays = [
            ['name' => 'Barangay San Isidro', 'code' => 'BRGY-001', 'description' => 'Central barangay area', 'latitude' => 14.5995, 'longitude' => 120.9842],
            ['name' => 'Barangay Santo Niño', 'code' => 'BRGY-002', 'description' => 'Eastern community zone', 'latitude' => 14.6010, 'longitude' => 120.9860],
            ['name' => 'Barangay Del Pilar', 'code' => 'BRGY-003', 'description' => 'Western residential area', 'latitude' => 14.5980, 'longitude' => 120.9820],
            ['name' => 'Barangay Rizal', 'code' => 'BRGY-004', 'description' => 'Northern community district', 'latitude' => 14.6025, 'longitude' => 120.9835],
            ['name' => 'Barangay Mabini', 'code' => 'BRGY-005', 'description' => 'Southern residential district', 'latitude' => 14.5960, 'longitude' => 120.9855],
        ];

        foreach ($barangays as $brgy) {
            $barangay = Barangay::firstOrCreate(['code' => $brgy['code']], $brgy);

            // Create community zones for each barangay
            $zones = ['Zone 1 - Residential', 'Zone 2 - Commercial', 'Zone 3 - Mixed Use'];
            foreach ($zones as $zoneName) {
                CommunityZone::firstOrCreate(
                    ['barangay_id' => $barangay->id, 'name' => $zoneName],
                    ['description' => "{$zoneName} area in {$barangay->name}"]
                );
            }
        }
    }

    private function seedCategories(): void
    {
        $categories = [
            // Lost/Found Property categories
            ['name' => 'Mobile Phone', 'slug' => 'mobile-phone', 'type' => 'lost_property', 'icon' => 'bi-phone', 'sort_order' => 1],
            ['name' => 'Wallet / Bag', 'slug' => 'wallet-bag', 'type' => 'lost_property', 'icon' => 'bi-wallet2', 'sort_order' => 2],
            ['name' => 'Identification Card', 'slug' => 'id-card', 'type' => 'lost_property', 'icon' => 'bi-person-badge', 'sort_order' => 3],
            ['name' => 'Keys', 'slug' => 'keys', 'type' => 'lost_property', 'icon' => 'bi-key', 'sort_order' => 4],
            ['name' => 'Jewelry', 'slug' => 'jewelry', 'type' => 'lost_property', 'icon' => 'bi-gem', 'sort_order' => 5],
            ['name' => 'Gadgets / Electronics', 'slug' => 'gadgets', 'type' => 'lost_property', 'icon' => 'bi-laptop', 'sort_order' => 6],
            ['name' => 'Documents', 'slug' => 'documents', 'type' => 'lost_property', 'icon' => 'bi-file-earmark-text', 'sort_order' => 7],
            ['name' => 'Clothing', 'slug' => 'clothing', 'type' => 'lost_property', 'icon' => 'bi-bag', 'sort_order' => 8],
            ['name' => 'School / Work Materials', 'slug' => 'school-work', 'type' => 'lost_property', 'icon' => 'bi-book', 'sort_order' => 9],
            ['name' => 'Other Personal Belongings', 'slug' => 'other-belongings', 'type' => 'lost_property', 'icon' => 'bi-box', 'sort_order' => 10],

            // Found Property (mirror lost categories)
            ['name' => 'Mobile Phone', 'slug' => 'found-mobile-phone', 'type' => 'found_property', 'icon' => 'bi-phone', 'sort_order' => 1],
            ['name' => 'Wallet / Bag', 'slug' => 'found-wallet-bag', 'type' => 'found_property', 'icon' => 'bi-wallet2', 'sort_order' => 2],
            ['name' => 'Identification Card', 'slug' => 'found-id-card', 'type' => 'found_property', 'icon' => 'bi-person-badge', 'sort_order' => 3],
            ['name' => 'Keys', 'slug' => 'found-keys', 'type' => 'found_property', 'icon' => 'bi-key', 'sort_order' => 4],
            ['name' => 'Jewelry', 'slug' => 'found-jewelry', 'type' => 'found_property', 'icon' => 'bi-gem', 'sort_order' => 5],
            ['name' => 'Gadgets / Electronics', 'slug' => 'found-gadgets', 'type' => 'found_property', 'icon' => 'bi-laptop', 'sort_order' => 6],
            ['name' => 'Documents', 'slug' => 'found-documents', 'type' => 'found_property', 'icon' => 'bi-file-earmark-text', 'sort_order' => 7],
            ['name' => 'Other Found Items', 'slug' => 'found-other', 'type' => 'found_property', 'icon' => 'bi-box', 'sort_order' => 8],

            // Missing Pet categories
            ['name' => 'Dog', 'slug' => 'missing-dog', 'type' => 'missing_pet', 'icon' => 'bi-heart', 'sort_order' => 1],
            ['name' => 'Cat', 'slug' => 'missing-cat', 'type' => 'missing_pet', 'icon' => 'bi-heart', 'sort_order' => 2],
            ['name' => 'Bird', 'slug' => 'missing-bird', 'type' => 'missing_pet', 'icon' => 'bi-heart', 'sort_order' => 3],
            ['name' => 'Other Animal', 'slug' => 'missing-other', 'type' => 'missing_pet', 'icon' => 'bi-heart', 'sort_order' => 4],

            // Found Pet categories
            ['name' => 'Dog', 'slug' => 'found-dog', 'type' => 'found_pet', 'icon' => 'bi-heart-fill', 'sort_order' => 1],
            ['name' => 'Cat', 'slug' => 'found-cat', 'type' => 'found_pet', 'icon' => 'bi-heart-fill', 'sort_order' => 2],
            ['name' => 'Bird', 'slug' => 'found-bird', 'type' => 'found_pet', 'icon' => 'bi-heart-fill', 'sort_order' => 3],
            ['name' => 'Other Animal', 'slug' => 'found-other-animal', 'type' => 'found_pet', 'icon' => 'bi-heart-fill', 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            ReportCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }

    private function seedUsers(): void
    {
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $barangayAdminRole = Role::where('slug', 'barangay_admin')->first();
        $residentRole = Role::where('slug', 'resident')->first();
        $barangay1 = Barangay::where('code', 'BRGY-001')->first();
        $barangay2 = Barangay::where('code', 'BRGY-002')->first();

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@community-recovery.test'],
            [
                'role_id' => $superAdminRole->id,
                'barangay_id' => null,
                'name' => 'System Administrator',
                'password' => 'password123',
                'phone' => '09171234567',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        UserProfile::firstOrCreate(['user_id' => $superAdmin->id], ['bio' => 'System super administrator']);

        // Barangay Admin 1
        $brgyAdmin1 = User::firstOrCreate(
            ['email' => 'admin.sanisidro@community-recovery.test'],
            [
                'role_id' => $barangayAdminRole->id,
                'barangay_id' => $barangay1->id,
                'name' => 'Maria Santos',
                'password' => 'password123',
                'phone' => '09181234567',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        UserProfile::firstOrCreate(['user_id' => $brgyAdmin1->id], ['bio' => 'Barangay San Isidro administrator']);

        // Barangay Admin 2
        $brgyAdmin2 = User::firstOrCreate(
            ['email' => 'admin.santonino@community-recovery.test'],
            [
                'role_id' => $barangayAdminRole->id,
                'barangay_id' => $barangay2->id,
                'name' => 'Jose Cruz',
                'password' => 'password123',
                'phone' => '09191234567',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        UserProfile::firstOrCreate(['user_id' => $brgyAdmin2->id], ['bio' => 'Barangay Santo Niño administrator']);

        // Resident Users
        $residents = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@community-recovery.test', 'barangay_id' => $barangay1->id],
            ['name' => 'Ana Reyes', 'email' => 'ana@community-recovery.test', 'barangay_id' => $barangay1->id],
            ['name' => 'Pedro Garcia', 'email' => 'pedro@community-recovery.test', 'barangay_id' => $barangay2->id],
            ['name' => 'Rosa Mendoza', 'email' => 'rosa@community-recovery.test', 'barangay_id' => $barangay2->id],
            ['name' => 'Carlo Villanueva', 'email' => 'carlo@community-recovery.test', 'barangay_id' => $barangay1->id],
        ];

        foreach ($residents as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $residentRole->id,
                    'barangay_id' => $data['barangay_id'],
                    'name' => $data['name'],
                    'password' => 'password123',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            UserProfile::firstOrCreate(['user_id' => $user->id]);
        }
    }

    private function seedSystemSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Community Recovery System', 'type' => 'string', 'group' => 'general', 'description' => 'Application name displayed in the header'],
            ['key' => 'match_threshold', 'value' => '40', 'type' => 'integer', 'group' => 'matching', 'description' => 'Minimum score (0-100) to suggest a possible match'],
            ['key' => 'auto_approve_reports', 'value' => '0', 'type' => 'boolean', 'group' => 'moderation', 'description' => 'Automatically approve new reports without admin review'],
            ['key' => 'max_images_per_report', 'value' => '5', 'type' => 'integer', 'group' => 'reports', 'description' => 'Maximum number of images allowed per report'],
            ['key' => 'max_image_size_kb', 'value' => '2048', 'type' => 'integer', 'group' => 'reports', 'description' => 'Maximum image file size in KB'],
            ['key' => 'public_tips_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'general', 'description' => 'Allow guests to submit public tips'],
            ['key' => 'email_notifications_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Send email notifications in addition to in-app'],
            ['key' => 'data_retention_days', 'value' => '365', 'type' => 'integer', 'group' => 'privacy', 'description' => 'Days to retain closed/archived reports before cleanup'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    private function seedReports(): void
    {
        $juan = User::where('email', 'juan@community-recovery.test')->first();
        $ana = User::where('email', 'ana@community-recovery.test')->first();
        $pedro = User::where('email', 'pedro@community-recovery.test')->first();
        $brgy1 = Barangay::where('code', 'BRGY-001')->first();
        $brgy2 = Barangay::where('code', 'BRGY-002')->first();

        $phoneCat = ReportCategory::where('slug', 'mobile-phone')->first();
        $foundPhoneCat = ReportCategory::where('slug', 'found-mobile-phone')->first();
        $walletCat = ReportCategory::where('slug', 'wallet-bag')->first();
        $missingDogCat = ReportCategory::where('slug', 'missing-dog')->first();
        $foundDogCat = ReportCategory::where('slug', 'found-dog')->first();

        if (Report::count() > 0) {
            return;
        }

        // 1. Lost iPhone (Juan - Active)
        $r1 = Report::create([
            'user_id' => $juan->id,
            'category_id' => $phoneCat->id,
            'barangay_id' => $brgy1->id,
            'type' => ReportType::LostProperty,
            'title' => 'Lost iPhone 15 Pro (Black Titanium)',
            'description' => 'Left on a bench near the basketball court at San Isidro Plaza. Has a transparent case with a green lanyard.',
            'incident_date' => now()->subDays(2)->format('Y-m-d'),
            'incident_time' => '16:30',
            'location_description' => 'San Isidro Plaza, concrete bench near the hoop',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'status' => ReportStatus::Active,
            'contact_preference' => 'in_app',
            'is_public' => true,
        ]);
        LostPropertyDetail::create([
            'report_id' => $r1->id,
            'brand' => 'Apple',
            'color' => 'Black Titanium',
            'model' => 'iPhone 15 Pro 256GB',
            'distinguishing_marks' => 'Small hairline scratch on bottom left corner, clear MagSafe case with green lanyard',
            'serial_number_hint' => 'Ends in ...9X42',
            'estimated_value' => 65000,
        ]);
        ReportStatusHistory::create([
            'report_id' => $r1->id,
            'old_status' => null,
            'new_status' => ReportStatus::Active->value,
            'changed_by' => $juan->id,
            'remarks' => 'Initial report submitted and published.',
        ]);

        // 2. Found iPhone (Ana - Active)
        $r2 = Report::create([
            'user_id' => $ana->id,
            'category_id' => $foundPhoneCat->id,
            'barangay_id' => $brgy1->id,
            'type' => ReportType::FoundProperty,
            'title' => 'Found iPhone with Clear Case near Plaza',
            'description' => 'Discovered a black smartphone on the bench in San Isidro Plaza. Battery was at 15%. Phone is locked.',
            'incident_date' => now()->subDays(2)->format('Y-m-d'),
            'incident_time' => '17:15',
            'location_description' => 'San Isidro Plaza benches',
            'latitude' => 14.5997,
            'longitude' => 120.9845,
            'status' => ReportStatus::Active,
            'contact_preference' => 'in_app',
            'is_public' => true,
        ]);
        FoundPropertyDetail::create([
            'report_id' => $r2->id,
            'brand' => 'Apple',
            'color' => 'Black',
            'model' => 'iPhone',
            'distinguishing_marks' => 'Has clear casing with a lanyard attached.',
            'custody_status' => 'with_finder',
            'storage_location_hint' => 'Kept securely in finder residence, San Isidro',
        ]);
        ReportStatusHistory::create([
            'report_id' => $r2->id,
            'old_status' => null,
            'new_status' => ReportStatus::Active->value,
            'changed_by' => $ana->id,
            'remarks' => 'Found item registered.',
        ]);

        // 3. Missing Pet: Bailey (Pedro - Active)
        $r3 = Report::create([
            'user_id' => $pedro->id,
            'category_id' => $missingDogCat->id,
            'barangay_id' => $brgy2->id,
            'type' => ReportType::MissingPet,
            'title' => 'Missing Golden Retriever - Bailey',
            'description' => 'Bailey slipped through our front gate when delivery arrived. Very friendly, responds eagerly to his name and treats.',
            'incident_date' => now()->subDay()->format('Y-m-d'),
            'incident_time' => '08:00',
            'location_description' => 'Santo Niño, near corner of Mabini St and Church',
            'latitude' => 14.6010,
            'longitude' => 120.9860,
            'status' => ReportStatus::Active,
            'contact_preference' => 'phone',
            'is_public' => true,
        ]);
        MissingPetDetail::create([
            'report_id' => $r3->id,
            'pet_name' => 'Bailey',
            'animal_type' => 'dog',
            'breed' => 'Golden Retriever',
            'color_markings' => 'Golden / Cream coat, bushy tail, slightly darker ears',
            'approximate_age' => '2 years old',
            'size' => 'large',
            'has_collar' => true,
            'collar_description' => 'Navy blue nylon collar with small bell',
            'microchip_id_hint' => 'Ends in ...5521',
            'medical_notes' => 'On sensitive-stomach diet, please do not feed poultry',
            'behavioral_notes' => 'Loves people, might jump gently to greet',
            'reward_offered' => true,
            'reward_description' => '₱5,000 cash reward upon safe return',
        ]);
        ReportStatusHistory::create([
            'report_id' => $r3->id,
            'old_status' => null,
            'new_status' => ReportStatus::Active->value,
            'changed_by' => $pedro->id,
            'remarks' => 'Pet report broadcast to community.',
        ]);

        // 4. Found Pet (Juan - Pending Review)
        $r4 = Report::create([
            'user_id' => $juan->id,
            'category_id' => $foundDogCat->id,
            'barangay_id' => $brgy2->id,
            'type' => ReportType::FoundPet,
            'title' => 'Found Friendly Golden Dog wandering near church',
            'description' => 'Found a friendly golden retriever wandering near Santo Niño parish church. Wearing a blue collar.',
            'incident_date' => now()->format('Y-m-d'),
            'incident_time' => '10:30',
            'location_description' => 'Outside Santo Niño Parish Church',
            'latitude' => 14.6015,
            'longitude' => 120.9865,
            'status' => ReportStatus::PendingReview,
            'contact_preference' => 'in_app',
            'is_public' => true,
        ]);
        FoundPetDetail::create([
            'report_id' => $r4->id,
            'animal_type' => 'dog',
            'breed_guess' => 'Golden Retriever',
            'color_markings' => 'Cream/golden, bushy tail',
            'approximate_age' => 'Young adult',
            'size' => 'large',
            'has_collar' => true,
            'collar_description' => 'Blue collar',
            'condition' => 'healthy',
            'immediate_needs' => 'Given fresh water and resting in garage area',
            'is_safely_sheltered' => true,
            'shelter_location_hint' => 'Sheltered temporarily in finder yard',
        ]);
        ReportStatusHistory::create([
            'report_id' => $r4->id,
            'old_status' => null,
            'new_status' => ReportStatus::PendingReview->value,
            'changed_by' => $juan->id,
            'remarks' => 'Submitted and awaiting barangay admin verification.',
        ]);

        // 5. Lost Wallet (Ana - Pending Review)
        $r5 = Report::create([
            'user_id' => $ana->id,
            'category_id' => $walletCat->id,
            'barangay_id' => $brgy1->id,
            'type' => ReportType::LostProperty,
            'title' => 'Lost Brown Leather Wallet with UMID & Driver License',
            'description' => 'Fell from bag while riding a tricycle from San Isidro market to Purok 2. Contains government IDs and cash.',
            'incident_date' => now()->format('Y-m-d'),
            'incident_time' => '13:00',
            'location_description' => 'Along San Isidro Main Avenue / Market area',
            'status' => ReportStatus::PendingReview,
            'contact_preference' => 'in_app',
            'is_public' => true,
        ]);
        LostPropertyDetail::create([
            'report_id' => $r5->id,
            'brand' => 'Fossil',
            'color' => 'Dark Brown',
            'model' => 'Bi-fold leather',
            'distinguishing_marks' => 'Contains Ana Reyes UMID ID card inside clear slot',
            'estimated_value' => 3500,
        ]);
        ReportStatusHistory::create([
            'report_id' => $r5->id,
            'old_status' => null,
            'new_status' => ReportStatus::PendingReview->value,
            'changed_by' => $ana->id,
            'remarks' => 'Submitted for review.',
        ]);
    }
}
