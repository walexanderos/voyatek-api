<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Traits\Slugify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // import custom trait
    use Slugify;

    // Fetch all posts under a specific blog
    public function index(string $blog_slug)
    {
        $blog = Blog::where('slug', $blog_slug)->firstOrFail();
        return response()->json(Post::where('blog_id', $blog->id)->paginate(config('app.pagination')));
    }

    // Create a new post under a specific blog
    public function store(Request $request, string $blog_slug)
    {
        $blog = Blog::where('slug', $blog_slug)->firstOrFail();

        $input = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        $input['blog_id'] = $blog->id;
        $input['slug'] = $this->to_slug($input['title']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $input['image'] = Storage::url($path);
        }

        $post = Post::create($input);

        return response()->json($post, 201);
    }

    // Show a specific post with its likes and comments
    public function show(string $blog_slug, string $post_slug)
    {
        $post = Post::where('slug', $post_slug)->firstOrFail();

        return response()->json([
            ...$post->toArray(),
            'comments' => $post->comments,
            'likes' => $post->likes()->count()
        ]);

    }

    // Update a post
    public function update(Request $request, string $blog_slug, string $post_slug)
    {
        $blog = Blog::where('slug', $blog_slug)->firstOrFail();
        $post = Post::where('slug', $post_slug)->where('blog_id', $blog->id)->firstOrFail();

        $input = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        if ($input['title'] != $post->title) {
            $input['slug'] = $this->to_slug($input['title']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $input['image'] = Storage::url($path);
        }


        $post->update($input);

        return response()->json($post);
    }

    // Delete a post
    public function destroy(string $blog_slug, string $post_slug)
    {
        $blog = Blog::where('slug', $blog_slug)->firstOrFail();
        $post = Post::where('slug', $post_slug)->where('blog_id', $blog->id)->firstOrFail();
        $post->delete();

        return response()->json(true, 200);
    }

    // Add a comment to a post.
    public function addComment(Request $request, string $blog_slug, string $post_slug)
    {
        $post = Post::where('slug', $post_slug)->firstOrFail();

        $input = $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|integer'
        ]);

        User::findOrFail($input['user_id']);
        $input['post_id'] = $post->id;


        $comment = Comment::create($input);

        return response()->json($comment, 201);
    }

    public function likePost(Request $request, string $blog_slug, string $post_slug)
    {
        $input = $request->validate([
            'user_id' => 'required|integer'
        ]);

        User::findOrFail($input['user_id']);
        $post = Post::where('slug', $post_slug)->firstOrFail();

        $input['post_id'] = $post->id;
        Like::firstOrCreate($input);

        return response()->json(true, 200);
    }

}
