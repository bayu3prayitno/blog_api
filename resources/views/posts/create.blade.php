@extends('layouts.app')

@section('title', 'Tulis Artikel Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="/posts" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-400 hover:text-cyan-400 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        Batal dan Kembali
    </a>

    <div class="glass-panel rounded-3xl p-8 border border-darkBorder/60 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-cyan-500 to-indigo-600"></div>
        <h3 class="text-2xl font-bold text-white mb-6">Tulis Artikel Baru</h3>
        
        <form onsubmit="handleCreate(event)" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-300 mb-2">Judul Artikel</label>
                <input type="text" id="title" required maxlength="100" class="w-full bg-slate-900 border border-darkBorder focus:border-cyan-500 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-600 focus:outline-none transition-colors duration-200" placeholder="Masukkan judul artikel yang menarik...">
            </div>
            
            <div>
                <label for="content-input" class="block text-sm font-semibold text-slate-300 mb-2">Isi Artikel</label>
                <textarea id="content-input" required rows="10" class="w-full bg-slate-900 border border-darkBorder focus:border-cyan-500 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-600 focus:outline-none transition-colors duration-200" placeholder="Tuliskan gagasan, panduan, atau cerita Anda di sini..."></textarea>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-300 mb-2">Status Publikasi</label>
                <select id="status" required class="w-full bg-slate-900 border border-darkBorder focus:border-cyan-500 rounded-xl px-4 py-3 text-slate-200 focus:outline-none transition-colors duration-200">
                    <option value="published">Publish (Dapat dibaca oleh semua orang)</option>
                    <option value="draft">Draft (Simpan sebagai draf pribadi)</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-darkBorder/40">
                <a href="/posts" class="px-5 py-2.5 rounded-xl border border-darkBorder hover:bg-slate-800 text-slate-400 hover:text-white transition-colors duration-150 text-center">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-md transition-all duration-200">Terbitkan Artikel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Must be logged in
        requireAuth();
    });

    async function handleCreate(e) {
        e.preventDefault();
        const title = document.getElementById('title').value;
        const content = document.getElementById('content-input').value;
        const status = document.getElementById('status').value;

        try {
            const response = await runApiRequest('/posts', {
                method: 'POST',
                body: JSON.stringify({ title, content, status })
            });

            localStorage.setItem('flash_message', response.message || 'Artikel berhasil diterbitkan!');
            window.location.href = '/posts';
        } catch (err) {
            showGlobalToast('Gagal memuat draf: ' + err.message, 'error');
        }
    }
</script>
@endsection
