<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('product create page requires authentication', function () {
    $this->get(route('products.create'))
        ->assertRedirect(route('login'));
});

test('product create page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('products.create'))
        ->assertOk()
        ->assertSee('Tambah Produk');
});

test('product can be created with image upload', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductCreate::class)
        ->set('name', 'Test Product')
        ->set('description', 'A test description')
        ->set('price', 50000)
        ->set('stock', 10)
        ->set('image', UploadedFile::fake()->image('product.jpg'))
        ->call('save')
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'price' => 50000,
        'stock' => 10,
    ]);

    $product = Product::where('name', 'Test Product')->first();
    Storage::disk('public')->assertExists($product->image);
});

test('product can be created without image', function () {
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductCreate::class)
        ->set('name', 'No Image Product')
        ->set('price', 25000)
        ->set('stock', 5)
        ->call('save')
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'No Image Product',
        'image' => null,
    ]);
});

test('product name is required', function () {
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductCreate::class)
        ->set('name', '')
        ->set('price', 50000)
        ->set('stock', 10)
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});
