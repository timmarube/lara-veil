<?php

use App\Models\Media;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $inputName = 'media_id';
    public $inputId = 'media_id';
    public $selectedId;
    public $selectedUrl;
    public $search = '';

    public function mount($name = 'media_id', $id = null, $value = null)
    {
        $this->inputName = $name;
        $this->inputId = $id ?? $name;
        $this->selectedId = $value;
        
        if ($this->selectedId) {
            $media = Media::find($this->selectedId);
            $this->selectedUrl = $media?->url;
        }
    }

    public function selectMedia($id)
    {
        $media = Media::find($id);
        if ($media) {
            $this->selectedId = $media->id;
            $this->selectedUrl = $media->url;
            $this->dispatch('media-selected', id: $this->selectedId, url: $this->selectedUrl, inputId: $this->inputId);
        }
    }

    public function removeSelection()
    {
        $this->selectedId = null;
        $this->selectedUrl = null;
        $this->dispatch('media-selected', id: null, url: null, inputId: $this->inputId);
    }

    public function with()
    {
        return [
            'mediaItems' => Media::query()
                ->when($this->search, fn($q) => $q->where('file_name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(15)
        ];
    }
}; ?>

<div>
    <div class="relative group">
        <input type="hidden" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $selectedId }}">
        
        <div class="flex items-center gap-4 p-4 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 hover:border-indigo-500/50 transition-colors">
            @if($selectedUrl)
                <div class="relative w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 shadow-sm border border-white dark:border-zinc-800">
                    <img src="{{ $selectedUrl }}" class="w-full h-full object-cover">
                    <button type="button" wire:click="removeSelection" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:icon icon="x-mark" variant="mini" />
                    </button>
                </div>
            @else
                <div class="w-24 h-24 rounded-lg bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                    <flux:icon icon="photo" class="w-8 h-8" />
                </div>
            @endif

            <div class="flex-1">
                @if($selectedId)
                    <div class="text-sm font-medium dark:text-white mb-2">Image Selected</div>
                @else
                    <div class="text-sm font-medium dark:text-zinc-400 mb-2">No image selected</div>
                @endif
                
                <flux:modal.trigger name="media-selector-modal-{{ $inputId }}">
                    <flux:button size="sm" variant="ghost" icon="magnifying-glass">
                        {{ $selectedId ? 'Change Image' : 'Choose from Library' }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <flux:modal name="media-selector-modal-{{ $inputId }}" variant="large" class="space-y-6">
        <div>
            <flux:heading size="lg">Media Library</flux:heading>
            <flux:subheading>Choose an image for this resource.</flux:subheading>
        </div>

        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search files..." icon="magnifying-glass" />

        <div class="grid grid-cols-3 md:grid-cols-5 gap-3 max-h-[400px] overflow-y-auto pr-2">
            @foreach($mediaItems as $item)
                <button 
                    type="button"
                    wire:click="selectMedia({{ $item->id }})"
                    x-on:click="$dispatch('close')"
                    class="relative aspect-square rounded-lg overflow-hidden border-2 transition-all {{ $selectedId == $item->id ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-transparent hover:border-zinc-300 dark:hover:border-zinc-600' }}"
                >
                    <img src="{{ $item->url }}" class="w-full h-full object-cover">
                    @if($selectedId == $item->id)
                        <div class="absolute inset-0 bg-indigo-500/20 flex items-center justify-center">
                            <flux:icon icon="check-circle" variant="solid" class="text-white w-8 h-8" />
                        </div>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $mediaItems->links() }}
        </div>

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>
</div>
