<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * Mengambil daftar seluruh artikel.
     * Data diakses dari memori Redis. Jika tidak ditemukan, MySQL diakses dan data disimpan ke Redis.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        $paginatedData = Cache::remember("posts.page.{$page}", 120, function () {
            $posts = Post::paginate(10);
            return [
                'data' => PostResource::collection($posts)->resolve(),
                'meta' => [
                    'total_records' => $posts->total(),
                    'current_page'  => $posts->currentPage(),
                    'last_page'     => $posts->lastPage(),
                ]
            ];
        });

        return response()->json($paginatedData);
    }

    /**
     * Menyimpan artikel baru ke MySQL.
     * Cache daftar artikel pada Redis dihapus untuk memastikan konsistensi data.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:100',
            'status' => 'required|in:draft,published',
            'content' => 'required',
        ]);

        $validatedData['user_id'] = $request->user()->id;

        $post = Post::create($validatedData);

        return response()->json($post, 201);
    }

    public function show(string $id)
    {
        $post = Cache::remember("posts.{$id}", 120, function () use ($id) {
            $data = Post::find($id);
            return $data ? (new PostResource($data))->resolve() : null;
        });

        if (!$post) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan.',
                'error'   => 'Not Found'
            ], 404);
        }

        return response()->json(['data' => $post]);
    }

    /**
     * Memperbarui data artikel pada MySQL.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->update($request->all());

        Cache::forget('posts.all');
        Cache::forget("posts.{$id}");

        return response()->json($post);
    }

    /**
     * Menghapus artikel dari MySQL.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        Cache::forget("posts.{$id}");
        Cache::forget("posts.page.1");

        return response()->json([
            'id' => $id,
            'deleted' => 'true'
        ]);
    }
}
