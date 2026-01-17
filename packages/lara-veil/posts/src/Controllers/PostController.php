<?php

namespace LaraVeil\Posts\Controllers;

use App\Http\Controllers\Controller;
use LaraVeil\Posts\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author')->latest()->paginate(10);
        return view('posts::posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts::posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $slug = Str::slug($request->title);
        // check slug even for soft deleted posts where slug like %slug%
        $count = Post::withTrashed()->where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $post = Post::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('featured_image')) {
            $media = $post->addMedia($request->file('featured_image'))
                ->to('uploads/posts')
                ->inCollection('featured_image')
                ->save();
                
            $post->update(['featured_image_id' => $media->id]);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return view('posts::posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->status = $request->status;

        if ($request->hasFile('featured_image')) {
            // Delete old media/file if replaced
             if ($post->featured_image_id) {
                 // Clean up old media record and file
                 // Since we have HasMedia, we can just delete the media model directly if we loaded it, 
                 // or use the relation. 
                 $post->featuredImage?->delete();
                 // Note: Media model delete() usually handles file deletion if using Spatie or similar, 
                 // but here we just created a basic Media model.
                 // Ideally MediaForgeService or Media model events should handle file deletion.
                 // For now, let's assume MediaForge cleanup or manual cleanup is needed?
                 // Wait, MediaForgeService::deleteOldFile logic existed but that was for atomic ops.
                 // I will blindly overwrite for now or rely on SoftDeletes if Media has it?
                 // Media model doesn't have SoftDeletes unless I added it? 
                 // I should check Media migration? No soft deletes there.
                 
                 // If I want to delete the file, I should do:
                 // Storage::disk($post->featuredImage->disk)->delete($post->featuredImage->path);
                 // $post->featuredImage->delete();
                 
                 if ($oldMedia = $post->featuredImage) {
                      Storage::disk($oldMedia->disk)->delete($oldMedia->path);
                      $oldMedia->delete();
                 }
             }
             
             // Also clear collection as backup cleanup
             $post->media()->where('collection_name', 'featured_image')->delete();

            $media = $post->addMedia($request->file('featured_image'))
                ->to('uploads/posts')
                ->inCollection('featured_image')
                ->save();

            $post->featured_image_id = $media->id;
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
             Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    // Settings
    public function settings()
    {
        return view('posts::settings');
    }

    public function removeTable(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        Schema::dropIfExists('posts');

        return redirect()->route('admin.posts.settings')->with('success', 'Posts table removed successfully. The plugin will no longer function until you reinstall or run migrations.');
    }
}
