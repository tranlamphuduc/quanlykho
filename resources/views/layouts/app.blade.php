<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý kho đồ gia dụng')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            padding-top: 20px; z-index: 1000; overflow-y: auto;
        }
        .sidebar .logo { color: #fff; text-align: center; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .logo h4 { margin: 0; font-weight: 600; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; margin: 2px 10px; border-radius: 8px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; }
        .main-content { margin-left: var(--sidebar-width); padding: 20px; min-height: 100vh; }
        .top-navbar { background: #fff; padding: 15px 25px; margin: -20px -20px 20px -20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card-header { background: #fff; border-bottom: 1px solid #eee; font-weight: 600; }
        .stat-card { border-radius: 15px; padding: 20px; color: #fff; }
        .stat-card.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .table th { background: #f8f9fa; font-weight: 600; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.sidebar')
    
    <div class="main-content">
        <div class="top-navbar">
            <h4 class="mb-0">@yield('header')</h4>
            <div class="d-flex align-items-center">
                <span class="me-3">Xin chào, <strong>{{ auth()->user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Khởi tạo DataTable với custom filter
            var table = $('.data-table').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json' },
                pageLength: 10,
                dom: 'rtip' // Ẩn search box mặc định của DataTables
            });
            
            // Live search - lọc trực tiếp không reload trang
            $('input[name="search"]').on('keyup', function() {
                table.search(this.value).draw();
            });
            
            // Lọc theo dropdown - tìm trong cột cụ thể
            $('select[name="category_id"]').on('change', function() {
                var val = $(this).find('option:selected').text();
                if ($(this).val() === '') val = '';
                table.column(2).search(val).draw(); // Cột danh mục thường ở vị trí 2
            });
            
            $('select[name="supplier_id"]').on('change', function() {
                var val = $(this).find('option:selected').text();
                if ($(this).val() === '') val = '';
                table.column(3).search(val).draw();
            });
            
            $('select[name="warehouse_id"]').on('change', function() {
                var val = $(this).find('option:selected').text();
                if ($(this).val() === '') val = '';
                table.column(3).search(val).draw();
            });
            
            $('select[name="status"]').on('change', function() {
                var val = '';
                if ($(this).val() === 'low') val = 'Tồn thấp';
                else if ($(this).val() === 'normal') val = 'Bình thường';
                else if ($(this).val() === 'over') val = 'Tồn cao';
                else if ($(this).val() === '1') val = 'Hoạt động';
                else if ($(this).val() === '0') val = 'Khóa';
                table.search(val).draw();
            });
            
            $('select[name="role"]').on('change', function() {
                var val = $(this).find('option:selected').text();
                if ($(this).val() === '') val = '';
                table.search(val).draw();
            });
            
            $('select[name="manager_id"]').on('change', function() {
                var val = $(this).find('option:selected').text();
                if ($(this).val() === '') val = '';
                table.search(val).draw();
            });
            
            // Nút xóa lọc - reset tất cả filter
            $('a[href*="index"], a.btn-secondary').on('click', function(e) {
                if ($(this).closest('.card-body form').length) {
                    e.preventDefault();
                    $('input[name="search"]').val('');
                    $('select[name="category_id"], select[name="supplier_id"], select[name="warehouse_id"], select[name="status"], select[name="role"], select[name="manager_id"]').val('');
                    table.search('').columns().search('').draw();
                }
            });
        });
        setTimeout(function() { $('.alert-dismissible').fadeOut('slow'); }, 3000);
    </script>
    @stack('scripts')
</body>
</html>
