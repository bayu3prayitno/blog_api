@extends('layouts.app')

@section('title', 'Detail Artikel')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="/posts" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-400 hover:text-cyan-400 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        Kembali ke Daftar
    </a>

    <!-- Skeleton Loader for Detail -->
    <div id="detail-loading" class="glass-panel rounded-3xl p-8 border border-darkBorder animate-pulse mb-8 h-80 flex flex-col justify-between">
        <div>
            <div class="h-4 bg-slate-800 rounded w-16 mb-4"></div>
            <div class="h-8 bg-slate-800 rounded w-2/3 mb-4"></div>
            <div class="h-4 bg-slate-800 rounded w-1/4 mb-8"></div>
            <div class="space-y-3">
                <div class="h-4 bg-slate-800 rounded w-full"></div>
                <div class="h-4 bg-slate-800 rounded w-full"></div>
                <div class="h-4 bg-slate-800 rounded w-5/6"></div>
            </div>
        </div>
    </div>

    <!-- Article Content card -->
    <article id="detail-panel" class="hidden glass-panel rounded-3xl p-8 border border-darkBorder/60 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-cyan-500 to-indigo-600"></div>
        <div class="flex items-center justify-between mb-4">
            <span id="detail-status" class="px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wider"></span>
            
            <!-- Owner Actions -->
            <div id="owner-actions" class="hidden flex items-center gap-2">
                <a id="btn-edit" href="#" class="p-2 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 transition-all duration-200" title="Edit Artikel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </a>
                <button id="btn-delete" class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-all duration-200" title="Hapus Artikel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <h1 id="detail-title" class="text-4xl font-extrabold text-white tracking-tight mb-4">Title</h1>
        
        <div class="flex items-center gap-2 mb-8 text-sm text-slate-400">
            <span>Penulis:</span>
            <span id="detail-author" class="font-semibold text-slate-300">User #ID</span>
        </div>

        <div id="detail-content" class="text-slate-300 leading-relaxed text-lg whitespace-pre-wrap border-t border-darkBorder/40 pt-6"></div>
    </article>

    <!-- Comments Block -->
    <section class="glass-panel rounded-3xl p-8 border border-darkBorder/60 mb-12">
        <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
            Komentar
        </h3>

        <!-- Comment Input form -->
        <div id="comment-form-container" class="mb-8 hidden">
            <form onsubmit="handlePostComment(event)" class="flex gap-4">
                <input type="text" id="comment-input" placeholder="Tulis komentar Anda di sini..." required class="flex-grow bg-slate-900 border border-darkBorder rounded-xl px-4 py-3 text-slate-300 placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-colors duration-200">
                <button type="submit" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl shadow-md transition-all duration-200">Kirim</button>
            </form>
        </div>
        <div id="comment-guest-msg" class="mb-8 p-4 rounded-2xl bg-indigo-950/20 border border-indigo-500/20 text-center text-slate-400">
            Harap <a href="/login" class="text-cyan-400 font-semibold hover:underline">Masuk</a> terlebih dahulu untuk menulis komentar.
        </div>

        <!-- Comments Feed -->
        <div id="comments-list" class="space-y-4"></div>
    </section>
</div>

<!-- Confirm Delete Post Modal -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl w-full max-w-md border border-darkBorder p-6 relative overflow-hidden shadow-2xl text-center">
        <div class="w-14 h-14 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Hapus Artikel?</h3>
        <p class="text-slate-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteModal()" class="px-5 py-2.5 rounded-xl border border-darkBorder hover:bg-slate-800 text-slate-400 hover:text-white transition-colors duration-150">Batal</button>
            <button id="btn-confirm-delete" class="px-6 py-2.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl shadow-md transition-all duration-200">Hapus</button>
        </div>
    </div>
</div>

