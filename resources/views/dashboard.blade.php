@extends('layouts.app')

@section('title', 'Dashboard')
@section('header')
<i class="bi bi-speedometer2 me-2"></i>Dashboard
@endsection

@section('content')
<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50">Sản phẩm</h6>
                    <h2 class="mb-0">{{ number_format($totalProducts ?? 0) }}</h2>
                </div>
                <i class="bi bi-box" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50">Danh mục</h6>
                    <h2 class="mb-0">{{ number_format($totalCategories ?? 0) }}</h2>
                </div>
                <i class="bi bi-tags" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50">Nhà cung cấp</h6>
                    <h2 class="mb-0">{{ number_format($totalSuppliers ?? 0) }}</h2>
                </div>
                <i class="bi bi-truck" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50">Giá trị tồn kho</h6>
                    <h2 class="mb-0">{{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }}đ</h2>
                </div>
                <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Biểu đồ -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Biểu đồ nhập xuất kho {{ date('Y') }}</div>
            <div class="card-body">
                <canvas id="stockChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-trophy me-2"></i>Top 5 sản phẩm xuất nhiều nhất</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topProducts ?? [] as $index => $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                            {{ $product->name }}
                        </div>
                        <span class="badge bg-success">{{ number_format($product->total_quantity) }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted">Chưa có dữ liệu</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Cảnh báo tồn kho thấp -->
<div class="card">
    <div class="card-header bg-danger text-white">
        <i class="bi bi-exclamation-triangle me-2"></i>Cảnh báo tồn kho thấp
    </div>
    <div class="card-body">
        @if(empty($lowStockProducts) || $lowStockProducts->isEmpty())
        <p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Không có sản phẩm nào dưới mức tồn kho tối thiểu.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Kho</th>
                        <th>Tồn kho</th>
                        <th>Tối thiểu</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $item)
                    <tr>
                        <td>{{ $item->product->code }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->warehouse->name }}</td>
                        <td><strong class="text-danger">{{ number_format($item->quantity) }}</strong></td>
                        <td>{{ number_format($item->product->min_stock) }}</td>
                        <td><span class="badge bg-danger">Cần nhập thêm</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const months = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
const stockInData = @json(isset($stockInStats) ? $stockInStats->pluck('total', 'month') : []);
const stockOutData = @json(isset($stockOutStats) ? $stockOutStats->pluck('total', 'month') : []);

new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            { label: 'Nhập kho', data: months.map((_, i) => stockInData[i + 1] || 0), backgroundColor: 'rgba(54, 162, 235, 0.7)' },
            { label: 'Xuất kho', data: months.map((_, i) => stockOutData[i + 1] || 0), backgroundColor: 'rgba(255, 99, 132, 0.7)' }
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('vi-VN') + 'đ' } } }
    }
});
</script>
@endpush
