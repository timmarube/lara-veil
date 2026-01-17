<?php

use App\Models\Theme;
use App\Core\ThemeManager;
use Livewire\Volt\Component;

new class extends Component {
    public function activate(Theme $theme)
    {
        Theme::where('is_active', true)->update(['is_active' => false]);
        $theme->is_active = true;
        $theme->save();

        session()->flash('success', "Theme {$theme->name} activated.");
    }

    public function with(ThemeManager $themeManager)
    {
        $themeManager->syncThemes();
        return [
            'themes' => Theme::all(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">🎨 Themes</h1>
        <p class="text-neutral-500 dark:text-neutral-400 font-medium">Lara-Veil Style Engine</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-400 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($themes as $theme)
            <div class="bg-white dark:bg-neutral-800 rounded-3xl border {{ $theme->is_active ? 'border-indigo-500 ring-4 ring-indigo-500/10 dark:ring-indigo-500/20' : 'border-neutral-200 dark:border-neutral-700' }} overflow-hidden shadow-sm transition-all hover:translate-y-[-4px] hover:shadow-xl">
                <div class="aspect-[4/3] bg-neutral-100 dark:bg-neutral-900 relative group">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 backdrop-blur-sm">
                         <span class="text-white font-bold text-lg">Preview Theme</span>
                    </div>
                    <div class="absolute top-4 right-4">
                        @if($theme->is_active)
                            <span class="bg-indigo-600 text-white text-[10px] font-black uppercase px-3 py-1.5 rounded-full shadow-lg">Active</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-black text-neutral-900 dark:text-neutral-100 mb-1">{{ $theme->name }}</h3>
                    <p class="text-xs text-neutral-500 font-mono mb-6">{{ $theme->slug }}</p>
                    
                    <flux:button 
                        wire:click="activate({{ $theme->id }})" 
                        class="w-full" 
                        :variant="$theme->is_active ? 'filled' : 'primary'"
                        :disabled="$theme->is_active"
                    >
                        {{ $theme->is_active ? 'Currently Active' : 'Activate Theme' }}
                    </flux:button>
                </div>
            </div>
        @endforeach
    </div>
</div>
