<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Court;
use App\Models\Governorate;
use App\Models\Hearing;
use App\Models\HearingType;
use App\Models\LegalCase;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_fetch_their_cases_and_case_details(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب المحاماة',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $governorate = Governorate::create([
            'name_ar' => 'القاهرة',
            'name_en' => 'Cairo',
        ]);

        $court = Court::create([
            'name' => 'محكمة استئناف القاهرة',
        ]);

        $client = Client::create([
            'office_id'      => $office->id,
            'name'           => 'علي حسن',
            'phone'          => '01012345678',
            'access_code'    => 'CLI-100200',
            'governorate_id' => $governorate->id,
        ]);

        $legalCase = LegalCase::create([
            'office_id'   => $office->id,
            'client_id'   => $client->id,
            'client_role' => 'plaintiff',
            'court_id'    => $court->id,
            'case_number' => '554433',
            'year'        => 2026,
            'degree'      => 'appeal',
            'case_type'   => 'civil',
            'status'      => 'نشطة',
        ]);

        $hearingType = HearingType::create([
            'name' => 'جلسة مرافعات',
        ]);

        $hearing = Hearing::create([
            'office_id'       => $office->id,
            'legal_case_id'   => $legalCase->id,
            'hearing_date'    => '2026-11-01',
            'hearing_time'    => '10:00',
            'hearing_type_id' => $hearingType->id,
            'court_room'      => 'قاعة 3',
            'status'          => 'مقبلة',
        ]);

        // Test listing cases
        $response = $this->actingAs($client, 'sanctum')
            ->getJson('/api/client-cases');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    [
                        'id'          => $legalCase->id,
                        'case_number' => '554433',
                    ],
                ],
            ]);

        // Test fetching single case detail
        $showResponse = $this->actingAs($client, 'sanctum')
            ->getJson("/api/client-cases/{$legalCase->id}");

        $showResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'id'          => $legalCase->id,
                    'case_number' => '554433',
                ],
            ]);
    }

    public function test_client_can_fetch_fees_summary(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب المحاماة',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'علي حسن',
            'phone'       => '01012345678',
            'access_code' => 'CLI-100200',
        ]);

        $legalCase = LegalCase::create([
            'office_id'      => $office->id,
            'client_id'      => $client->id,
            'client_role'    => 'plaintiff',
            'case_number'    => '101',
            'year'           => 2026,
            'case_type'      => 'civil',
            'total_fees'     => 100000,
            'paid_fees'      => 65000,
            'remaining_fees' => 35000,
        ]);

        $receipt = \App\Models\FinancialReceipt::create([
            'office_id'      => $office->id,
            'client_id'      => $client->id,
            'legal_case_id'  => $legalCase->id,
            'receipt_number' => 'RCP-001',
            'amount'         => 65000,
            'payment_method' => 'نقدي',
            'date'           => '2026-10-07',
        ]);

        $response = $this->actingAs($client, 'sanctum')
            ->getJson('/api/client-fees-summary');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'total_agreed_fees'    => 100000,
                    'total_paid_fees'      => 65000,
                    'total_remaining_fees' => 35000,
                    'receipts'             => [
                        [
                            'id'             => $receipt->id,
                            'receipt_number' => 'RCP-001',
                            'amount'         => 65000,
                        ],
                    ],
                ],
            ]);

        $receiptsResponse = $this->actingAs($client, 'sanctum')
            ->getJson('/api/client-financial-receipts');

        $receiptsResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    [
                        'id'             => $receipt->id,
                        'receipt_number' => 'RCP-001',
                        'amount'         => 65000,
                    ],
                ],
            ]);
    }
}


