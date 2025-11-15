<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Dashboard')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 70px; /* Space for bottom nav */
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e0e0e0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            cursor: pointer;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 60%;
            height: 3px;
            background: #375e2f;
            border-radius: 0 0 3px 3px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-item:hover {
            color: #375e2f;
            transform: translateY(-2px);
        }
        
        .nav-item.active {
            color: #375e2f;
        }
        
        .nav-item.active::before {
            transform: translateX(-50%) scaleX(1);
        }
        
        .nav-item i {
            font-size: 24px;
            margin-bottom: 4px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-item.active i {
            transform: scale(1.1);
        }
        
        .nav-item span {
            font-size: 12px;
            transition: font-weight 0.3s;
        }
        
        .nav-item.active span {
            font-weight: 600;
        }
        
        /* Page transition animations */
        .page-content {
            transition: opacity 0.3s ease-in, transform 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Loading indicator */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        
        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .filter-btn {
            border: 1px solid #ddd;
            background: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .filter-btn:hover {
            background: #f5f5f5;
        }
        
        .filter-btn:active {
            background: #e5e5e5;
        }
        
        .item-card {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 16px;
            transition: all 0.2s;
        }
        
        .item-card:hover {
            background: #f9fafb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .item-card:active {
            background: #f5f5f5;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Top Header -->
    <div class="bg-white border-b">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center space-x-3">
                <img src="/images/logo-kampus.png" alt="Logo" class="w-10 h-10 object-contain">
                <span class="font-semibold text-gray-800">Dashboard</span>
            </div>
            <button onclick="toggleMenu()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-cog text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Settings Menu (Hidden by default) -->
    <div id="settingsMenu" class="hidden bg-white border-b shadow-sm">
        <div class="px-4 py-2">
            <a href="{{ route('mahasiswa.profile') }}" class="block py-2 text-gray-700 hover:text-green-600">
                <i class="fas fa-user mr-2"></i> Profile
            </a>
            <form action="{{ route('mahasiswa.logout') }}" method="POST" class="inline w-full">
                @csrf
                <button type="submit" class="block w-full text-left py-2 text-gray-700 hover:text-red-600">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="px-4 mt-3">
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded text-sm">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="px-4 mt-3">
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm">
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <!-- Main Content -->
    <main id="mainContent" class="page-content">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <div class="flex">
            <a href="{{ route('mahasiswa.index') }}" 
               data-route="mahasiswa.index"
               class="nav-item {{ request()->routeIs('mahasiswa.index') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('mahasiswa.history') }}" 
               data-route="mahasiswa.history"
               class="nav-item {{ request()->routeIs('mahasiswa.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>History</span>
            </a>
            <a href="{{ route('mahasiswa.profile') }}" 
               data-route="mahasiswa.profile"
               class="nav-item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </div>
    </nav>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('settingsMenu');
            menu.classList.toggle('hidden');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('settingsMenu');
            const button = event.target.closest('button');
            
            if (!button && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // SPA Navigation
        (function() {
            const mainContent = document.getElementById('mainContent');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const navItems = document.querySelectorAll('.nav-item[data-route]');
            
            // Routes that should use SPA navigation
            const spaRoutes = ['mahasiswa.index', 'mahasiswa.history', 'mahasiswa.profile'];
            
            // Handle navigation clicks
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    const route = this.getAttribute('data-route');
                    const href = this.getAttribute('href');
                    
                    // Only intercept if it's a SPA route
                    if (spaRoutes.includes(route)) {
                        e.preventDefault();
                        navigateTo(href, route);
                    }
                });
            });
            
            // Navigation function
            async function navigateTo(url, route) {
                // Show loading
                loadingOverlay.classList.add('active');
                
                // Update active nav item
                updateActiveNav(route);
                
                try {
                    // Fetch new content
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        redirect: 'follow' // Follow redirects normally
                    });
                    
                    // Check if unauthorized (401) or forbidden (403)
                    if (response.status === 401 || response.status === 403) {
                        window.location.href = '{{ route("mahasiswa.login") }}';
                        return;
                    }
                    
                    if (!response.ok && response.status !== 200) {
                        // If not ok and not 200, check if it's a redirect to login
                        if (response.redirected && response.url.includes('/login')) {
                            window.location.href = response.url;
                            return;
                        }
                        throw new Error('Failed to load page');
                    }
                    
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Check if the response is a login page (only check body content, not title)
                    const bodyContent = doc.querySelector('body')?.innerHTML || '';
                    const hasLoginForm = bodyContent.includes('Student ID (NIM)') || 
                                        bodyContent.includes('mahasiswa-auth/login') ||
                                        (bodyContent.includes('Login') && bodyContent.includes('Password') && bodyContent.includes('NIM'));
                    
                    // Only redirect if we're sure it's a login page AND we're not already on a valid page
                    if (hasLoginForm && !bodyContent.includes('mahasiswa.layout')) {
                        window.location.href = '{{ route("mahasiswa.login") }}';
                        return;
                    }
                    
                    // Extract content
                    const newContent = doc.querySelector('main') || doc.querySelector('body');
                    if (!newContent) {
                        throw new Error('No content found');
                    }
                    
                    const newTitle = doc.querySelector('title')?.textContent || document.title;
                    
                    // Extract alert messages if any
                    const alerts = doc.querySelectorAll('.px-4.mt-3');
                    
                    // Fade out current content
                    mainContent.style.opacity = '0';
                    mainContent.style.transform = 'translateY(-10px)';
                    
                    await new Promise(resolve => setTimeout(resolve, 150));
                    
                    // Update content
                    mainContent.innerHTML = newContent.innerHTML;
                    document.title = newTitle;
                    
                    // Add alert messages if any (before main content)
                    if (alerts.length > 0) {
                        alerts.forEach(alert => {
                            mainContent.insertBefore(alert.cloneNode(true), mainContent.firstChild);
                        });
                    }
                    
                    // Update URL without reload
                    window.history.pushState({ route: route }, newTitle, url);
                    
                    // Re-initialize scripts in new content
                    reinitializeScripts();
                    
                    // Fade in new content
                    mainContent.style.opacity = '1';
                    mainContent.style.transform = 'translateY(0)';
                    
                } catch (error) {
                    console.error('Navigation error:', error);
                    // Fallback to normal navigation
                    window.location.href = url;
                } finally {
                    // Hide loading
                    loadingOverlay.classList.remove('active');
                }
            }
            
            // Update active navigation item
            function updateActiveNav(route) {
                navItems.forEach(item => {
                    if (item.getAttribute('data-route') === route) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
            
            // Re-initialize scripts in new content
            function reinitializeScripts() {
                // Re-run any scripts in the new content
                const scripts = mainContent.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => {
                        newScript.setAttribute(attr.name, attr.value);
                    });
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
                
                // Trigger any custom events if needed
                window.dispatchEvent(new Event('contentLoaded'));
            }
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(e) {
                const url = window.location.pathname + window.location.search;
                const route = getRouteFromUrl(url);
                if (route && spaRoutes.includes(route)) {
                    navigateTo(url, route);
                } else {
                    window.location.reload();
                }
            });
            
            // Get route from URL
            function getRouteFromUrl(url) {
                if (url.includes('/mahasiswa/profile')) return 'mahasiswa.profile';
                if (url.includes('/mahasiswa/history')) return 'mahasiswa.history';
                if (url.includes('/mahasiswa') && !url.includes('/pinjam') && !url.includes('/peminjaman')) return 'mahasiswa.index';
                return null;
            }
            
            // Intercept form submissions that redirect to SPA routes
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.tagName === 'FORM' && form.method === 'POST') {
                    // Let forms submit normally, they will redirect
                    // But we can add loading indicator
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
                    }
                }
            });
        })();
    </script>

    @yield('scripts')
</body>
</html>

