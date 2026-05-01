<?php

use App\Models\Product;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('product index page requires authentication', function () {
    $this->get(route('products.index'))
        ->assertRedirect(route('login'));
});

test('product index page displays products', function () {
    $user = User::factory()->create();
    $products = Product::factory(3)->create();

    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertOk()
        ->assertSee($products->first()->name);
});

test('product index page can search products', function () {
    $user = User::factory()->create();
    Product::factory()->create(['name' => 'Nasi Goreng Special']);
    Product::factory()->create(['name' => 'Mie Ayam Bakso']);

    $this->actingAs($user)
        ->get(route('products.index', ['search' => 'Nasi Goreng']))
        ->assertOk()
        ->assertSee('Nasi Goreng Special')
        ->assertDontSee('Mie Ayam Bakso');
});
