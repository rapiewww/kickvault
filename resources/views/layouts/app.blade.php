<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RunnersVault - Modern POS & Inventory System</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --sidebar-width: 250px;
            --topbar-height: 68px;
            --primary-dark: #111315;
            --sidebar-bg: #111315;
            --sidebar-active-bg: #22262b;
            --sidebar-text: #9ca3af;
            --sidebar-heading: #6b7280;
            --accent-green: #10b981;
            --card-radius: 16px;
        }

        body {
            font-family: var(--app-font);
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Dark Sidebar Styling (Subcom Theme) */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid #1f2327;
            display: flex;
            flex-direction: column;
            z-index: 1030;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.35rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid #1f2327;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--accent-green);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            flex-shrink: 0;
        }

        .sidebar-nav {
            padding: 1.2rem 0.85rem;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sidebar-heading);
            padding: 0.75rem 0.75rem 0.35rem;
            margin-top: 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 0.9rem;
            color: var(--sidebar-text);
            font-weight: 600;
            font-size: 0.88rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0.3rem;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            transition: transform 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #1a1d21;
            color: #ffffff;
        }

        .sidebar-link:hover i {
            transform: translateX(2px);
            color: var(--accent-green);
        }

        .sidebar-link.active {
            background-color: var(--sidebar-active-bg) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .sidebar-link.active i {
            color: var(--accent-green);
        }

        .sidebar-footer {
            padding: 1rem 1rem;
            border-top: 1px solid #1f2327;
            background: var(--sidebar-bg);
        }

        /* Main Content Layout */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        .topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            position: sticky;
            top: 0;
            z-index: 1020;
            padding: 0 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .content-area {
            padding: 1.75rem;
            flex: 1;
        }

        /* Modern POS Card & Utility Styles */
        .pos-card {
            border-radius: var(--card-radius);
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .pos-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .btn-pos-dark {
            background-color: #111315;
            color: #ffffff;
            border: 1px solid #111315;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-pos-dark:hover {
            background-color: #1f2327;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(17, 19, 21, 0.2);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

@auth
    <!-- Dark Sidebar Navigation (Subcom Theme) -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ auth()->user()->role === 'customer' ? route('customer.catalog') : route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-white tracking-tight" style="font-size: 1.15rem; line-height: 1.2;">RunnersVault</div>
                <small class="text-secondary text-truncate d-block" style="font-size: 0.7rem;">Inventory & POS System</small>
            </div>
        </a>

        <div class="sidebar-nav">
            @if(auth()->user()->role === 'customer')
                <div class="nav-section-title">GENERAL</div>
                <a href="{{ route('customer.catalog') }}" class="sidebar-link {{ request()->routeIs('customer.catalog') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i>
                    <span>Katalog Belanja</span>
                </a>
                <a href="{{ route('customer.orders') }}" class="sidebar-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                    <i class="bi bi-bag-heart"></i>
                    <span>Pesanan Saya</span>
                </a>
            @else
                <div class="nav-section-title">GENERAL</div>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('sales.create') }}" class="sidebar-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i>
                    <span>POS / Kasir</span>
                </a>
                <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Product</span>
                </a>
                <a href="{{ route('purchases.index') }}" class="sidebar-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus"></i>
                    <span>Inventory (Stock-In)</span>
                </a>
                <a href="{{ route('sales.index') }}" class="sidebar-link {{ request()->routeIs('sales.index') || request()->routeIs('sales.show') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Sales History</span>
                </a>

                @if(auth()->user()->role === 'admin')
                    <div class="nav-section-title">ACCOUNT / ADMIN</div>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        <span>Suppliers</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                @endif
            @endif
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold" style="font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-1 px-2 border" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                        <span class="rounded-circle bg-success d-inline-block" style="width: 7px; height: 7px;"></span>
                        Open
                    </span>
                    <h5 class="fw-bold mb-0 text-dark ms-2 d-none d-sm-block">
                        @yield('page_title', 'RunnersVault Store')
                    </h5>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small d-none d-md-flex align-items-center gap-2 bg-light px-3 py-1-5 rounded-pill border">
                    <i class="bi bi-calendar3 text-secondary"></i>
                    <span>{{ date('l, d M Y') }}</span>
                </div>

                <div class="d-flex align-items-center gap-2 ps-2 border-start">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; font-size: 0.85rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark small lh-1">{{ auth()->user()->name }}</div>
                        <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-primary' : (auth()->user()->role === 'staff' ? 'bg-secondary' : 'bg-success') }} text-uppercase" style="font-size: 0.65rem; padding: 2px 7px;">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Perhatian:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
@else
    <!-- Guest Layout (Login & Register) -->
    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mx-auto" style="max-width: 500px;" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mx-auto" style="max-width: 500px;" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mx-auto mb-4" style="max-width: 500px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Memperkecil ikon SVG pagination secara otomatis agar rapi
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.pagination svg').forEach(svg => {
            svg.setAttribute('width', '16');
            svg.setAttribute('height', '16');
        });
    });
</script>

</body>
</html>
