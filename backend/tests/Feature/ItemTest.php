<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_akses_tanpa_token_kembali_401(): void
    {
        $response = $this->getJson('/api/items');
        $response->assertStatus(401);
    }

    public function test_bisa_tambah_barang_dengan_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/items', [
            'name' => 'Laptop Asus',
            'sku' => 'LTP-001',
            'quantity' => 10,
            'price' => 15000000,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['sku' => 'LTP-001']);
    }
}