<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Transformers\PostTransformer; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Database\QueryException;
use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver;
use App\Utilities\ImageFormatter;
use Illuminate\Support\Facades\Auth; 

class PostController extends Controller
{
    /**
     * Mengambil daftar seluruh artikel dengan caching Redis dan Fractal.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        // Perbaikan: Gunakan Cache Tags secara konsisten sesuai laporan [cite: 301]
        $paginatedData = Cache::tags(['posts'])->remember("posts.page.{$page}", 120, function () {
            $posts = Post::paginate(10); 

            $transformed = fractal()
                ->collection($posts, new PostTransformer())
                ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
                ->toArray();

            return [
                'data' => $transformed['data'],
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
     * Menyimpan artikel baru dengan validasi dan pemrosesan gambar.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'   => 'required|max:100', 
            'status'  => 'required|in:draft,published',
            'content' => 'required',
        ]);

        // Menggunakan JWT auth untuk mendapatkan ID user yang sedang login [cite: 339]
        $validatedData['user_id'] = Auth::id(); 

        $post = Post::create($validatedData);

        // Hapus cache agar data terbaru muncul [cite: 360]
        Cache::tags(['posts'])->flush();

        $transformed = fractal()
            ->item($post, new PostTransformer())
            ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
            ->toArray();

        return response()->json(['message' => 'Artikel berhasil dibuat', 'data' => $transformed], 201);
    }

    /**
     * Mengambil satu artikel spesifik.
     */
    public function show(string $id)
    {
        // Perbaikan: Tambahkan Tags agar sinkron dengan index [cite: 376]
        $post = Cache::tags(['posts'])->remember("posts.show.{$id}", 120, function () use ($id) {
            $data = Post::find($id);
            return $data ? fractal()
                ->item($data, new PostTransformer())
                ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
                ->toArray() : null; 
        });

        if (!$post) {
            return response()->json(['message' => 'Artikel tidak ditemukan.', 'error' => 'Not Found'], 404);
        }

        return response()->json(['data' => $post]);
    }

    /**
     * Memperbarui data artikel dan membersihkan cache.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        }

        // Proteksi kepemilikan data: Memastikan hanya pemilik yang bisa update [cite: 406]
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title'   => 'sometimes|max:100',
            'status'  => 'sometimes|in:draft,published',
            'content' => 'sometimes',
        ]);

        $post->update($validatedData);

        // Membersihkan cache setelah data berubah [cite: 450]
        Cache::tags(['posts'])->flush();

        $transformed = fractal()
            ->item($post, new PostTransformer())
            ->serializeWith(new \League\Fractal\Serializer\ArraySerializer())
            ->toArray();

        return response()->json(['message' => 'Artikel berhasil diperbarui.', 'data' => $transformed]);
    }

    /**
     * Menghapus artikel dan file terkait.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        if ($post->user_id !== Auth::id()) return response()->json(['message' => 'Unauthorized'], 403);

        // Hapus berkas fisik gambar jika ada [cite: 479]
        if ($post->image) {
            $oldPath = storage_path('app/public/' . $post->image);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        try {
            $post->delete();
        } catch (QueryException $e) {
            // Penanganan relasi tabel jika masih ada komentar [cite: 492]
            if ($e->getCode() == '23000') {
                return response()->json(['message' => 'Gagal menghapus. Masih ada komentar terhubung.'], 409);
            }
        }

        // Hapus cache daftar artikel agar tetap sinkron [cite: 501]
        Cache::tags(['posts'])->flush(); 

        return response()->json(['id' => $id, 'deleted' => true, 'message' => 'Artikel berhasil dihapus.']);
    }
}