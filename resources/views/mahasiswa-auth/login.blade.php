<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Peminjaman Barang Kampus</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #375E2F;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-8 pt-8 pb-4 text-center border-b">
                <h1 class="text-xl font-bold text-gray-800 mb-3 tracking-wide">PEMINJAMAN BARANG<br>KAMPUS</h1>
                
                <!-- Logo/Image -->
               <!-- Logo/Image -->
<div class="mb-4">
    <img src="/images/logo-kampus.png" alt="Logo Kampus" class="w-24 h-24 mx-auto object-contain">
</div>
            </div>

            <!-- Form Section -->
            <div class="px-8 py-6">
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
                @endif

            <form action="{{ route('mahasiswa.login.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Student ID (NIM) -->
                <div>
                    <input type="text" name="nim" id="nim" required autofocus
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400 @error('nim') border-red-500 @enderror"
                           placeholder="Student ID (NIM)" value="{{ old('nim') }}">
                    @error('nim')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400 @error('password') border-red-500 @enderror"
                           placeholder="Password">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-green-100 hover:bg-green-400 text-gray-800 py-3 rounded-md font-medium transition duration-200 mt-6">
                    Log In
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center pb-2">
                <a href="{{ route('mahasiswa.register') }}" class="text-gray-800 hover:text-gray-600 text-sm font-medium">
                    Register
                </a>
            </div>
            </div>

            <!-- Footer Disclaimer -->
            <div class="px-8 py-4 bg-gray-50 border-t">
                <p class="text-xs text-gray-500 text-center leading-relaxed">
                    By continuing, you agree to our Terms of Service and Privacy Policy
                </p>
            </div>
        </div>
    </div>
</body>
</html>

