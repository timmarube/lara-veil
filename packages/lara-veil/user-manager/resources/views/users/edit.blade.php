<x-layouts::app :title="__('Edit User')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">✏️ Edit User: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700">← Back to Users</a>
        </div>

        <div class="max-w-2xl bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full bg-neutral-50 dark:bg-neutral-900 border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full bg-neutral-50 dark:bg-neutral-900 border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Password (Leave blank to keep current)</label>
                    <input type="password" name="password" id="password" class="w-full bg-neutral-50 dark:bg-neutral-900 border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-[10px] text-neutral-500 uppercase tracking-wider">Only fill this if you want to change the password.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        💾 Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
