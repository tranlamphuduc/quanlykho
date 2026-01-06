<div class="sidebar">
    <div class="logo">
        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
        <h4>Quản Lý Kho</h4>
        <small>Đồ Gia Dụng</small>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
            <i class="bi bi-box"></i> Sản phẩm
        </a>
        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
            <i class="bi bi-tags"></i> Danh mục
        </a>
        <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
            <i class="bi bi-truck"></i> Nhà cung cấp
        </a>
        <a class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}">
            <i class="bi bi-building"></i> Kho hàng
        </a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 20px;">
        <a class="nav-link {{ request()->routeIs('stock-in.*') ? 'active' : '' }}" href="{{ route('stock-in.index') }}">
            <i class="bi bi-box-arrow-in-down"></i> Nhập kho
        </a>
        <a class="nav-link {{ request()->routeIs('stock-out.*') ? 'active' : '' }}" href="{{ route('stock-out.index') }}">
            <i class="bi bi-box-arrow-up"></i> Xuất kho
        </a>
        <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
            <i class="bi bi-clipboard-data"></i> Tồn kho
        </a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 20px;">
        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
            <i class="bi bi-bar-chart"></i> Báo cáo
        </a>
        @if(auth()->user()->role === 'admin')
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="bi bi-people"></i> Người dùng
        </a>
        @endif
    </nav>
</div>
