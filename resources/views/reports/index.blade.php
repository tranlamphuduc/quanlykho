@extends('layouts.app')

@section('title', 'Báo cáo thống kê')
@section('header')
<i class="bi bi-bar-chart me-2"></i>Báo cáo thống kê
@endsection

@section('content')
<div class="d-flex justify-content-end mb-3">
    <select class="form-select" style="width: 120px;" onchange="location.href='?year='+this.value">
        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
    </select>
</div>

<!-- Tổng quan -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-primary">
            <h6 class="text-white-50">Tổng nhập kho {{ $year }}</h6>
            <h3 class="mb-0">{{ number_format($totalStockIn, 0, ',', '.') }}đ</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-success">
            <h6 class="text-white-50">Tổng xuất kho {{ $year }}</h6>
            <h3 class="mb-0">{{ number_format($totalStockOut, 0, ',', '.') }}đ</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-warning">
            <h6 class="text-white-50">Chênh lệch</h6>
            <h3 class="mb-0">{{ number_format($totalStockOut - $totalStockIn, 0, ',', '.') }}đ</h3>
        </div>
    </div>
</div>

<div class="row">
    <!-- Biểu đồ -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Biểu đồ nhập xuất theo tháng</div>
            <div class="card-body">
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Top 10 sản phẩm xuất nhiều nhất</div>
            <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr><th>#</th><th>Sản phẩm</th><th>SL</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->name }}</td>
                            <td><span class="badge bg-primary">{{ number_format($p->total_quantity) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center">Chưa có dữ liệu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cảnh báo tồn kho thấp -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>Sản phẩm tồn kho thấp ({{ $lowStockProducts->count() }})
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr><th>Mã SP</th><th>Tên</th><th>Kho</th><th>Tồn</th></tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $p)
                        <tr>
                            <td><code>{{ $p->product->code }}</code></td>
                            <td>{{ $p->product->name }}</td>
                            <td>{{ $p->warehouse->name }}</td>
                            <td><span class="badge bg-danger">{{ $p->quantity }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-success text-center">Không có sản phẩm nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cảnh báo tồn kho cao -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <i class="bi bi-exclamation-circle me-2"></i>Sản phẩm tồn kho cao ({{ $overStockProducts->count() }})
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr><th>Mã SP</th><th>Tên</th><th>Kho</th><th>Tồn</th></tr>
                    </thead>
                    <tbody>
                        @forelse($overStockProducts as $p)
                        <tr>
                            <td><code>{{ $p->product->code }}</code></td>
                            <td>{{ $p->product->name }}</td>
                            <td>{{ $p->warehouse->name }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $p->quantity }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-success text-center">Không có sản phẩm nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const months = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
const stockInData = @json($stockInStats->pluck('total', 'month'));
const stockOutData = @json($stockOutStats->pluck('total', 'month'));

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            { label: 'Nhập kho', data: months.map((_, i) => stockInData[i + 1] || 0), borderColor: 'rgba(54, 162, 235, 1)', backgroundColor: 'rgba(54, 162, 235, 0.1)', fill: true, tension: 0.3 },
            { label: 'Xuất kho', data: months.map((_, i) => stockOutData[i + 1] || 0), borderColor: 'rgba(255, 99, 132, 1)', backgroundColor: 'rgba(255, 99, 132, 0.1)', fill: true, tension: 0.3 }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('vi-VN') + 'đ' } } } }
});
</script>
@endpush
