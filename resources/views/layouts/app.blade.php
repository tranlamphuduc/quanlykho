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
        :root { --sidebar-width: 220px; }
        * { box-sizing: border-box; }
        html { font-size: 13px; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f6f9; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            padding-top: 10px; z-index: 1000; overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .sidebar .logo { color: #fff; text-align: center; padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; }
        .sidebar .logo h4 { margin: 0; font-weight: 600; font-size: 1rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 8px 12px; margin: 2px 6px; border-radius: 6px; transition: all 0.3s; font-size: 0.85rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar .nav-link i { margin-right: 6px; width: 16px; }
        .main-content { margin-left: var(--sidebar-width); padding: 12px; min-height: 100vh; }
        .top-navbar { background: #fff; padding: 10px 15px; margin: -12px -12px 12px -12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
        .top-navbar h4 { font-size: 1rem; margin: 0; }
        .card { border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .card-header { background: #fff; border-bottom: 1px solid #eee; font-weight: 600; padding: 10px 12px; font-size: 0.9rem; }
        .card-body { padding: 12px; }
        .stat-card { border-radius: 10px; padding: 12px; color: #fff; }
        .stat-card h6 { font-size: 0.7rem; margin-bottom: 4px; }
        .stat-card h3 { font-size: 1.1rem; margin: 0; }
        .stat-card.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .stat-card.bg-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%) !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .table { font-size: 0.8rem; margin-bottom: 0; }
        .table th { background: #f8f9fa; font-weight: 600; padding: 8px; }
        .table td { padding: 6px 8px; vertical-align: middle; }
        .btn { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .btn-sm { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
        .form-control, .form-select { font-size: 0.8rem; padding: 0.3rem 0.5rem; }
        .form-label { font-size: 0.75rem; margin-bottom: 0.2rem; }
        .alert { padding: 8px 12px; font-size: 0.85rem; }
        .badge { font-size: 0.7rem; }
        .menu-toggle { display: none; position: fixed; top: 8px; left: 8px; z-index: 1100; background: #2c3e50; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; }
        .filter-row { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 12px; }
        .filter-row .form-control, .filter-row .form-select { max-width: 180px; }
        .modal-body { font-size: 0.85rem; }
        .modal-body .form-label { font-size: 0.8rem; }
        /* Ẩn cảnh báo autocomplete của Chrome */
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px white inset !important; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); border-color: #667eea; }
        
        @media (max-width: 992px) {
            :root { --sidebar-width: 200px; }
        }
        @media (max-width: 768px) {
            html { font-size: 12px; }
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); width: 240px; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 8px; padding-top: 45px; }
            .top-navbar { padding: 8px 10px; margin: -8px -8px 8px -8px; }
            .filter-row .form-control, .filter-row .form-select { max-width: 140px; }
        }
        @media (max-width: 576px) {
            .filter-row { flex-direction: column; align-items: stretch; }
            .filter-row .form-control, .filter-row .form-select { max-width: 100%; }
            .table-responsive { font-size: 0.7rem; }
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
