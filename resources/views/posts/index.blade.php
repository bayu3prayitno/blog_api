@extends('layouts.app')

@section('title', 'Daftar Artikel')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 id="page-title" class="text-3xl font-bold tracking-tight text-gray-800 mb-2">Jelajahi Artikel</h2>
        <p id="page-description" class="text-gray-500">Temukan wawasan baru, tutorial, dan opini menarik dari para kontributor kami.</p>
    </div>
</div>

<!-- Skeleton Loading -->
<div id="posts-loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl p-6 border border-gray-200 animate-pulse flex flex-col justify-between h-64 shadow-sm">
        <div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div class="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-1/4"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 border border-gray-200 animate-pulse flex flex-col justify-between h-64 shadow-sm">
        <div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div class="h-6 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-4/5"></div>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-1/4"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 border border-gray-200 animate-pulse flex flex-col justify-between h-64 shadow-sm">
        <div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div class="h-6 bg-gray-200 rounded w-4/5 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-1/4"></div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="posts-empty" class="hidden bg-white rounded-2xl p-12 text-center max-w-xl mx-auto my-12 border border-gray-200 shadow-md">
    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4M5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
        </svg>
    </div>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak Ada Artikel</h3>
    <p id="empty-message" class="text-gray-500 mb-6">Belum ada artikel yang dipublikasikan di platform ini.</p>
    <a href="/posts/create" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow hover:bg-blue-700 transition-colors duration-200">Tulis Artikel Pertama</a>
</div>

<!-- Posts Grid -->
<div id="posts-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

<!-- Pagination -->
<div id="posts-pagination" class="hidden flex items-center justify-center gap-4 mt-12 border-t border-gray-200 pt-8">
    <button id="btn-prev" onclick="goToPrevPage()" class="p-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-600 transition-all duration-200 disabled:opacity-30 disabled:pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <span id="page-num" class="text-sm font-semibold tracking-wider text-gray-700">Halaman 1 dari 1</span>
    <button id="btn-next" onclick="goToNextPage()" class="p-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-600 transition-all duration-200 disabled:opacity-30 disabled:pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</div>

<!-- Delete Confirm Modal -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-600/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md border border-gray-200 p-6 relative overflow-hidden shadow-xl text-center">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Artikel?</h3>
        <p id="delete-message" class="text-gray-500 mb-6">Tindakan ini akan menghapus artikel secara permanen.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-600 transition-colors duration-150">Batal</button>
            <button id="btn-confirm-delete" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200">Hapus</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let totalPages = 1;
    let isFilterMine = false;
    let postToDeleteId = null;

    document.addEventListener('DOMContentLoaded', () => {
        // Show flash messages if any
        const flashMessage = localStorage.getItem('flash_message');
        if (flashMessage) {
            showGlobalToast(flashMessage, 'success');
            localStorage.removeItem('flash_message');
        }

        // Determine filter from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        isFilterMine = urlParams.get('filter') === 'mine';

        if (isFilterMine) {
            // My Posts view requires login
            if (!auth.token) {
                window.location.href = '/login';
                return;
            }
            document.getElementById('page-title').textContent = 'Artikel Saya';
            document.getElementById('page-description').textContent = 'Kelola semua artikel draf dan artikel yang sudah Anda publikasikan.';
            document.getElementById('empty-message').textContent = 'Anda belum menulis artikel apapun saat ini.';
        }

        // Fetch posts
        loadPosts(1);
    });

    async function loadPosts(page = 1) {
        currentPage = page;
        const grid = document.getElementById('posts-grid');
        const loading = document.getElementById('posts-loading');
        const empty = document.getElementById('posts-empty');
        const pagination = document.getElementById('posts-pagination');

        loading.classList.remove('hidden');
        grid.classList.add('hidden');
        empty.classList.add('hidden');
        pagination.classList.add('hidden');

        try {
            const data = await runApiRequest(`/posts?page=${page}`);
            let posts = data.data || [];
            const meta = data.meta || { current_page: 1, last_page: 1 };

            if (isFilterMine) {
                // If filtering by mine, filter locally by owner ID
                posts = posts.filter(p => p.user_id === auth.id);
            }

            if (posts.length === 0) {
                empty.classList.remove('hidden');
            } else {
                renderGrid(posts);
                grid.classList.remove('hidden');
                
                totalPages = meta.last_page;
                document.getElementById('page-num').textContent = `Halaman ${meta.current_page} dari ${meta.last_page}`;
                document.getElementById('btn-prev').disabled = meta.current_page === 1;
                document.getElementById('btn-next').disabled = meta.current_page === meta.last_page;
                pagination.classList.remove('hidden');
            }
        } catch (err) {
            showGlobalToast('Gagal memuat artikel: ' + err.message, 'error');
        } finally {
            loading.classList.add('hidden');
        }
    }

    function renderGrid(posts) {
        const grid = document.getElementById('posts-grid');
        grid.innerHTML = '';

        posts.forEach(post => {
             const card = document.createElement('div');
             card.className = 'bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col justify-between h-72 relative overflow-hidden';
             
             const activeColor = post.status === 'published' ? 'bg-green-500' : 'bg-yellow-500';
             const isOwner = auth.token && post.user_id === auth.id;
 
             let actionButtons = '';
             if (isOwner) {
                 actionButtons = `
                     <div class="flex items-center gap-1.5">
                         <a href="/posts/${post.id}/edit" onclick="event.stopPropagation()" class="p-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-700 transition-colors duration-150" title="Edit Artikel">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                             </svg>
                         </a>
                         <button onclick="confirmDelete(${post.id}, '${escapeText(post.title)}', event)" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors duration-150" title="Hapus Artikel">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                             </svg>
                         </button>
                     </div>
                 `;
             }
 
             card.innerHTML = `
                 <div class="absolute top-0 left-0 w-full h-1 ${activeColor}"></div>
                 <div>
                     <div class="flex justify-between items-center mb-3">
                         <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-wider rounded-full bg-gray-100 text-gray-700 uppercase">${post.status}</span>
                         ${isOwner ? actionButtons : `<span class="text-[11px] text-gray-400 font-medium">Penulis ID: ${post.user_id}</span>`}
                     </div>
                     <h4 class="text-lg font-bold text-gray-800 mb-2 leading-tight line-clamp-2">${escapeText(post.title)}</h4>
                     <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">${escapeText(post.content)}</p>
                 </div>
                 <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-auto">
                     <a href="/posts/${post.id}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-all duration-200">
                         Baca Selengkapnya
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                         </svg>
                     </a>
                 </div>
             `;
             grid.appendChild(card);
        });
    }

    function goToNextPage() {
        if (currentPage < totalPages) {
            loadPosts(currentPage + 1);
        }
    }

    function goToPrevPage() {
        if (currentPage > 1) {
            loadPosts(currentPage - 1);
        }
    }

    function confirmDelete(id, title, event) {
        event.stopPropagation();
        postToDeleteId = id;
        document.getElementById('delete-message').textContent = `Apakah Anda yakin ingin menghapus artikel "${title}"? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('btn-confirm-delete').onclick = executeDelete;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        postToDeleteId = null;
    }

    async function executeDelete() {
        if (!postToDeleteId) return;

        try {
            const response = await runApiRequest(`/posts/${postToDeleteId}`, {
                method: 'DELETE'
            });

            showGlobalToast(response.message || 'Artikel berhasil dihapus.', 'success');
            closeDeleteModal();
            loadPosts(currentPage);
        } catch (err) {
            showGlobalToast('Gagal menghapus artikel: ' + err.message, 'error');
        }
    }
</script>
@endsection
