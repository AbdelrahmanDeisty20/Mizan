<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_resource_contains_is_accepted_field(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => now(),
            'is_accepted'       => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'is_accepted' => true,
                ],
            ]);
    }

    public function test_unaccepted_user_is_blocked_by_middleware(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $unacceptedUser = User::factory()->create([
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => null,
            'is_accepted'       => false,
        ]);

        $response = $this->actingAs($unacceptedUser, 'sanctum')
            ->postJson('/api/clients', [
                'name'  => 'عميل جديد',
                'phone' => '01099998888',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => false,
            ]);
    }

    public function test_accepted_user_can_access_protected_routes(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $acceptedUser = User::factory()->create([
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => now(),
            'is_accepted'       => true,
        ]);

        $response = $this->actingAs($acceptedUser, 'sanctum')
            ->getJson('/api/clients');

        $response->assertStatus(200);
    }

    public function test_unaccepted_user_can_update_profile_and_phone(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $unacceptedUser = User::factory()->create([
            'phone'             => '01011111111',
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => null,
            'is_accepted'       => false,
        ]);

        $response = $this->actingAs($unacceptedUser, 'sanctum')
            ->postJson('/api/profile/update', [
                'phone' => '01022222222',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'phone' => '01022222222',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id'    => $unacceptedUser->id,
            'phone' => '01022222222',
        ]);
    }

    public function test_can_get_profile_by_id(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب الاختيار',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $targetUser = User::factory()->create([
            'name'              => 'أحمد محمود',
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => now(),
            'is_accepted'       => true,
        ]);

        $authUser = User::factory()->create([
            'role'              => 'lawyer',
            'office_id'         => $office->id,
            'email_verified_at' => now(),
            'is_accepted'       => true,
        ]);

        $response = $this->actingAs($authUser, 'sanctum')
            ->getJson("/api/profile/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data'   => [
                    'id'   => $targetUser->id,
                    'name' => 'أحمد محمود',
                ],
            ]);
    }
}
