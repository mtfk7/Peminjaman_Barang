@extends('mahasiswa.layout')

@section('title', 'Dashboard')

@section('content')
<!-- Hero Section -->
<div class="px-4 py-6 text-white" style="background-color: #375e2f;">
    <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ $mahasiswa->nama_lengkap }}! </h1>
    <p class="text-white/90 text-sm">Pinjam peralatan yang Anda butuhkan dengan mudah</p>
</div>

<!-- Quick Actions -->
<div class="px-4 py-4 bg-white border-b">
    <div class="flex gap-3">
        <a href="{{ route('mahasiswa.create') }}" class="flex-1 text-white px-4 py-3 rounded-lg text-center font-semibold shadow-md hover:shadow-lg transition" style="background-color: #375e2f;">
            <i class="fas fa-shopping-cart mr-2"></i>View Cart
        </a>
        <a href="{{ route('mahasiswa.history') }}" class="flex-1 text-white px-4 py-3 rounded-lg text-center font-semibold shadow-md hover:shadow-lg transition" style="background-color: #4a7c59;">
            <i class="fas fa-history mr-2"></i>History
        </a>
    </div>
</div>

<!-- Filter Buttons -->
<div class="px-4 py-3 bg-white border-b sticky top-0 z-20">
    <div class="flex gap-2 overflow-x-auto">
        <div class="relative">
            <button class="filter-btn" id="availabilityFilter" onclick="toggleFilter('availability')">
                <span id="availabilityText">Availability</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div id="availabilityDropdown" class="hidden fixed bg-white border border-gray-200 rounded-lg shadow-xl z-50 min-w-[150px]" style="display: none;">
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 rounded-t-lg" onclick="applyFilter('availability', 'all')">All</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50" onclick="applyFilter('availability', 'available')">Available</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 rounded-b-lg" onclick="applyFilter('availability', 'unavailable')">Unavailable</button>
            </div>
        </div>
        <div class="relative">
            <button class="filter-btn" id="categoryFilter" onclick="toggleFilter('category')">
                <span id="categoryText">Category</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div id="categoryDropdown" class="hidden fixed bg-white border border-gray-200 rounded-lg shadow-xl z-50 min-w-[150px]" style="display: none;">
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 rounded-t-lg" onclick="applyFilter('category', 'all')">All</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50" onclick="applyFilter('category', 'habis_pakai')">Consumable</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 rounded-b-lg" onclick="applyFilter('category', 'tidak_habis_pakai')">Non-consumable</button>
            </div>
        </div>
        <button class="filter-btn" onclick="clearAllFilters()">
            <span>Clear Filters</span>
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
</div>

