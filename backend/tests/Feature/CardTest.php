<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_cards_endpoints(): void
    {
        $this->getJson('/api/cards')->assertUnauthorized();
        $this->postJson('/api/cards', [])->assertUnauthorized();
    }

    public function test_can_list_paginated_cards(): void
    {
        Sanctum::actingAs($this->user);
        Card::factory()->count(15)->create();

        $response = $this->getJson('/api/cards');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'card_number', 'pin', 'activation_date', 'expiration_date', 'balance'],
                ],
                'meta' => ['current_page', 'last_page', 'total'],
            ]);
    }

    public function test_can_create_a_new_card_with_valid_data(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'card_number' => '12345678901234567890',
            'pin' => '1234',
            'activation_date' => now()->toDateTimeString(),
            'expiration_date' => now()->addYear()->toDateString(),
            'balance' => 150.00,
        ];

        $response = $this->postJson('/api/cards', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.card_number', '12345678901234567890');

        // Porównanie balance bez wymuszania ścisłego typu float w JSON
        $this->assertEquals(150.00, $response->json('data.balance'));

        $this->assertDatabaseHas('cards', [
            'card_number' => '12345678901234567890',
        ]);
    }

    public function test_cannot_create_card_with_invalid_card_number_or_pin_length(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'card_number' => '12345',
            'pin' => '12',
            'activation_date' => now()->toDateTimeString(),
            'expiration_date' => now()->addYear()->toDateString(),
            'balance' => 100,
        ];

        $response = $this->postJson('/api/cards', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['card_number', 'pin']);
    }

    public function test_expiration_date_must_be_after_or_equal_to_activation_date(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'card_number' => '12345678901234567890',
            'pin' => '1234',
            'activation_date' => now()->toDateTimeString(),
            'expiration_date' => now()->subDay()->toDateString(),
            'balance' => 100,
        ];

        $response = $this->postJson('/api/cards', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['expiration_date']);
    }

    public function test_can_show_a_specific_card(): void
    {
        Sanctum::actingAs($this->user);
        $card = Card::factory()->create();

        $response = $this->getJson("/api/cards/{$card->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $card->id);
    }

    public function test_can_update_a_card_keeping_its_own_card_number(): void
    {
        Sanctum::actingAs($this->user);

        $card = Card::factory()->create([
            'card_number' => '98765432109876543210',
            'balance' => 100.00,
        ]);

        $payload = [
            'card_number' => '98765432109876543210',
            'pin' => '9999',
            'activation_date' => $card->activation_date,
            'expiration_date' => $card->expiration_date,
            'balance' => 250.00,
        ];

        $response = $this->putJson("/api/cards/{$card->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.pin', '9999');

        $this->assertEquals(250.00, $response->json('data.balance'));
    }

    public function test_can_delete_a_card(): void
    {
        Sanctum::actingAs($this->user);
        $card = Card::factory()->create();

        $response = $this->deleteJson("/api/cards/{$card->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('cards', ['id' => $card->id]);
    }
}
