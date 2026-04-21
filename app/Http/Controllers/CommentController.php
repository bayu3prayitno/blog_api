<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Mengambil daftar seluruh komentar.
     * Menggunakan Redis untuk caching selama 120 detik.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $cacheKey = "comments.page.{$page}";

        $paginatedData = Cache::remember($cacheKey, 120, function () {
            $comments = Comment::paginate(10);

            return [
                'data' => CommentResource::collection($comments)->resolve(),
                'meta' => [
                    'total_records' => $comments->total(),
                    'current_page'  => $comments->currentPage(),
                ]
            ];
        });

        return response()->json($paginatedData);
    }

    /**
     * Menyimpan komentar baru ke MySQL.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|max:250',
            'post_id' => 'required|exists:posts,id',
        ]);

        $validated['user_id'] = Auth::id();
        $comment = Comment::create($validated);

        Cache::flush(); 

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data'    => new CommentResource($comment)
        ], 201);
    }

    /**
     * Mengambil satu komentar spesifik berdasarkan ID.
     */
    public function show(string $id)
    {
        $comment = Cache::remember("comments.{$id}", 120, function () use ($id) {
            return Comment::find($id);
        });

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json($comment);
    }

    /**
     * Memperbarui komentar pada MySQL.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Memperbarui atribut yang dikirimkan klien
        $comment->update($request->all());

        // Hapus cache global dan cache spesifik item ini
        Cache::forget('comments.all');
        Cache::forget("comments.{$id}");

        return response()->json($comment);
    }

    /**
     * Menghapus komentar dari MySQL.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->delete();

        Cache::forget('comments.all');
        Cache::forget("comments.{$id}");

        return response()->json([
            'id' => $id,
            'deleted' => 'true'
        ]);
    }
}
