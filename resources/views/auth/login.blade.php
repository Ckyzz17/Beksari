<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SMART BOOKS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f7f9fb] h-screen flex">
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

    <!-- Right Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl border shadow-sm">
            <h2 class="text-3xl font-bold text-[#191c1e] mb-2">Selamat datang kembali</h2>
            <p class="text-sm text-[#434655] mb-8">Masukkan detail akun untuk mengakses sistem SMART BOOKS.</p>

            @foreach(['info' => 'bg-blue-50 text-blue-800', 'success' => 'bg-emerald-50 text-emerald-800'] as $key => $cls)
                @if(session($key))
                    <div class="mb-4 p-3 rounded-lg text-sm {{ $cls }}">{{ session($key) }}</div>
                @endif
            @endforeach

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-[#004ac6]">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded text-[#004ac6]">
                        <span>Ingat saya</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-3.5 bg-[#2563eb] hover:bg-blue-700 text-white font-bold rounded-full transition shadow">
                    Masuk
                </button>
            </form>
            <div class="mt-6 text-sm text-gray-600 text-center">
                Belum punya akun? <a href="{{ route('register') }}" class="text-[#004ac6] font-semibold hover:underline">Daftar sebagai pelanggan</a>
            </div>
        </div>
    </div>
</body>
</html>
