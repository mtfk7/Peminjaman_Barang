@extends('mahasiswa.layout')

@section('title', 'Profile')

@section('content')
<!-- Profile Header -->
<div class="bg-white px-4 py-6 border-b">
    <div class="text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user text-green-600 text-3xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">{{ $mahasiswa->nama_lengkap }}</h2>
        <p class="text-sm text-gray-500">{{ $mahasiswa->nim }}</p>
    </div>
</div>

<!-- Profile Information -->
<div class="bg-white mt-2">
    <div class="px-4 py-3 border-b">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Personal Information</h3>
    </div>
    
    <div class="divide-y">
        <div class="px-4 py-3 flex justify-between">
            <span class="text-gray-600 text-sm">Email</span>
            <span class="text-gray-800 font-medium text-sm">{{ $mahasiswa->email }}</span>
        </div>
        <div class="px-4 py-3 flex justify-between">
            <span class="text-gray-600 text-sm">Phone</span>
            <span class="text-gray-800 font-medium text-sm">{{ $mahasiswa->no_telp }}</span>
        </div>
        @if($mahasiswa->jurusan)
        <div class="px-4 py-3 flex justify-between">
            <span class="text-gray-600 text-sm">Major</span>
            <span class="text-gray-800 font-medium text-sm">{{ $mahasiswa->jurusan }}</span>
        </div>
        @endif
        @if($mahasiswa->prodi)
        <div class="px-4 py-3 flex justify-between">
            <span class="text-gray-600 text-sm">Study Program</span>
            <span class="text-gray-800 font-medium text-sm">{{ $mahasiswa->prodi }}</span>
        </div>
        @endif
    </div>
</div>

<!-- Actions -->
<div class="bg-white mt-2">
    <div class="px-4 py-3 border-b">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Settings</h3>
    </div>
    
    <div class="divide-y">
        <button onclick="toggleEditModal()" class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50">
            <div class="flex items-center">
                <i class="fas fa-edit text-gray-400 mr-3"></i>
                <span class="text-gray-800 text-sm">Edit Profile</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </button>
        
        <button onclick="togglePasswordModal()" class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50">
            <div class="flex items-center">
                <i class="fas fa-key text-gray-400 mr-3"></i>
                <span class="text-gray-800 text-sm">Change Password</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </button>
        
        <form action="{{ route('mahasiswa.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                <div class="flex items-center">
                    <i class="fas fa-sign-out-alt text-red-500 mr-3"></i>
                    <span class="text-red-600 text-sm font-medium">Logout</span>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </button>
        </form>
    </div>
</div>

<!-- Edit Profile Modal (Hidden by default) -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end">
    <div class="bg-white w-full rounded-t-2xl p-6 pb-24 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Edit Profile</h3>
            <button onclick="toggleEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('mahasiswa.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="nama_lengkap" value="{{ $mahasiswa->nama_lengkap }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ $mahasiswa->email }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="tel" name="no_telp" value="{{ $mahasiswa->no_telp }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                <input type="text" name="jurusan" value="{{ $mahasiswa->jurusan }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Study Program</label>
                <input type="text" name="prodi" value="{{ $mahasiswa->prodi }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-md font-medium mb-4">
                Save Changes
            </button>
        </form>
    </div>
</div>

<!-- Change Password Modal (Hidden by default) -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end">
    <div class="bg-white w-full rounded-t-2xl p-6 pb-24">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Change Password</h3>
            <button onclick="togglePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('mahasiswa.password.change') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="new_password" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:ring-2 focus:ring-green-500">
            </div>
            
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-md font-medium mb-4">
                Change Password
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleEditModal() {
        document.getElementById('editModal').classList.toggle('hidden');
    }
    
    function togglePasswordModal() {
        document.getElementById('passwordModal').classList.toggle('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleEditModal();
        }
    });
    
    document.getElementById('passwordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            togglePasswordModal();
        }
    });
</script>
@endsection


