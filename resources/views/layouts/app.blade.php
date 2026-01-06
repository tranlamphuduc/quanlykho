<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý kho đồ gia dụng')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 270px; }
        * { box-sizing: border-box; }
        html { font-size: 16px; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f6f9; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            padding-top: 20px; z-index: 1000; overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .sidebar .logo { color: #fff; text-align: center; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .logo h4 { margin: 0; font-weight: 600; font-size: 1.25rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 4px 10px; border-radius: 10px; transition: all 0.3s; font-size: 1.05rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar .nav-link i { margin-right: 12px; width: 24px; font-size: 1.15rem; }
        .main-content { margin-left: var(--sidebar-width); padding: 25px; min-height: 100vh; }
        .top-navbar { background: #fff; padding: 18px 25px; margin: -25px -25px 25px -25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .top-navbar h4 { font-size: 1.35rem; margin: 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card-header { background: #fff; border-bottom: 1px solid #eee; font-weight: 600; padding: 16px 20px; font-size: 1.1rem; }
        .card-body { padding: 20px; }
        .stat-card { border-radius: 14px; padding: 22px; color: #fff; }
        .stat-card h6 { font-size: 0.95rem; margin-bottom: 8px; opacity: 0.9; }
        .stat-card h3 { font-size: 1.6rem; margin: 0; font-weight: 600; }
        .stat-card.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .stat-card.bg-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%) !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .table { font-size: 1rem; margin-bottom: 0; }
        .table th { background: #f8f9fa; font-weight: 600; padding: 14px 12px; }
        .table td { padding: 12px; vertical-align: middle; }
        .btn { font-size: 1rem; padding: 0.5rem 1rem; }
        .btn-sm { font-size: 0.9rem; padding: 0.4rem 0.75rem; }
        .form-control, .form-select { font-size: 1rem; padding: 0.6rem 0.9rem; }
        .form-label { font-size: 1rem; margin-bottom: 0.4rem; }
        .alert { padding: 14px 18px; font-size: 1rem; }
        .badge { font-size: 0.85rem; padding: 0.5em 0.75em; }
        .menu-toggle { display: none; position: fixed; top: 12px; left: 12px; z-index: 1100; background: #2c3e50; color: #fff; border: none; padding: 10px 14px; border-radius: 8px; font-size: 1.3rem; }
        .filter-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 18px; }
        .filter-row .form-control { max-width: 200px; }
        .filter-row .form-select { min-width: 180px; max-width: 280px; }
        .modal-body { font-size: 1rem; }
        .modal-body .form-label { font-size: 1rem; }
        .modal-title { font-size: 1.2rem; }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px white inset !important; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); border-color: #667eea; }
        
        @media (max-width: 992px) {
            :root { --sidebar-width: 250px; }
            html { font-size: 15px; }
        }
        @media (max-width: 768px) {
            html { font-size: 15px; }
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); width: 280px; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 18px; padding-top: 60px; }
            .top-navbar { padding: 14px 18px; margin: -18px -18px 18px -18px; }
            .filter-row .form-control { max-width: 160px; }
            .filter-row .form-select { min-width: 150px; max-width: 220px; }
        }
        @media (max-width: 576px) {
            .filter-row { flex-direction: column; align-items: stretch; }
            .filter-row .form-control, .filter-row .form-select { max-width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    @include('layouts.sidebar')
    
    <div class="main-content">
        <div class="top-navbar">
            <h4 class="mb-0">@yield('header')</h4>
            <div class="d-flex align-items-center gap-2">
                <small>{{ auth()->user()->name }}</small>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var dataTable;
        $(document).ready(function() {
            dataTable = $('.data-table').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json' },
                pageLength: 15,
                dom: 'rtip',
                responsive: true
            });
            
            // Live search
            $('input[name="search"]').on('keyup', function() {
                dataTable.search(this.value).draw();
            });
            
            // Dropdown filters
            $('select.filter-select').on('change', function() {
                var col = $(this).data('column');
                var val = $(this).val() ? $(this).find('option:selected').text() : '';
                if (col !== undefined) {
                    dataTable.column(col).search(val).draw();
                } else {
                    dataTable.search(val).draw();
                }
            });
        });
        
        setTimeout(function() { $('.alert-dismissible').fadeOut('slow'); }, 3000);
        
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const toggle = document.querySelector('.menu-toggle');
                if (sidebar && toggle && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
