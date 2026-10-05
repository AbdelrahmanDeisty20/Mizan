<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Governorate;
use App\Models\Office;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_request_generates_auto_case_number_when_empty(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $governorate = Governorate::create([
            'name'    => 'القاهرة',
            'name_ar' => 'القاهرة',
            'name_en' => 'Cairo',
        ]);

        $court = Court::create([
            'name'           => 'محكمة القاهرة',
            'governorate_id' => $governorate->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/service-requests', [
                'title'          => 'طلب إنابة جديد',
                'description'    => 'وصف تفصيلي للطلب',
                'governorate_id' => $governorate->id,
                'court_id'       => $court->id,
                'due_date'       => '2026-12-01',
                'offered_fee'    => 150,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('service_requests', [
            'title' => 'طلب إنابة جديد',
        ]);

        $requestModel = ServiceRequest::first();
        $this->assertNotNull($requestModel->case_number);
        $this->assertStringStartsWith('SR-', $requestModel->case_number);
    }

    public function test_service_request_keeps_provided_case_number(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $governorate = Governorate::create([
            'name'    => 'القاهرة',
            'name_ar' => 'القاهرة',
            'name_en' => 'Cairo',
        ]);

        $court = Court::create([
            'name'           => 'محكمة القاهرة',
            'governorate_id' => $governorate->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/service-requests', [
                'title'          => 'طلب إنابة مع رقم قضية',
                'description'    => 'وصف تفصيلي للطلب',
                'governorate_id' => $governorate->id,
                'court_id'       => $court->id,
                'case_number'    => '12345/2026',
                'due_date'       => '2026-12-01',
                'offered_fee'    => 200,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('service_requests', [
            'case_number' => '12345/2026',
        ]);
    }
}
