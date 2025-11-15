<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug - Troubleshooting</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.3em;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #dc3545;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #28a745;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        pre {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            overflow-x: auto;
            font-size: 0.9em;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Session Debug & Troubleshooting</h1>
        
        @if(session('success'))
            <div class="success-box">
                <strong>✅ Success!</strong> {{ session('success') }}
            </div>
        @endif
        
        <div class="info-box">
            <strong>📍 Current Route:</strong> <code>{{ $current_route }}</code><br>
            <strong>🆔 Session ID:</strong> <code>{{ $session_id }}</code><br>
            <strong>📛 Session Name:</strong> <code>{{ $session_name }}</code><br>
            <strong>🎯 Expected Cookie:</strong> <code>{{ $expected_cookie }}</code>
            @if($session_name === $expected_cookie)
                <span class="badge badge-success">✓ Correct</span>
            @else
                <span class="badge badge-danger">✗ Wrong (should be {{ $expected_cookie }})</span>
            @endif
            <br><br>
            <strong>🔐 CSRF Token:</strong> <code>{{ substr($csrf_token, 0, 20) }}...</code><br>
            <strong>🔐 CSRF from Session:</strong> <code>{{ substr($csrf_token_from_session, 0, 20) }}...</code>
            @if($csrf_token === $csrf_token_from_session)
                <span class="badge badge-success">✓ Match</span>
            @else
                <span class="badge badge-danger">✗ Mismatch</span>
            @endif
            <br><br>
            <strong>📡 Request Method:</strong> <code>{{ $request_method }}</code><br>
            <strong>🌐 Request URL:</strong> <code>{{ $request_url }}</code>
        </div>

        <h2>Route Detection</h2>
        <table>
            <tr>
                <th>Route Type</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Admin Route</td>
                <td>
                    @if($is_admin_route)
                        <span class="badge badge-success">✓ Detected</span>
                    @else
                        <span class="badge badge-warning">✗ Not Detected</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Mahasiswa Route</td>
                <td>
                    @if($is_mahasiswa_route)
                        <span class="badge badge-success">✓ Detected</span>
                    @else
                        <span class="badge badge-warning">✗ Not Detected</span>
                    @endif
                </td>
            </tr>
        </table>

        <h2>Session Configuration</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Driver</td>
                <td><code>{{ $session_config['driver'] }}</code></td>
                <td>
                    @if($session_config['driver'] === 'file' || $session_config['driver'] === 'database')
                        <span class="badge badge-success">✓ OK</span>
                    @else
                        <span class="badge badge-warning">⚠ Check</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Default Cookie</td>
                <td><code>{{ $session_config['cookie'] }}</code></td>
                <td><span class="badge badge-info">Default</span></td>
            </tr>
            <tr>
                <td>Admin Cookie</td>
                <td><code>{{ $session_config['cookie_admin'] }}</code></td>
                <td>
                    @if($session_config['cookie_admin'] === 'admin_session')
                        <span class="badge badge-success">✓ OK</span>
                    @else
                        <span class="badge badge-danger">✗ Wrong</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Mahasiswa Cookie</td>
                <td><code>{{ $session_config['cookie_mahasiswa'] }}</code></td>
                <td>
                    @if($session_config['cookie_mahasiswa'] === 'mhs_session')
                        <span class="badge badge-success">✓ OK</span>
                    @else
                        <span class="badge badge-danger">✗ Wrong</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Lifetime (minutes)</td>
                <td><code>{{ $session_config['lifetime'] }}</code></td>
                <td><span class="badge badge-info">Info</span></td>
            </tr>
        </table>

        <h2>Cookies Detected</h2>
        @if(count($cookies) > 0)
            <table>
                <tr>
                    <th>Cookie Name</th>
                    <th>Value (Preview)</th>
                    <th>Status</th>
                </tr>
                @foreach($cookies as $name => $value)
                    <tr>
                        <td><code>{{ $name }}</code></td>
                        <td><code>{{ strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value }}</code></td>
                        <td>
                            @if($name === 'admin_session')
                                <span class="badge badge-success">✓ Admin Session</span>
                            @elseif($name === 'mhs_session')
                                <span class="badge badge-success">✓ Mahasiswa Session</span>
                            @elseif($name === 'laravel_session')
                                <span class="badge badge-warning">⚠ Default Session</span>
                            @elseif($name === 'XSRF-TOKEN')
                                <span class="badge badge-info">CSRF Token</span>
                            @else
                                <span class="badge badge-info">Other</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            @php
                $hasAdminSession = isset($cookies['admin_session']);
                $hasLaravelSession = isset($cookies['laravel_session']);
                $hasMahasiswaSession = isset($cookies['mhs_session']);
                $cookieCount = count($cookies);
            @endphp

            @if($is_admin_route)
                @if($hasAdminSession && !$hasLaravelSession)
                    <div class="success-box">
                        <strong>✅ Perfect!</strong> Only <code>admin_session</code> cookie detected. This is correct for admin routes.
                    </div>
                @elseif($hasAdminSession && $hasLaravelSession)
                    <div class="error-box">
                        <strong>❌ Problem Detected!</strong> Both <code>admin_session</code> and <code>laravel_session</code> are present. 
                        This means <code>SmartStartSession</code> middleware is still starting a session for admin routes.
                        <br><br>
                        <strong>Solution:</strong> Make sure <code>SmartStartSession</code> skips admin routes completely.
                    </div>
                @elseif(!$hasAdminSession && $hasLaravelSession)
                    <div class="error-box">
                        <strong>❌ Problem Detected!</strong> Only <code>laravel_session</code> cookie detected. 
                        <code>AdminSessionCookie</code> middleware is not working.
                        <br><br>
                        <strong>Solution:</strong> Check if <code>AdminSessionCookie</code> is registered in Filament middleware stack.
                    </div>
                @endif
            @endif

            @if($cookieCount > 3)
                <div class="warning-box">
                    <strong>⚠ Warning:</strong> Too many cookies detected ({{ $cookieCount }}). 
                    Expected: 2 cookies (<code>admin_session</code> or <code>mhs_session</code> + <code>XSRF-TOKEN</code>).
                </div>
            @endif
        @else
            <div class="warning-box">
                <strong>⚠ No cookies detected.</strong> This might be normal if you just cleared cookies.
            </div>
        @endif

        <h2>Session Data</h2>
        @if(count($session_data) > 0)
            <pre>{{ json_encode($session_data, JSON_PRETTY_PRINT) }}</pre>
        @else
            <div class="info-box">No session data found.</div>
        @endif

        <h2>Middleware Information</h2>
        <h3>Web Middleware Group</h3>
        <pre>{{ json_encode($middleware['web_middleware'] ?? [], JSON_PRETTY_PRINT) }}</pre>

        @if(isset($middleware['route_middleware']))
            <h3>Route Middleware</h3>
            <pre>{{ json_encode($middleware['route_middleware'], JSON_PRETTY_PRINT) }}</pre>
        @endif

        @if(config('session.driver') === 'file' && count($session_files) > 0)
            <h2>Session Files (Last 10)</h2>
            <table>
                <tr>
                    <th>File Name</th>
                </tr>
                @foreach($session_files as $file)
                    <tr>
                        <td><code>{{ $file }}</code></td>
                    </tr>
                @endforeach
            </table>
        @endif

        <div class="actions">
            <h2>Quick Actions</h2>
            <a href="{{ route('debug.session') }}" class="btn">🔄 Refresh</a>
            <a href="{{ route('debug.session.clear') }}" class="btn btn-danger" onclick="return confirm('Clear all session data?')">🗑️ Clear Session</a>
            <a href="/admin/login" class="btn btn-success">🔐 Test Admin Login</a>
            <a href="/mahasiswa/login" class="btn btn-success">👤 Test Mahasiswa Login</a>
        </div>

        <div class="info-box" style="margin-top: 30px;">
            <h3>📝 Troubleshooting Steps</h3>
            <ol style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Clear all browser cookies</strong> (F12 → Application → Cookies → Delete All)</li>
                <li><strong>Clear Laravel cache:</strong> <code>php artisan config:clear && php artisan cache:clear</code></li>
                <li><strong>Restart Laravel server</strong></li>
                <li><strong>Test login</strong> and check cookies again</li>
                <li>If still having issues, check middleware registration in <code>bootstrap/app.php</code> and <code>app/Providers/Filament/AdminPanelProvider.php</code></li>
            </ol>
        </div>
    </div>
</body>
</html>

