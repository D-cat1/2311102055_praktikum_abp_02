<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function deleteProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        Flux::toast(variant: 'success', text: __('Produk berhasil dihapus.'));
        $this->modal('delete-product-modal')->close();
    }

    public function render()
    {
        return view('livewire.products.product-index');
    }
}
