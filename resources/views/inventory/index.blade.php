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
    <select name="warehouse_id" class="form-select filter-select" data-column="3">
        <option value="">Tất cả kho</option>
        @foreach($warehouses as $wh)
        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
        @endforeach
    </select>
    <select name="status" class="form-select filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="low">Tồn thấp</option>
        <option value="normal">Bình thường</option>
        <option value="over">Tồn cao</option>
    </select>
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:180px;">
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
