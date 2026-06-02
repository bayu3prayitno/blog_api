@extends('layouts.app')

@section('title', 'Perbarui Artikel')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="/posts" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        Batal dan Kembali
    </a>

    <!-- Loading Screen -->
    <div id="edit-loading" class="bg-white rounded-2xl p-12 text-center border border-gray-200 animate-pulse shadow-sm">
        <div class="h-6 bg-gray-200 rounded w-1/3 mx-auto mb-6"></div>
        <div class="space-y-4">
            <div class="h-10 bg-gray-200 rounded w-full"></div>
            <div class="h-40 bg-gray-200 rounded w-full"></div>
            <div class="h-10 bg-gray-200 rounded w-1/2"></div>
        </div>
    </div>

    <!-- Edit Form Panel -->
    <div id="edit-panel" class="hidden bg-white border border-gray-200 rounded-2xl p-8 shadow-sm relative overflow-hidden">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Perbarui Artikel</h3>
        
        <form onsubmit="handleUpdate(event)" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel</label>
                <input type="text" id="title" required maxlength="100" class="w-full bg-white border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl px-4 py-3 text-gray-800 focus:outline-none transition-all duration-200">
            </div>
            
            <div>
                <label for="content-input" class="block text-sm font-semibold text-gray-700 mb-2">Isi Artikel</label>
                <textarea id="content-input" required rows="10" class="w-full bg-white border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl px-4 py-3 text-gray-800 focus:outline-none transition-all duration-200"></textarea>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status Publikasi</label>
                <select id="status" required class="w-full bg-white border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl px-4 py-3 text-gray-800 focus:outline-none transition-all duration-200">
                    <option value="published">Publish (Dapat dibaca oleh semua orang)</option>
                    <option value="draft">Draft (Simpan sebagai draf pribadi)</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="/posts" class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-600 transition-colors duration-150 text-center font-semibold">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow hover:shadow-md transition-all duration-200">Perbarui Artikel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const postId = "{{ $id }}";

    document.addEventListener('DOMContentLoaded', () => {
        requireAuth();
        loadPostData();
    });

    async function loadPostData() {
        const loading = document.getElementById('edit-loading');
        const panel = document.getElementById('edit-panel');

        try {
            const response = await runApiRequest(`/posts/${postId}`);
            const post = response.data;

            // Integrity Check: verify current user is owner
            if (post.user_id !== auth.id) {
                localStorage.setItem('flash_message', 'Anda tidak memiliki hak akses untuk mengedit artikel ini.');
                window.location.href = '/posts';
                return;
            }

            // Fill input data
            document.getElementById('title').value = post.title;
            document.getElementById('content-input').value = post.content;
            document.getElementById('status').value = post.status;

            loading.classList.add('hidden');
            panel.classList.remove('hidden');
        } catch (err) {
            showGlobalToast('Gagal memuat artikel: ' + err.message, 'error');
        }
    }

    async function handleUpdate(e) {
        e.preventDefault();
        const title = document.getElementById('title').value;
        const content = document.getElementById('content-input').value;
        const status = document.getElementById('status').value;

        try {
            const response = await runApiRequest(`/posts/${postId}`, {
                method: 'PATCH',
                body: JSON.stringify({ title, content, status })
            });

            localStorage.setItem('flash_message', response.message || 'Artikel berhasil diperbarui!');
            window.location.href = '/posts';
        } catch (err) {
            showGlobalToast('Gagal memperbarui artikel: ' + err.message, 'error');
        }
    }
</script>
@endsection
