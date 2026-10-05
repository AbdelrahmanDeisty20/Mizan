<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_chatbot_suggestions(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب العدل',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/chatbot/suggestions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    public function test_can_send_chat_prompt_and_receive_response(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب العدل',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/chatbot/chat', [
                'prompt' => 'ما هي الجلسات المسجلة اليوم؟',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'reply',
                    'referenced_cases',
                ],
            ]);

        $this->assertDatabaseHas('chatbot_messages', [
            'user_id' => $user->id,
            'prompt'  => 'ما هي الجلسات المسجلة اليوم؟',
        ]);
    }

    public function test_can_get_and_clear_chat_history(): void
    {
        $office = Office::create([
            'office_name'       => 'مكتب العدل',
            'syndicate_card_id' => '1234567890',
            'office_address'    => 'القاهرة',
            'office_phone'      => '01000000000',
        ]);

        $user = User::factory()->create([
            'role'      => 'lawyer',
            'office_id' => $office->id,
        ]);

        // Send a chat
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/chatbot/chat', [
                'prompt' => 'كم عدد القضايا النشطة بالمكتب؟',
            ]);

        // Get history
        $historyRes = $this->actingAs($user, 'sanctum')
            ->getJson('/api/chatbot/history');

        $historyRes->assertStatus(200);

        // Clear history
        $clearRes = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/chatbot/history');

        $clearRes->assertStatus(200);

        $this->assertDatabaseMissing('chatbot_messages', [
            'user_id' => $user->id,
        ]);
    }
}
