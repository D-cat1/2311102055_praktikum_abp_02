<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Edit Produk') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Perbarui informasi produk di inventory.') }}</flux:text>
    </div>

    <form wire:submit="save" class="max-w-2xl space-y-6">
        <flux:input
            wire:model="name"
            :label="__('Nama Produk')"
            placeholder="Masukkan nama produk"
            required
        />

        <flux:field>
            <flux:label>{{ __('Gambar Produk') }}</flux:label>

            @if ($newImage)
                <div class="flex items-center gap-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <img src="{{ $newImage->temporaryUrl() }}" alt="Preview" class="h-16 w-16 rounded-lg object-cover" />
                    <div class="flex-1">
                        <flux:text variant="strong">{{ $newImage->getClientOriginalName() }}</flux:text>
                        <flux:text class="text-xs">{{ number_format($newImage->getSize() / 1024, 1) }} KB</flux:text>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="removeNewImage" />
                </div>
            @elseif ($existingImage)
                <div class="flex items-center gap-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <img src="{{ $existingImage }}" alt="Current Image" class="h-16 w-16 rounded-lg object-cover" />
                    <div class="flex-1">
                        <flux:text variant="strong">{{ __('Gambar Saat Ini') }}</flux:text>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="removeExistingImage" />
                </div>
            @else
                <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 px-6 py-8 transition hover:border-zinc-400 dark:border-zinc-600 dark:hover:border-zinc-500">
                    <flux:icon name="arrow-up-tray" class="size-8 text-zinc-400" />
                    <flux:text class="text-sm">{{ __('Klik untuk upload gambar baru') }}</flux:text>
                    <flux:text class="text-xs">JPG, PNG, GIF maksimal 2MB</flux:text>
                    <input type="file" wire:model="newImage" accept="image/*" class="hidden" />
                </label>
            @endif

            <flux:error name="newImage" />
        </flux:field>

        <flux:textarea
            wire:model="description"
            :label="__('Deskripsi')"
            placeholder="Masukkan deskripsi produk"
            rows="3"
        />

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                wire:model="price"
                :label="__('Harga (Rp)')"
                type="number"
                min="0"
                required
            />

            <flux:input
                wire:model="stock"
                :label="__('Stok')"
                type="number"
                min="0"
                required
            />
        </div>

        <div class="flex items-center gap-3">
            <flux:button variant="primary" type="submit" icon="check">
                {{ __('Simpan Perubahan') }}
            </flux:button>

            <flux:button variant="ghost" :href="route('products.index')" wire:navigate>
                {{ __('Batal') }}
            </flux:button>
        </div>
    </form>
</div>
