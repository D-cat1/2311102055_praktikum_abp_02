<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('product edit page requires authentication', function () {
    $product = Product::factory()->create();
    
    $this->get(route('products.edit', $product))
        ->assertRedirect(route('login'));
});

test('product edit page renders with correct product data', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Legacy Product',
    ]);

    $this->actingAs($user)
        ->get(route('products.edit', $product))
        ->assertOk()
        ->assertSee('Edit Produk')
        ->assertSee('Legacy Product');
});

test('product can be updated without changing image', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Old Name',
        'price' => 10000,
    ]);

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductEdit::class, ['product' => $product])
        ->set('name', 'New Name')
        ->set('price', 20000)
        ->call('save')
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New Name',
        'price' => 20000,
    ]);
});

test('product image can be replaced', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    $oldImage = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
    $product = Product::factory()->create([
        'image' => $oldImage,
    ]);
    
    Storage::disk('public')->assertExists($oldImage);

    $newImage = UploadedFile::fake()->image('new.jpg');

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductEdit::class, ['product' => $product])
        ->set('name', 'Name with new image')
        ->set('newImage', $newImage)
        ->call('save')
        ->assertRedirect(route('products.index'));

    Storage::disk('public')->assertMissing($oldImage);
    
    $updatedProduct = Product::find($product->id);
    Storage::disk('public')->assertExists($updatedProduct->image);
});

test('product image can be removed', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    $oldImage = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
    $product = Product::factory()->create([
        'image' => $oldImage,
    ]);

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductEdit::class, ['product' => $product])
        ->call('removeExistingImage')
        ->call('save')
        ->assertRedirect(route('products.index'));

    Storage::disk('public')->assertMissing($oldImage);
    
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'image' => null,
    ]);
});
