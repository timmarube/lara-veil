<x-layouts::app :title="__('Manage Posts')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">📝 Posts</h1>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Create Post
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Title</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Author</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Created</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($posts as $post)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-neutral-900 dark:text-neutral-100 font-medium">{{ $post->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 text-sm">{{ $post->author->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-neutral-500 dark:text-neutral-500 text-xs">{{ $post->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 font-semibold text-xs" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-neutral-500">No posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</x-layouts::app>
