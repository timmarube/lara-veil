<x-layouts::app :title="__('Manage Plugins')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">🔌 Plugins</h1>
            <p class="text-neutral-500 dark:text-neutral-400 font-medium">Lara-Veil System v1.0</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($plugins as $plugin)
                <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm transition-all hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $plugin->name }}</h3>
                                <p class="text-xs text-neutral-500 font-mono">{{ $plugin->namespace }}</p>
                            </div>
                            <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md {{ $plugin->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-100 text-neutral-600' }}">
                                {{ $plugin->status }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-neutral-600 dark:text-neutral-400">
                                <span class="w-20">Version:</span>
                                <span class="font-medium font-mono text-xs">{{ $plugin->version }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <form action="{{ route('admin.plugins.toggle', $plugin) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 rounded-xl font-semibold text-sm transition-all {{ $plugin->status === 'active' ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200' }}">
                                    {{ $plugin->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts::app>
