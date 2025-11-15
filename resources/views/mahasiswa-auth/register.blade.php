<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Peminjaman Barang Kampus</title>
    
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
    <div class="w-full max-w-lg">
        <!-- Register Card -->
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-8 pt-8 pb-4 text-center border-b">
                <h1 class="text-xl font-bold text-gray-800 mb-3 tracking-wide">PEMINJAMAN BARANG<br>KAMPUS</h1>
                
                <!-- Logo/Image -->
                <div class="mb-4">
                    <img src="/images/logo-kampus.png" alt="Logo Kampus" class="w-24 h-24 mx-auto object-contain">
                </div>
                
                <p class="text-sm text-gray-600 font-medium">Register New Account</p>
            </div>

            <!-- Form Section -->
            <div class="px-8 py-6">
            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('mahasiswa.register.store') }}" method="POST" class="space-y-3">
                @csrf
                
                <!-- NIM -->
                <div>
                    <input type="text" name="nim" id="nim" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Student ID (NIM)" value="{{ old('nim') }}">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Full Name" value="{{ old('nama_lengkap') }}">
                </div>

                <!-- Email -->
                <div>
                    <input type="email" name="email" id="email" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Email Address" value="{{ old('email') }}">
                </div>

                <!-- No Telepon -->
                <div>
                    <input type="tel" name="no_telp" id="no_telp" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Phone Number" value="{{ old('no_telp') }}">
                </div>

                <!-- Jurusan -->
                <div>
                    <input type="text" name="jurusan" id="jurusan"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Major (Optional)" value="{{ old('jurusan') }}">
                </div>

                <!-- Program Studi -->
                <div>
                    <input type="text" name="prodi" id="prodi"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Study Program (Optional)" value="{{ old('prodi') }}">
                </div>

                <!-- Password -->
                <div>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Password (min. 6 characters)">
                </div>

                <!-- Confirm Password -->
                <div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-gray-300 focus:border-transparent text-gray-700 placeholder-gray-400"
                           placeholder="Confirm Password">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-green-100 hover:bg-green-400 text-gray-800 py-3 rounded-md font-medium transition duration-200 mt-6">
                    Register
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center pb-2">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('mahasiswa.login') }}" class="text-gray-800 hover:text-gray-600 font-medium">
                        Log In
                    </a>
                </p>
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

