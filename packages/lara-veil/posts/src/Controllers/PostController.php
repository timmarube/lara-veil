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
            'featured_image_id' => 'nullable|exists:media,id',
        ]);

        $slug = Str::slug($request->title);
        $count = Post::withTrashed()->where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Post::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'status' => $request->status,
            'user_id' => auth()->id(),
            'featured_image_id' => $request->featured_image_id,
        ]);

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
            'featured_image_id' => 'nullable|exists:media,id',
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->status = $request->status;
        $post->featured_image_id = $request->featured_image_id;

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    public function settings()
    {
        return view('posts::settings');
    }

    public function removeTable(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        // Schema::dropIfExists('posts');
        // remove migration
        \Illuminate\Support\Facades\Artisan::call('migrate:rollback', [
            '--path' => 'packages/lara-veil/posts/database/migrations',
            '--force' => true,
        ]);

        return redirect()->route('admin.posts.settings')->with('success', 'Posts table removed successfully.');
    }
}