<!-- Items List -->
<div id="itemsList">
    @php
        $allItems = collect();
        
        // Combine both types of items
        foreach($barangHabis as $item) {
            $allItems->push([
                'id' => $item->id,
                'type' => 'habis_pakai',
                'name' => $item->nama_barang,
                'code' => $item->kode_barang,
                'stock' => $item->total_stok,
                'available' => $item->total_stok > 0,
                'satuan' => $item->satuan
            ]);
        }
        
        foreach($barangTidakHabis as $item) {
            $allItems->push([
                'id' => $item->id,
                'type' => 'tidak_habis_pakai',
                'name' => $item->nama_barang,
                'code' => $item->kode_barang,
                'stock' => $item->total_stok,
                'available' => $item->total_stok > 0,
                'satuan' => $item->satuan
            ]);
        }
    @endphp

    @forelse($allItems as $item)
    <div class="item-card" 
         data-name="{{ strtolower($item['name']) }}" 
         data-available="{{ $item['available'] ? 'true' : 'false' }}"
         data-category="{{ $item['type'] }}"
         data-stock="{{ $item['stock'] }}">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-base mb-1">{{ $item['name'] }}</h3>
                <p class="text-xs text-gray-400 mb-2">Code: {{ $item['code'] }}</p>
                <div class="flex items-center gap-2">
                    @if($item['available'])
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Available
                        </span>
                        <span class="text-xs text-gray-500">{{ $item['stock'] }} {{ $item['satuan'] }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Unavailable
                        </span>
                    @endif
                </div>
            </div>
            <div class="ml-3">
                @if($item['available'])
                    <form action="{{ route('mahasiswa.add-to-cart') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="barang_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="jenis_barang" value="{{ $item['type'] }}">
                        <input type="hidden" name="jumlah" value="1">
                        <button type="submit" 
                                class="text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-md hover:shadow-lg" style="background-color: #375e2f;">
                            <i class="fas fa-cart-plus mr-1"></i>Add
                        </button>
                    </form>
                @else
                    <button disabled class="bg-gray-200 text-gray-400 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                        <i class="fas fa-ban mr-1"></i>Unavailable
                    </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="p-8 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
        <p>No equipment available</p>
    </div>
    @endforelse
</div>

<!-- Empty State (Hidden by default) -->
<div id="emptyState" class="hidden p-8 text-center text-gray-500">
    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-filter text-3xl text-gray-400"></i>
    </div>
    <p class="text-lg font-semibold mb-2">No items found</p>
    <p class="text-sm text-gray-400">Try adjusting your filters</p>
</div>
@endsection

@section('scripts')
<script>
    let currentFilters = {
        availability: 'all',
        category: 'all'
    };

    // Toggle filter dropdown
    function toggleFilter(type) {
        const dropdown = document.getElementById(type + 'Dropdown');
        const button = document.getElementById(type + 'Filter');
        const otherDropdowns = document.querySelectorAll('[id$="Dropdown"]');
        
        // Close other dropdowns
        otherDropdowns.forEach(d => {
            if (d.id !== type + 'Dropdown') {
                d.classList.add('hidden');
                d.style.display = 'none';
            }
        });
        
        // Toggle current dropdown
        if (dropdown.classList.contains('hidden') || dropdown.style.display === 'none') {
            // Show dropdown
            const rect = button.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + window.scrollY + 4) + 'px';
            dropdown.style.left = (rect.left + window.scrollX) + 'px';
            dropdown.classList.remove('hidden');
            dropdown.style.display = 'block';
        } else {
            // Hide dropdown
            dropdown.classList.add('hidden');
            dropdown.style.display = 'none';
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative') && !e.target.closest('[id$="Dropdown"]')) {
            document.querySelectorAll('[id$="Dropdown"]').forEach(d => {
                d.classList.add('hidden');
                d.style.display = 'none';
            });
        }
    });
    
    // Close dropdowns on scroll
    window.addEventListener('scroll', function() {
        document.querySelectorAll('[id$="Dropdown"]').forEach(d => {
            d.classList.add('hidden');
            d.style.display = 'none';
        });
    });

    // Apply filter
    function applyFilter(type, value) {
        currentFilters[type] = value;
        
        // Update button text
        const textElement = document.getElementById(type + 'Text');
        if (type === 'availability') {
            textElement.textContent = value === 'all' ? 'Availability' : 
                                     value === 'available' ? 'Available' : 'Unavailable';
        } else if (type === 'category') {
            textElement.textContent = value === 'all' ? 'Category' : 
                                     value === 'habis_pakai' ? 'Consumable' : 'Non-consumable';
        }
        
        // Close dropdown
        const dropdown = document.getElementById(type + 'Dropdown');
        dropdown.classList.add('hidden');
        dropdown.style.display = 'none';
        
        // Apply filters
        applyFilters();
    }

    // Clear all filters
    function clearAllFilters() {
        currentFilters = {
            availability: 'all',
            category: 'all'
        };
        
        document.getElementById('availabilityText').textContent = 'Availability';
        document.getElementById('categoryText').textContent = 'Category';
        
        applyFilters();
    }

    // Apply all filters
    function applyFilters() {
        const items = document.querySelectorAll('.item-card');
        let visibleCount = 0;

        items.forEach(item => {
            let show = true;

            // Availability filter
            if (currentFilters.availability !== 'all' && show) {
                const available = item.dataset.available === 'true';
                if (currentFilters.availability === 'available' && !available) {
                    show = false;
                } else if (currentFilters.availability === 'unavailable' && available) {
                    show = false;
                }
            }

            // Category filter
            if (currentFilters.category !== 'all' && show) {
                const category = item.dataset.category;
                if (category !== currentFilters.category) {
                    show = false;
                }
            }

            // Show/hide item
            if (show) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide empty state
        document.getElementById('emptyState').classList.toggle('hidden', visibleCount > 0);
        document.getElementById('itemsList').classList.toggle('hidden', visibleCount === 0);
    }
</script>
@endsection
