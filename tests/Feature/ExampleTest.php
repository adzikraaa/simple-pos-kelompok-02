<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/pos');

        $response->assertStatus(200);
    }

    public function test_transaction_is_rejected_when_quantity_exceeds_stock(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $category = new Category();
        $category->name = 'Tes';
        $category->save();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Stok Terbatas',
            'price' => 10000,
            'stock' => 2,
        ]);

        $response = $this->actingAs($user)->post(
            route('transactions.store'),
            [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'qty' => 3,
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors('items.0.qty');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('transaction_details', 0);
    }

    public function test_transaction_succeeds_when_stock_is_sufficient(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $category = new Category();
        $category->name = 'Tes';
        $category->save();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Stok Cukup',
            'price' => 10000,
            'stock' => 5,
        ]);

        $response = $this->actingAs($user)->post(
            route('transactions.store'),
            [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'qty' => 2,
                    ],
                ],
            ]
        );

        $response->assertRedirect(route('pos.create'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'total' => 20000,
        ]);

        $this->assertDatabaseHas('transaction_details', [
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 20000,
        ]);
    }
}
