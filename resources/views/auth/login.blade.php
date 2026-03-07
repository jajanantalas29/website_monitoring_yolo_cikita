<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Deteksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1 { font-family: 'Oswald', sans-serif; } 
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-gray-900 h-screen w-full flex items-center justify-center relative overflow-hidden">

    <div class="absolute inset-0 z-0">
        <img src="{{ asset('img/bg_login.png') }}" 
             class="w-full h-full object-cover opacity-60" 
             alt="Background Login">
        
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-6">
        
        <h1 class="text-white text-5xl text-center mb-10 tracking-wide font-medium">Login</h1>

        <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-white text-lg mb-2 font-medium">Username</label>
                <input type="text" name="username" 
                       class="w-full border-2 border-gray-400 bg-transparent rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-white transition glass-input"
                       placeholder="Masukan username anda..">
            </div>

            <div>
                <label class="block text-white text-lg mb-2 font-medium">Password</label>
                <input type="password" name="password" 
                       class="w-full border-2 border-gray-400 bg-transparent rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-white transition glass-input"
                       placeholder="Masukan password anda..">
            </div>

            @error('username')
                <p class="text-red-400 text-sm text-center bg-black/30 p-2 rounded">{{ $message }}</p>
            @enderror

            <div class="pt-4">
                <button type="submit" class="w-full bg-white text-black font-bold text-xl py-3 rounded-lg hover:bg-gray-200 transition shadow-lg transform active:scale-95">
                    Masuk
                </button>
            </div>

        </form>
    </div>

</body>
</html>