<!-- Confirm Delete Comment Modal -->
<div id="delete-comment-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl w-full max-w-md border border-darkBorder p-6 relative overflow-hidden shadow-2xl text-center">
        <div class="w-14 h-14 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Hapus Komentar?</h3>
        <p class="text-slate-400 mb-6">Komentar ini akan dihapus permanen.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteCommentModal()" class="px-5 py-2.5 rounded-xl border border-darkBorder hover:bg-slate-800 text-slate-400 hover:text-white transition-colors duration-150">Batal</button>
            <button id="btn-confirm-delete-comment" class="px-6 py-2.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl shadow-md transition-all duration-200">Hapus</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const postId = parseInt("{{ $id }}");
    let currentPost = null;
    let commentToDeleteId = null;
    let allComments = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadPostDetail();
        loadPostComments();

        // Setup Comment Form Visibility
        const formContainer = document.getElementById('comment-form-container');
        const guestMsg = document.getElementById('comment-guest-msg');
        if (auth.token) {
            formContainer.classList.remove('hidden');
            guestMsg.classList.add('hidden');
        } else {
            formContainer.classList.add('hidden');
            guestMsg.classList.remove('hidden');
        }
    });

    async function loadPostDetail() {
        const loading = document.getElementById('detail-loading');
        const panel = document.getElementById('detail-panel');

        try {
            const response = await runApiRequest(`/posts/${postId}`);
            const post = response.data;
            currentPost = post;

            document.getElementById('detail-title').textContent = post.title;
            document.getElementById('detail-content').textContent = post.content;
            document.getElementById('detail-author').textContent = `User #${post.user_id}`;
            
            const status = document.getElementById('detail-status');
            status.textContent = post.status;
            if (post.status === 'published') {
                status.className = 'px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else {
                status.className = 'px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20';
            }

            // Show Owner Actions
            if (auth.token && post.user_id === auth.id) {
                document.getElementById('owner-actions').classList.remove('hidden');
                document.getElementById('btn-edit').href = `/posts/${post.id}/edit`;
                document.getElementById('btn-delete').onclick = confirmDeletePost;
            }

            loading.classList.add('hidden');
            panel.classList.remove('hidden');
        } catch (err) {
            showGlobalToast('Gagal memuat detail artikel: ' + err.message, 'error');
            setTimeout(() => window.location.href = '/posts', 2000);
        }
    }

    async function loadPostComments() {
        const commentsList = document.getElementById('comments-list');
        commentsList.innerHTML = `<div class="text-center py-4"><span class="text-slate-400 text-sm animate-pulse">Memuat komentar...</span></div>`;

        try {
            const response = await runApiRequest('/comments?page=1');
            allComments = response.data || [];
            renderComments();
        } catch (err) {
            commentsList.innerHTML = `<p class="text-red-400 text-sm italic">Gagal memuat komentar: ${err.message}</p>`;
        }
    }

    function renderComments() {
        const commentsList = document.getElementById('comments-list');
        commentsList.innerHTML = '';

        // Filter comments for active post
        const postComments = allComments.filter(c => c.post_id === postId);

        if (postComments.length === 0) {
            commentsList.innerHTML = `<p class="text-slate-500 text-sm italic">Belum ada komentar untuk artikel ini. Jadilah yang pertama memberikan tanggapan!</p>`;
            return;
        }

        postComments.forEach(comment => {
            const bubble = document.createElement('div');
            bubble.className = 'p-4 rounded-2xl bg-slate-900/50 border border-darkBorder/40 flex items-start justify-between gap-4 group hover:bg-slate-900 transition-all duration-200';
            
            const isOwner = auth.token && comment.user_id === auth.id;
            const deleteBtn = isOwner ? `
                <button onclick="confirmDeleteComment(${comment.id})" class="p-1 rounded-lg hover:bg-red-500/10 text-slate-500 hover:text-red-400 transition-colors duration-150" title="Hapus Komentar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            ` : '';

            bubble.innerHTML = `
                <div class="flex-grow">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-xs font-bold text-slate-300">User #${comment.user_id}</span>
                        ${isOwner ? '<span class="text-[9px] bg-cyan-500/10 text-cyan-400 font-semibold px-1.5 py-0.5 rounded-full">Anda</span>' : ''}
                    </div>
                    <p class="text-slate-300 text-sm whitespace-pre-wrap">${escapeText(comment.comment)}</p>
                </div>
                <div class="shrink-0">${deleteBtn}</div>
            `;
            commentsList.appendChild(bubble);
        });
    }

    async function handlePostComment(e) {
        e.preventDefault();
        const textInput = document.getElementById('comment-input');
        const text = textInput.value;

        try {
            const response = await runApiRequest('/comments', {
                method: 'POST',
                body: JSON.stringify({
                    comment: text,
                    post_id: postId
                })
            });

            showGlobalToast(response.message || 'Komentar dipublikasikan!', 'success');
            textInput.value = '';
            
            // Reload comments
            loadPostComments();
        } catch (err) {
            showGlobalToast('Gagal mengirim komentar: ' + err.message, 'error');
        }
    }

    // Delete post functions
    function confirmDeletePost() {
        document.getElementById('delete-modal').classList.remove('hidden');
        document.getElementById('btn-confirm-delete').onclick = executeDeletePost;
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    async function executeDeletePost() {
        try {
            const response = await runApiRequest(`/posts/${postId}`, {
                method: 'DELETE'
            });

            localStorage.setItem('flash_message', response.message || 'Artikel berhasil dihapus.');
            window.location.href = '/posts';
        } catch (err) {
            showGlobalToast('Gagal menghapus artikel: ' + err.message, 'error');
            closeDeleteModal();
        }
    }

    // Delete comment functions
    function confirmDeleteComment(id) {
        commentToDeleteId = id;
        document.getElementById('delete-comment-modal').classList.remove('hidden');
        document.getElementById('btn-confirm-delete-comment').onclick = executeDeleteComment;
    }

    function closeDeleteCommentModal() {
        document.getElementById('delete-comment-modal').classList.add('hidden');
        commentToDeleteId = null;
    }

    async function executeDeleteComment() {
        if (!commentToDeleteId) return;

        try {
            const response = await runApiRequest(`/comments/${commentToDeleteId}`, {
                method: 'DELETE'
            });

            showGlobalToast(response.message || 'Komentar dihapus.', 'success');
            closeDeleteCommentModal();
            loadPostComments();
        } catch (err) {
            showGlobalToast('Gagal menghapus komentar: ' + err.message, 'error');
            closeDeleteCommentModal();
        }
    }
</script>
@endsection
