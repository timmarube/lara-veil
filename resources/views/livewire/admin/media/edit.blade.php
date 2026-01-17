<?php

use App\Models\Media;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Services\Vrm\MediaForgeService;

new class extends Component {
    use WithFileUploads;

    public Media $medium;
    public $replacement;
    public $width;
    public $height;
    public $ratio = true;
    public $rotate;
    public $flip;
    public $brightness = 0;
    public $contrast = 0;
    public $blur = 0;
    public $greyscale = false;

    public function mount(Media $medium)
    {
        $this->medium = $medium;
        
        $fullPath = public_path($medium->path);
        if (file_exists($fullPath)) {
            $dimensions = @getimagesize($fullPath);
            if ($dimensions) {
                $this->width = $dimensions[0];
                $this->height = $dimensions[1];
            }
        }
    }

    public function save(MediaForgeService $mediaForge)
    {
        // Handle replacement upload if provided
        if ($this->replacement) {
            $this->validate([
                'replacement' => 'image|max:10240',
            ]);

            // Delete old physical files first
            $mediaForge->delete($this->medium->path, 'all');
            
            // Use the new file
            $mediaForge->upload($this->replacement);
            
            // Try to keep it in the same sub-directory
            $uploadDir = dirname($this->medium->path);
            if (str_starts_with($uploadDir, 'media/')) {
                $uploadDir = substr($uploadDir, 6);
                $mediaForge->to($uploadDir)->useYearFolder(false);
            }
        } else {
            $mediaForge->loadFromPath($this->medium->path);
        }

        // Apply operations
        if ($this->width || $this->height) {
            $mediaForge->resize(
                (int) ($this->width ?: 0), 
                (int) ($this->height ?: 0), 
                (bool) $this->ratio
            );
        }

        if ($this->rotate) {
            $mediaForge->rotate((float) $this->rotate);
        }

        if ($this->flip) {
            $mediaForge->flip($this->flip);
        }

        if ($this->brightness != 0) {
            $mediaForge->brightness((int) $this->brightness);
        }

        if ($this->contrast != 0) {
            $mediaForge->contrast((int) $this->contrast);
        }

        if ($this->blur != 0) {
            $mediaForge->blur((int) $this->blur);
        }

        if ($this->greyscale) {
            $mediaForge->greyscale();
        }

        $newPath = $mediaForge->run();
        $fullPathProcessed = $mediaForge->getFullPath($newPath);
        
        $this->medium->update([
            'path' => $newPath,
            'size' => file_exists($fullPathProcessed) ? filesize($fullPathProcessed) : $this->medium->size,
            'mime_type' => file_exists($fullPathProcessed) ? mime_content_type($fullPathProcessed) : $this->medium->mime_type,
        ]);

        $this->replacement = null;
        $this->reset(['rotate', 'flip', 'brightness', 'contrast', 'blur', 'greyscale']);
        
        session()->flash('success', 'Image updated successfully.');
        
        // Refresh dimensions
        if (file_exists($fullPathProcessed)) {
            $dimensions = @getimagesize($fullPathProcessed);
            if ($dimensions) {
                $this->width = $dimensions[0];
                $this->height = $dimensions[1];
            }
        }
    }

    public function delete(MediaForgeService $mediaForge)
    {
        $mediaForge->delete($this->medium->path, 'all');
        $this->medium->delete();
        
        return $this->redirect(route('admin.media.index'), navigate: true);
    }
}; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Preview Column --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl shadow-sm border dark:border-zinc-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold dark:text-white">Image Preview</h2>
                <flux:link :href="route('admin.media.index')" variant="ghost" icon="arrow-left" wire:navigate>Back to Library</flux:link>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 p-3 rounded-lg mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="relative bg-zinc-100 dark:bg-zinc-900 rounded-lg overflow-hidden flex items-center justify-center min-h-[400px] border border-dashed border-zinc-300 dark:border-zinc-700">
                <img src="{{ $medium->url }}?t={{ time() }}" alt="{{ $medium->name }}" class="max-w-full max-h-[700px] object-contain shadow-2xl">
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border dark:border-zinc-700">
            <h3 class="text-md font-medium mb-4 dark:text-white">File Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                <div>
                    <span class="text-zinc-500 block mb-1">Filename</span>
                    <span class="dark:text-zinc-300 font-mono break-all">{{ $medium->file_name }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 block mb-1">MIME Type</span>
                    <span class="dark:text-zinc-300">{{ $medium->mime_type }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 block mb-1">File Size</span>
                    <span class="dark:text-zinc-300">{{ number_format($medium->size / 1024, 2) }} KB</span>
                </div>
                <div>
                    <span class="text-zinc-500 block mb-1">Dimensions</span>
                    <span class="dark:text-zinc-300">{{ $width }} × {{ $height }} px</span>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t dark:border-zinc-700">
                <span class="text-zinc-500 block mb-2 text-sm">Public URL</span>
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ $medium->url }}" class="flex-1 bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 rounded p-2 text-xs font-mono dark:text-zinc-300">
                    <flux:button size="sm" onclick="navigator.clipboard.writeText('{{ $medium->url }}'); alert('Copied!')">Copy</flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tools Column --}}
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            {{-- Replace Image --}}
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border dark:border-zinc-700">
                <h3 class="font-semibold mb-4 flex items-center gap-2 text-sm uppercase tracking-wider text-zinc-400">
                    <flux:icon icon="arrow-up-tray" variant="mini" />
                    Replace Image
                </h3>
                <div class="space-y-4">
                    <flux:input type="file" wire:model="replacement" label="Upload New Version" />
                    <p class="text-[10px] text-zinc-500 leading-relaxed">
                        Uploading a new file will delete the current image and its thumbnails immediately.
                    </p>
                </div>
            </div>

            {{-- Resize & Crop --}}
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border dark:border-zinc-700">
                <h3 class="font-semibold mb-4 dark:text-white flex items-center gap-2">
                    <flux:icon icon="arrows-pointing-out" variant="mini" />
                    Resize
                </h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <flux:input label="Width (px)" type="number" wire:model.defer="width" />
                    <flux:input label="Height (px)" type="number" wire:model.defer="height" />
                </div>
                <flux:checkbox wire:model="ratio" label="Maintain Aspect Ratio" />
            </div>

            {{-- Transformations --}}
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border dark:border-zinc-700">
                <h3 class="font-semibold mb-4 dark:text-white flex items-center gap-2">
                    <flux:icon icon="arrow-path" variant="mini" />
                    Transform
                </h3>
                <div class="space-y-4">
                    <flux:select wire:model="rotate" label="Rotate">
                        <option value="">No rotation</option>
                        <option value="90">90° Clockwise</option>
                        <option value="180">180° Rotate</option>
                        <option value="270">90° Counter-clockwise</option>
                    </flux:select>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="flip" value="h" class="hidden peer">
                            <div class="p-2 text-center border dark:border-zinc-700 rounded-lg peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 peer-checked:border-indigo-500 dark:text-zinc-400 text-sm">
                                Flip Horizontal
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="flip" value="v" class="hidden peer">
                            <div class="p-2 text-center border dark:border-zinc-700 rounded-lg peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 peer-checked:border-indigo-500 dark:text-zinc-400 text-sm">
                                Flip Vertical
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Adjustments --}}
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border dark:border-zinc-700">
                <h3 class="font-semibold mb-4 dark:text-white flex items-center gap-2">
                    <flux:icon icon="adjustments-horizontal" variant="mini" />
                    Adjustments
                </h3>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs dark:text-zinc-400">
                            <span>Brightness ({{ $brightness }})</span>
                        </div>
                        <input type="range" wire:model="brightness" min="-100" max="100" 
                               class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs dark:text-zinc-400">
                            <span>Contrast ({{ $contrast }})</span>
                        </div>
                        <input type="range" wire:model="contrast" min="-100" max="100" 
                               class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs dark:text-zinc-400">
                            <span>Blur ({{ $blur }})</span>
                        </div>
                        <input type="range" wire:model="blur" min="0" max="100" 
                               class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>

                    <flux:checkbox wire:model="greyscale" label="Greyscale Filter" />
                </div>
            </div>

            <div class="space-y-3 pt-4">
                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove>Apply Changes</span>
                    <span wire:loading>Processing...</span>
                </flux:button>
            </div>
        </form>

        {{-- Delete --}}
        <div class="mt-6 pt-6 border-t dark:border-zinc-700">
            <flux:button variant="danger" class="w-full" wire:click="delete" wire:confirm="Permanently delete this media?">Delete Permanently</flux:button>
        </div>
    </div>
</div>
