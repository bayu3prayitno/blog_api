@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-md">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
            <p class="text-gray-500 text-sm">Masuk untuk mengelola artikel dan menulis ulasan.</p>
        </div>
        
        <form onsubmit="handleLogin(event)" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                <input type="email" id="email" required class="w-full bg-white border border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-colors duration-200" placeholder="nama@email.com">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                <input type="password" id="password" required class="w-full bg-white border border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-colors duration-200" placeholder="••••••••">
            </div>
            
            <button type="submit" id="btn-login" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold tracking-wide rounded-xl shadow transition-colors duration-200">Masuk Sekarang</button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Belum punya akun? <a href="/register" class="text-blue-600 hover:underline font-semibold">Daftar Akun Baru</a></p>
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
