<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Services\Vrm\MediaForgeService;

new class extends Component {
    use WithFileUploads;

    public $file;

    public function save(MediaForgeService $mediaForge)
    {
        $this->validate([
            'file' => 'required|file|max:10240',
        ]);

        $mediaForge->upload($this->file)
            ->to('uploads')
            ->save();

        session()->flash('success', 'File uploaded successfully.');
        return $this->redirect(route('admin.media.index'), navigate: true);
    }
}; ?>

<div class="max-w-xl mx-auto flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold dark:text-white">Upload Media</h1>
        <flux:button :href="route('admin.media.index')" variant="ghost" icon="arrow-left" wire:navigate>Back to Library</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 p-8 rounded-2xl shadow-sm border dark:border-zinc-700">
        <form wire:submit="save" class="space-y-6">
            <div 
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
            >
                <flux:input 
                    type="file" 
                    wire:model="file" 
                    label="Select File" 
                    required 
                />

                <div x-show="uploading" class="mt-4">
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-600 h-1.5 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Upload Media</span>
                    <span wire:loading>Uploading...</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
