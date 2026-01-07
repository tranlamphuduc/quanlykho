@extends('layouts.app')

@section('title', 'Báo cáo tồn kho')
@section('header')
<i class="bi bi-clipboard-data me-2"></i>Báo cáo tồn kho
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card bg-info">
            <h6 class="text-white-50">Tổng giá trị</h6>
            <h3>{{ number_format($totalValue ?? 0) }}đ</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-danger">
            <h6 class="text-white-50">Tồn thấp</h6>
            <h3>{{ $lowStockCount ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-success">
            <h6 class="text-white-50">Bình thường</h6>
            <h3>{{ $normalStockCount ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-warning">
            <h6 class="text-white-50">Tồn cao</h6>
            <h3>{{ $overStockCount ?? 0 }}</h3>
        </div>
    </div>
</div>

<div class="filter-row">
    <select id="filterWarehouse" class="form-select">
        <option value="">Tất cả kho</option>
        @foreach($warehouses as $wh)
        <option value="{{ $wh->name }}">{{ $wh->name }}</option>
        @endforeach
    </select>
    <select id="filterStatus" class="form-select">
        <option value="">Tất cả trạng thái</option>
        <option value="Tồn thấp">Tồn thấp</option>
        <option value="Bình thường">Bình thường</option>
        <option value="Tồn cao">Tồn cao</option>
    </select>
    <select id="filterCategory" class="form-select">
        <option value="">Tất cả danh mục</option>
        @foreach($warehouses->first() ? \App\Models\Category::all() : [] as $cat)
        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
        @endforeach
    </select>
    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm..." style="max-width:180px;">
    <button class="btn btn-outline-secondary" onclick="resetFilters()" title="Reset bộ lọc">
        <i class="bi bi-arrow-counterclockwise"></i>
    </button>
    <a href="{{ route('inventory.export.excel') }}" class="btn btn-success" title="Xuất Excel">
        <i class="bi bi-file-earmark-excel"></i>
    </a>
    <a href="{{ route('inventory.export.pdf') }}" class="btn btn-danger" title="Xuất PDF">
        <i class="bi bi-file-earmark-pdf"></i>
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th width="50">STT</th>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Kho</th>
                        <th>Tồn</th>
                        <th>Trạng thái</th>
                        <th>Giá trị</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $index => $item)
                    @php
                        $statusText = 'Bình thường';
                        $statusClass = 'bg-success';
                        if ($item->quantity <= $item->product->min_stock) {
                            $statusText = 'Tồn thấp';
                            $statusClass = 'bg-danger';
                        } elseif ($item->quantity >= $item->product->max_stock) {
                            $statusText = 'Tồn cao';
                            $statusClass = 'bg-warning text-dark';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $item->product->code }}</code></td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td>{{ $item->warehouse->name }}</td>
                        <td><strong>{{ number_format($item->quantity) }}</strong> {{ $item->product->unit }}</td>
                        <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                        <td>{{ number_format($item->quantity * $item->product->cost_price) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#filterWarehouse').on('change', function() {
        dataTable.column(4).search(this.value).draw();
    });
    $('#filterStatus').on('change', function() {
        dataTable.column(6).search(this.value).draw();
    });
    $('#filterCategory').on('change', function() {
        dataTable.column(3).search(this.value).draw();
    });
    $('#searchInput').on('keyup', function() {
        dataTable.search(this.value).draw();
    });
});
function resetFilters() {
    $('#filterWarehouse, #filterStatus, #filterCategory').val('');
    $('#searchInput').val('');
    dataTable.search('').columns().search('').draw();
}
</script>
@endpush
