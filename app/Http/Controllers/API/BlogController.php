<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Traits\Slugify;

class BlogController extends Controller
{
    // import custom trait
    use Slugify;

    // Fetch all blogs
    public function index()
    {
        return response()->json(Blog::paginate(config('app.pagination')));
    }

    // Create a new blog
    public function store(Request $request)
    {
        $input = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'string',
        ]);

        $input['slug'] = $this->to_slug($input['title']);

        $blog = Blog::create($input);

        return response()->json($blog, 201);
    }

    // Show a specific blog with its posts
    public function show(string $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return response()->json($blog->load('posts'));
    }

    // Update a blog
    public function update(Request $request, string $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $input = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'string',
        ]);

        if ($input['title'] != $blog->title) {
            $input['slug'] = $this->to_slug($input['title']);
        }

        $blog->update($input);

        return response()->json($blog);
    }

    // Delete a blog
    public function destroy(string $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $blog->delete();

        return response()->json("Blog deleted successfully", 200);
    }
}
