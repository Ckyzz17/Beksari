<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SMART BOOKS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f7f9fb] min-h-screen flex">
    <!-- Left Illustration -->
    <div class="hidden lg:flex w-1/2 relative bg-white flex-col justify-between p-12 overflow-hidden border-r">
        <div class="z-10 flex items-center gap-3">
            <span class="text-2xl font-black text-[#004ac6]">SMART BOOKS</span>
        </div>
        <div class="absolute inset-0 bg-cover bg-center opacity-85" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCyd5_JK4MK22VnqWJbJm7pQgazZiFGfgFD_YI149U2JqsmwMfuOedaOozJa3UK7CIFGXWLLSXG7x_fnuVJ7WPC5ENjp3c70XGYAePUa4HsHzzlCwVTqp2Hrs-WF4NFYA-XFCFhbSYuPgq4rXbLghm6jBjoewGXqZh9-kPO_ZXjnQ-cknrvWNh7HGpMn5Bqk_LTMze_ixmwjncXqJoibRlWCLLhwXDhcCNzA5tKA1Q7Wa1ylfdbdPc');"></div>
        <div class="z-10 bg-white/90 backdrop-blur-md p-6 rounded-2xl border max-w-md shadow-sm">
            <p class="text-lg font-medium text-[#191c1e] mb-2">"A room without books is like a body without a soul."</p>
            <p class="text-sm text-[#434655] font-semibold">— Cicero</p>
        </div>
    </div>

    <!-- Right Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl border shadow-sm">
            <h2 class="text-3xl font-bold text-[#191c1e] mb-2">Buat akun</h2>
            <p class="text-sm text-[#434655] mb-6">Daftar untuk belanja buku di SMART BOOKS dan memantau pesananmu.</p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Nama lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ulangi password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <button type="submit" class="w-full py-3.5 bg-[#2563eb] hover:bg-blue-700 text-white font-bold rounded-full transition shadow">
                    Daftar
                </button>
            </form>
            <div class="mt-6 text-sm text-gray-600 text-center">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#004ac6] font-semibold hover:underline">Masuk</a>
            </div>
        </div>
    </div>
</body>
</html>
