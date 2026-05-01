<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    public Product $product;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|image|max:2048')]
    public $newImage;

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('required|integer|min:0')]
    public int $price = 0;

    #[Validate('required|integer|min:0')]
    public int $stock = 0;

    public ?string $existingImage = null;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->stock = $product->stock;

        if ($product->image) {
            $this->existingImage = Storage::disk('public')->exists($product->image)
                ? Storage::url($product->image)
                : $product->image;
        }
    }

    public function removeNewImage(): void
    {
        $this->newImage = null;
    }

    public function removeExistingImage(): void
    {
        $this->existingImage = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $imagePath = $this->product->image;

        if ($this->newImage) {
            // Delete old image if it exists in storage
            if ($this->product->image && Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }
            $imagePath = $this->newImage->store('products', 'public');
        } elseif ($this->existingImage === null && $this->product->image) {
            // User removed the existing image
            if (Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }
            $imagePath = null;
        }

        $this->product->update([
            'name' => $validated['name'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        Flux::toast(variant: 'success', text: __('Produk berhasil diperbarui.'));

        $this->redirectRoute('products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.products.product-edit');
    }
}
