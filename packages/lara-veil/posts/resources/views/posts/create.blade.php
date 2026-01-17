<x-layouts::app :title="__('Create Post')">
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6 dark:text-white">Create New Post</h1>
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-neutral-800 p-6 rounded-2xl shadow-sm border border-neutral-200 dark:border-neutral-700">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Title</label>
                <input type="text" name="title" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Content</label>
                <textarea name="content" rows="10" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Featured Image</label>
                    <livewire:media.selector 
                        name="featured_image_id" 
                        id="featured_image_id" 
                    />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500">Save Post</button>
            </div>
        </form>
    </div>
</x-layouts::app>
