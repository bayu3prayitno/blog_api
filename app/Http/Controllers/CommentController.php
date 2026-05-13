<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    /**
     * Mengambil daftar seluruh komentar.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        $paginatedData = Cache::tags(['comments'])->remember("comments.page.{$page}", 120, function () {
            $comments = Comment::paginate(10);

            // Fractal: transformasi collection dengan metadata paginasi
            $transformed = fractal()
                ->collection($comments, new CommentTransformer())
                ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
                ->toArray();

            return [
                'data' => $transformed['data'],
                'meta' => [
                    'total_records' => $comments->total(),
                    'current_page'  => $comments->currentPage(),
                    'last_page'     => $comments->lastPage(),
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
        $validatedData = $request->validate([
            'comment' => 'required|max:250',
            'post_id' => 'required|integer|exists:posts,id',
        ], [
            'comment.required' => 'Teks komentar wajib diisi.',
            'comment.max'      => 'Komentar tidak boleh lebih dari 250 karakter.',
            'post_id.required' => 'ID Artikel tujuan wajib disertakan.',
            'post_id.exists'   => 'Artikel dengan ID tersebut tidak ditemukan.',
        ]);

        $validatedData['user_id'] = Auth::id();
        $comment = Comment::create($validatedData);

        Cache::forget("comments.{$comment->id}");
        Cache::tags(['comments'])->flush();

        // Fractal: transformasi output create untuk konsistensi
        $transformed = fractal()
            ->item($comment, new CommentTransformer())
            ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
            ->toArray();

        return response()->json([
            'message' => 'Komentar berhasil dibuat',
            'data'    => $transformed
        ], 201);
    }

    /**
     * Mengambil satu komentar spesifik berdasarkan ID.
     */
    public function show(string $id)
    {
        $comment = Cache::remember("comments.{$id}", 120, function () use ($id) {
            $data = Comment::find($id);

            // Fractal: transformasi single item
            return $data
                ? fractal()
                    ->item($data, new CommentTransformer())
                    ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
                    ->toArray()
                : null;
        });

        if (!$comment) {
            return response()->json([
                'message' => 'Komentar tidak ditemukan.',
                'error'   => 'Not Found'
            ], 404);
        }

        return response()->json(['data' => $comment]);
    }

    /**
     * Memperbarui komentar pada MySQL.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Gagal memperbarui. Komentar tidak ditemukan.',
                'error'   => 'Not Found'
            ], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk memperbarui komentar ini.',
                'error'   => 'Unauthorized'
            ], 403);
        }

        $validatedData = $request->validate([
            'comment' => 'required|max:250',
        ], [
            'comment.required' => 'Teks komentar tidak boleh kosong.',
            'comment.max'      => 'Komentar maksimal 250 karakter.',
        ]);

        $comment->update($validatedData);

        Cache::forget("comments.{$id}");
        Cache::tags(['comments'])->flush();

        // Fractal: transformasi output update
        $transformed = fractal()
            ->item($comment, new CommentTransformer())
            ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
            ->toArray();

        return response()->json([
            'message' => 'Komentar berhasil diperbarui.',
            'data'    => $transformed
        ]);
    }

    /**
     * Menghapus komentar dari MySQL.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Gagal menghapus. Komentar tidak ditemukan.',
                'error'   => 'Not Found'
            ], 404);
        }

        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.',
                'error'   => 'Unauthorized'
            ], 403);
        }

        $comment->delete();

        Cache::forget("comments.{$id}");
        Cache::tags(['comments'])->flush();

        return response()->json([
            'id'      => $id,
            'deleted' => true,
            'message' => 'Komentar telah dihapus.'
        ]);
    }
}
