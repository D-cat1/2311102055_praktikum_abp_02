<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|image|max:2048')]
    public $image;

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('required|integer|min:0')]
    public int $price = 0;

    #[Validate('required|integer|min:0')]
    public int $stock = 0;

    public function removeImage(): void
    {
        $this->image = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'name' => $validated['name'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        Flux::toast(variant: 'success', text: __('Produk berhasil ditambahkan.'));

        $this->redirectRoute('products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.products.product-create');
    }
}
