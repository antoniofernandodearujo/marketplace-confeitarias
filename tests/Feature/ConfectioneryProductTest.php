<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Confectionery;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfectioneryProductTest extends TestCase
{
    use RefreshDatabase;

    private function createConfectionery($overrides = [])
    {
        $address = Address::factory()->create();
        
        return Confectionery::create(array_merge([
            'name' => 'Confeitaria Teste',
            'latitude' => -23.550520,
            'longitude' => -46.633308,
            'phone' => '(11) 99999-9999',
            'address_id' => $address->id
        ], $overrides));
    }

    public function test_product_cannot_be_created_without_confectionery()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Bolo de Chocolate',
            'price' => 50.00,
            'description' => 'Delicioso bolo de chocolate',
            'confectionery_id' => 999 // ID inexistente
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('products', ['name' => 'Bolo de Chocolate']);
    }

    public function test_confectionery_can_have_multiple_products()
    {
        $confectionery = $this->createConfectionery();

        $response1 = $this->postJson('/api/products', [
            'name' => 'Bolo de Chocolate',
            'price' => 50.00,
            'description' => 'Delicioso bolo de chocolate',
            'confectionery_id' => $confectionery->id
        ]);

        $response2 = $this->postJson('/api/products', [
            'name' => 'Pudim',
            'price' => 25.00,
            'description' => 'Pudim cremoso',
            'confectionery_id' => $confectionery->id
        ]);

        $response1->assertStatus(201);
        $response2->assertStatus(201);
        
        $this->assertEquals(2, $confectionery->products()->count());
    }

    public function test_required_fields_validation()
    {
        $confectionery = $this->createConfectionery();

        $response = $this->postJson('/api/products', [
            'description' => 'Descrição sem os campos obrigatórios',
            'confectionery_id' => $confectionery->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_confectionery_coordinates_validation()
    {
        $address = Address::factory()->create();

        $response = $this->postJson('/api/confectioneries', [
            'name' => 'Confeitaria com Coordenadas Inválidas',
            'phone' => '(11) 99999-9999',
            'latitude' => 91, // Inválido: maior que 90
            'longitude' => 181, // Inválido: maior que 180
            'address' => [
                'cep' => '12345678',
                'street' => 'Rua Teste',
                'number' => '123',
                'neighborhood' => 'Bairro Teste',
                'city' => 'Cidade Teste',
                'state' => 'SP'
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_deleting_confectionery_removes_associated_products()
    {
        $confectionery = $this->createConfectionery();

        $product = Product::create([
            'name' => 'Bolo para Deletar',
            'price' => 45.00,
            'description' => 'Este produto deve ser deletado',
            'confectionery_id' => $confectionery->id
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $response = $this->deleteJson("/api/confectioneries/{$confectionery->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('confectioneries', ['id' => $confectionery->id]);
    }
}