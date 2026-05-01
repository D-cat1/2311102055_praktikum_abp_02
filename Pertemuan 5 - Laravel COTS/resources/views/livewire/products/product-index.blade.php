<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Products') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage your product inventory.') }}</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('products.create')" wire:navigate>
            {{ __('Tambah Produk') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search products..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
    </div>

    <flux:table :paginate="$this->products">
        <flux:table.columns>
            <flux:table.column>{{ __('Image') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection" wire:click="sort('price')">{{ __('Price') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'stock'" :direction="$sortDirection" wire:click="sort('stock')">{{ __('Stock') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Created') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>
                        @if ($product->image)
                            <img
                                src="{{ Storage::disk('public')->exists($product->image) ? Storage::url($product->image) : $product->image }}"
                                alt="{{ $product->name }}"
                                class="h-10 w-10 rounded-lg object-cover"
                            />
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon name="photo" class="size-5 text-zinc-400" />
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div>
                            <flux:text variant="strong">{{ $product->name }}</flux:text>
                            @if ($product->description)
                                <flux:text class="mt-0.5 line-clamp-1 text-xs">{{ $product->description }}</flux:text>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell variant="strong">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$product->stock > 10 ? 'green' : ($product->stock > 0 ? 'yellow' : 'red')" inset="top bottom">
                            {{ $product->stock }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        {{ $product->created_at->format('d M Y') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="ghost" icon="pencil-square" :href="route('products.edit', $product)" wire:navigate aria-label="Edit" />
                            
                            <flux:modal.trigger name="delete-product-modal-{{ $product->id }}">
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10" aria-label="Delete" />
                            </flux:modal.trigger>
                            
                            <flux:modal name="delete-product-modal-{{ $product->id }}" class="min-w-[22rem]">
                                <form wire:submit="deleteProduct({{ $product->id }})" class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Hapus Produk') }}</flux:heading>
                                        <flux:text class="mt-1">
                                            {{ __('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.') }}
                                        </flux:text>
                                    </div>
                                    
                                    <div class="flex justify-end gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                        </flux:modal.close>
                                        
                                        <flux:button type="submit" variant="danger">{{ __('Hapus') }}</flux:button>
                                    </div>
                                </form>
                            </flux:modal>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">
                        <div class="flex flex-col items-center gap-2 py-8">
                            <flux:icon name="inbox" class="size-8 text-zinc-400" />
                            <flux:text>{{ __('No products found.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
