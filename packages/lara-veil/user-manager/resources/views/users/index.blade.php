<x-layouts::app :title="__('Manage Users')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">👥 User Management</h1>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Add New User
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase">Created</th>
                        <th class="px-6 py-4 text-xs font-bold text-neutral-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="size-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-400 font-bold text-xs mr-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-neutral-900 dark:text-neutral-100 font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 text-sm italic">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-neutral-500 dark:text-neutral-500 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 font-semibold text-xs" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts::app>
