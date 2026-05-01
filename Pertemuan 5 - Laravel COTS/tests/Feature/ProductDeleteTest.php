<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('product can be deleted via index action', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductIndex::class)
        ->call('deleteProduct', $product->id);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

test('product deletion also deletes associated image', function () {
    Storage::fake('public');
    
    $user = User::factory()->create();
    $imagePath = UploadedFile::fake()->image('test.jpg')->store('products', 'public');
    
    $product = Product::factory()->create([
        'image' => $imagePath,
    ]);
    
    Storage::disk('public')->assertExists($product->image);

    Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\Products\ProductIndex::class)
        ->call('deleteProduct', $product->id);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
    
    Storage::disk('public')->assertMissing($product->image);
});
