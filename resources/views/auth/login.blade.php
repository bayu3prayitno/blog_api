@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="glass-panel rounded-3xl p-8 border border-darkBorder shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-500 to-indigo-600"></div>
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white mb-2">Selamat Datang</h2>
            <p class="text-slate-400 text-sm">Masuk untuk mengelola artikel dan menulis ulasan.</p>
        </div>
        
        <form onsubmit="handleLogin(event)" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-300 mb-2">Alamat Email</label>
                <input type="email" id="email" required class="w-full bg-slate-900 border border-darkBorder focus:border-cyan-500 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-600 focus:outline-none transition-colors duration-200" placeholder="nama@email.com">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">Kata Sandi</label>
                <input type="password" id="password" required class="w-full bg-slate-900 border border-darkBorder focus:border-cyan-500 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-600 focus:outline-none transition-colors duration-200" placeholder="••••••••">
            </div>
            
            <button type="submit" id="btn-login" class="w-full py-3.5 bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-bold tracking-wide rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-500/20 transform hover:-translate-y-0.5 transition-all duration-200">Masuk Sekarang</button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-slate-400">Belum punya akun? <a href="/register" class="text-cyan-400 hover:underline font-semibold">Daftar Akun Baru</a></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Redirect if already authenticated
        if (auth.token) {
            window.location.href = '/posts';
        }
    });

    async function handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await runApiRequest('/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            if (response.status === 'success') {
                const data = response.data;
                localStorage.setItem('jwt_token', data.token);
                localStorage.setItem('user_id', data.user_id);
                
                const parsedName = email.split('@')[0];
                const capitalizedName = parsedName.charAt(0).toUpperCase() + parsedName.slice(1);
                localStorage.setItem('user_name', capitalizedName);

                // Success message
                localStorage.setItem('flash_message', 'Login berhasil! Selamat datang kembali.');
                window.location.href = '/posts';
            }
        } catch (err) {
            showGlobalToast(err.message || 'Gagal login. Email atau password salah.', 'error');
        }
    }
</script>
@endsection
