@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-md">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar Akun</h2>
            <p class="text-gray-500 text-sm">Buat akun penulis Anda dan mulailah berkontribusi.</p>
        </div>
        
        <form onsubmit="handleRegister(event)" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" id="name" required class="w-full bg-white border border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-colors duration-200" placeholder="Budi Santoso">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                <input type="email" id="email" required class="w-full bg-white border border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-colors duration-200" placeholder="budi@email.com">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi (Min. 6 Karakter)</label>
                <input type="password" id="password" required minlength="6" class="w-full bg-white border border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-colors duration-200" placeholder="••••••••">
            </div>
            
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold tracking-wide rounded-xl shadow transition-colors duration-200">Daftar Akun</button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Sudah memiliki akun? <a href="/login" class="text-blue-600 hover:underline font-semibold">Masuk di sini</a></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (auth.token) {
            window.location.href = '/posts';
        }
    });

    async function handleRegister(e) {
        e.preventDefault();
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await runApiRequest('/register', {
                method: 'POST',
                body: JSON.stringify({ name, email, password })
            });

            if (response.token) {
                localStorage.setItem('jwt_token', response.token);
                localStorage.setItem('user_id', response.user.id);
                localStorage.setItem('user_name', response.user.name);

                localStorage.setItem('flash_message', 'Registrasi berhasil! Anda telah masuk otomatis.');
                window.location.href = '/posts';
            }
        } catch (err) {
            showGlobalToast(err.message || 'Pendaftaran gagal. Email mungkin sudah digunakan.', 'error');
        }
    }
</script>
@endsection
