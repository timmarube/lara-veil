<x-layouts::app :title="__('Post Settings')">
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6 dark:text-white">Post Settings</h1>
        
         @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-700">
            <h2 class="text-lg font-bold text-rose-600 mb-2">Danger Zone</h2>
            <p class="text-neutral-600 dark:text-neutral-400 mb-4 text-sm">Deleting the posts table is irreversible. All posts will be permanently lost.</p>
            
            <form action="{{ route('admin.posts.remove_table') }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete the Posts table? This cannot be undone.');">
                @csrf
                <div class="flex items-center gap-2 mb-4">
                    <input type="checkbox" name="confirm" id="confirm" required class="rounded border-neutral-300 text-rose-600 focus:ring-rose-500">
                    <label for="confirm" class="text-sm text-neutral-700 dark:text-neutral-300">I confirm that I want to delete the posts table.</label>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 font-bold">Delete Posts Table</button>
            </form>
        </div>
    </div>
</x-layouts::app>
