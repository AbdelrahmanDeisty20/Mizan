<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Consultation;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_consultation_request_matching_ui(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $lawyer = User::factory()->create([
            'name'      => 'أحمد محمود المحامي',
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'أحمد محمود',
            'national_id' => '29901011234567',
            'phone'       => '01011112222',
            'access_code' => 'ACC123456',
        ]);

        $response = $this->actingAs($lawyer, 'sanctum')
            ->postJson('/api/consultations', [
                'client_id'           => $client->id,
                'office_id'           => $office->id,
                'user_id'             => $lawyer->id,
                'consultation_method' => 'بمقر المكتب',
                'preferred_date'      => '2026-10-05',
                'preferred_time'      => '06:00 PM',
                'subject'             => 'جلسة عمل لمناقشة وقائع الدعوى',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'consultation_number',
                    'consultation_method',
                    'preferred_date',
                    'preferred_time',
                    'subject',
                    'status',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('consultations', [
            'consultation_method' => 'بمقر المكتب',
            'preferred_time'      => '06:00 PM',
            'subject'             => 'جلسة عمل لمناقشة وقائع الدعوى',
        ]);

        $consultation = Consultation::first();
        $this->assertNotNull($consultation->consultation_number);
        $this->assertStringStartsWith('CNS-', $consultation->consultation_number);
    }

    public function test_can_reply_to_consultation(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $lawyer = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'سامح علي',
            'national_id' => '29901011234568',
            'phone'       => '01033334444',
            'access_code' => 'ACC123457',
        ]);

        $consultation = Consultation::create([
            'client_id'           => $client->id,
            'office_id'           => $office->id,
            'user_id'             => $lawyer->id,
            'consultation_method' => 'مكالمة هاتفية',
            'preferred_date'      => '2026-10-06',
            'preferred_time'      => '04:00 PM',
            'subject'             => 'استفسار عن الأوراق المطلوبة',
            'status'              => 'pending',
        ]);

        $response = $this->actingAs($lawyer, 'sanctum')
            ->postJson("/api/consultations/{$consultation->id}/reply", [
                'reply'  => 'تم تأكيد الموعد للمكالمة الهاتفية.',
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('consultations', [
            'id'     => $consultation->id,
            'status' => 'confirmed',
            'reply'  => 'تم تأكيد الموعد للمكالمة الهاتفية.',
        ]);
    }

    public function test_can_list_and_delete_consultations(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $lawyer = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'سامح علي',
            'national_id' => '29901011234568',
            'phone'       => '01033334444',
            'access_code' => 'ACC123458',
        ]);

        $consultation = Consultation::create([
            'client_id'           => $client->id,
            'office_id'           => $office->id,
            'consultation_method' => 'اجتماع أونلاين',
            'preferred_date'      => '2026-10-07',
            'preferred_time'      => '08:00 PM',
            'subject'             => 'موضوع للاختبار',
        ]);

        $listRes = $this->actingAs($lawyer, 'sanctum')
            ->getJson('/api/consultations');
        $listRes->assertStatus(200);

        $updateRes = $this->actingAs($lawyer, 'sanctum')
            ->putJson("/api/consultations/{$consultation->id}", [
                'subject' => 'موضوع معدل للاختبار',
            ]);
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('consultations', [
            'id'      => $consultation->id,
            'subject' => 'موضوع معدل للاختبار',
        ]);

        $delRes = $this->actingAs($lawyer, 'sanctum')
            ->deleteJson("/api/consultations/{$consultation->id}");
        $delRes->assertStatus(200);

        $this->assertDatabaseMissing('consultations', [
            'id' => $consultation->id,
        ]);
    }

    public function test_client_can_create_consultation_without_acceptance_restriction(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'عميل تجربة',
            'national_id' => '29901011234599',
            'phone'       => '01055556666',
            'access_code' => 'ACC123499',
        ]);

        $response = $this->actingAs($client, 'sanctum')
            ->postJson('/api/consultations', [
                'consultation_method' => 'بمقر المكتب',
                'preferred_date'      => '2026-10-10',
                'preferred_time'      => '04:00 PM',
                'subject'             => 'استشارة خاصة بالعميل',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'subject' => 'استشارة خاصة بالعميل',
                ],
            ]);

        $this->assertDatabaseHas('consultations', [
            'client_id' => $client->id,
            'office_id' => $office->id,
            'subject'   => 'استشارة خاصة بالعميل',
        ]);
    }
}

