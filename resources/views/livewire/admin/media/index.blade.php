<?php

use App\Models\Media;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\Vrm\MediaForgeService;

new class extends Component {
    use WithPagination;

    public function duplicate($id, MediaForgeService $mediaForge)
    {
        $media = Media::findOrFail($id);
        $oldPath = $media->path;
        $oldFullPath = $mediaForge->getFullPath($oldPath);

        if (!file_exists($oldFullPath)) {
            session()->flash('error', 'Original file not found.');
            return;
        }

        $pathInfo = pathinfo($oldPath);
        $newFilename = $pathInfo['filename'] . '-copy.' . ($pathInfo['extension'] ?? '');
        $newRelativePath = ($pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'] . '/') . $newFilename;
        $newFullPath = $mediaForge->getFullPath($newRelativePath);

        // Ensure we don't overwrite if copy already exists, add counter if needed
        $counter = 1;
        while (file_exists($newFullPath)) {
            $newFilename = $pathInfo['filename'] . '-copy-' . $counter . '.' . ($pathInfo['extension'] ?? '');
            $newRelativePath = ($pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'] . '/') . $newFilename;
            $newFullPath = $mediaForge->getFullPath($newRelativePath);
            $counter++;
        }

        if (copy($oldFullPath, $newFullPath)) {
            $newMedia = $media->replicate();
            $newMedia->path = $newRelativePath;
            $newMedia->name = $media->name . ' (Copy)';
            $newMedia->save();

            session()->flash('success', 'Media duplicated successfully.');
        } else {
            session()->flash('error', 'Failed to duplicate file.');
        }
    }

    public function delete($id, MediaForgeService $mediaForge)
    {
        $media = Media::findOrFail($id);
        $mediaForge->delete($media->path, 'all');
        $media->delete();
        
        session()->flash('success', 'Media deleted successfully.');
    }

    public function with()
    {
        return [
            'mediaItems' => Media::latest()->paginate(24),
        ];
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold dark:text-white">Media Library</h1>
        <flux:button :href="route('admin.media.create')" variant="primary">Add New Media</flux:button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 p-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 p-3 rounded-lg mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($mediaItems as $item)
            <div class="relative group bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden aspect-square border dark:border-zinc-700">
                @if(Str::startsWith($item->mime_type, 'image/'))
                    <img src="{{ $item->url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-zinc-700 text-gray-500">
                        {{ strtoupper($item->extension ?? pathinfo($item->file_name, PATHINFO_EXTENSION)) }}
                    </div>
                @endif
                
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 px-2">
                    <flux:button icon="pencil-square" :href="route('admin.media.edit', $item)" size="sm" variant="ghost" class="text-white hover:bg-white/20" />
                    
                    <flux:button icon="clipboard" wire:click="duplicate({{ $item->id }})" wire:confirm="Are you sure you want to duplicate this file?" size="sm" variant="ghost" class="text-white hover:bg-white/20" />
                    
                    <flux:button icon="trash" wire:click="delete({{ $item->id }})" wire:confirm="Are you sure you want to delete this file?" size="sm" variant="ghost" class="text-white hover:text-red-400 hover:bg-white/20" />
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-center">
                <flux:icon icon="photo" class="mx-auto h-12 w-12 text-zinc-300" />
                <flux:heading class="mt-4">No media found</flux:heading>
                <flux:subheading>Upload images to get started.</flux:subheading>
                <flux:button :href="route('admin.media.create')" class="mt-6">Upload First Image</flux:button>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $mediaItems->links() }}
    </div>
</div>
