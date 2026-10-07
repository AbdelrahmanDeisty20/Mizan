<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use App\Models\LegalCase;
use App\Models\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_fetch_their_shared_documents(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'عميل الاختبار',
            'phone'       => '01012345678',
            'access_code' => 'CLI-999888',
        ]);

        $legalCase = LegalCase::create([
            'office_id'   => $office->id,
            'client_id'   => $client->id,
            'client_role' => 'plaintiff',
            'case_number' => '2020',
            'year'        => 2026,
            'case_type'   => 'civil',
        ]);

        $document = Document::create([
            'office_id'     => $office->id,
            'client_id'     => $client->id,
            'legal_case_id' => $legalCase->id,
            'title'         => 'صورة رسمية من التوكيل العام',
            'file_path'     => 'documents/sample.pdf',
            'file_type'     => 'PDF',
            'file_size'     => '1.2 MB',
        ]);

        $response = $this->actingAs($client, 'sanctum')
            ->getJson('/api/client-documents');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    [
                        'id'        => $document->id,
                        'title'     => 'صورة رسمية من التوكيل العام',
                        'file_type' => 'PDF',
                        'file_size' => '1.2 MB',
                    ],
                ],
            ]);
    }

    public function test_client_can_upload_new_document(): void
    {
        Storage::fake('public');

        $office = Office::create([
            'office_name'       => 'مكتب القانون',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $client = Client::create([
            'office_id'   => $office->id,
            'name'        => 'عميل الاختبار',
            'phone'       => '01012345678',
            'access_code' => 'CLI-999888',
        ]);

        $legalCase = LegalCase::create([
            'office_id'   => $office->id,
            'client_id'   => $client->id,
            'client_role' => 'plaintiff',
            'case_number' => '2020',
            'year'        => 2026,
            'case_type'   => 'civil',
        ]);

        $file = UploadedFile::fake()->create('contract.pdf', 2048, 'application/pdf');

        $response = $this->actingAs($client, 'sanctum')
            ->postJson('/api/client-documents/upload', [
                'file'          => $file,
                'title'         => 'عقد الاتفاق المبرم بين الطرفين',
                'legal_case_id' => $legalCase->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'title'     => 'عقد الاتفاق المبرم بين الطرفين',
                    'file_type' => 'PDF',
                ],
            ]);

        $this->assertDatabaseHas('documents', [
            'client_id' => $client->id,
            'office_id' => $office->id,
            'title'     => 'عقد الاتفاق المبرم بين الطرفين',
        ]);
    }
}
