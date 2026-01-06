@extends('layouts.app')

@section('title', 'Báo cáo tồn kho')
@section('header')
<i class="bi bi-clipboard-data me-2"></i>Báo cáo tồn kho
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Kho</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">Tất cả kho</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Tồn thấp</option>
                    <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>Bình thường</option>
                    <option value="over" {{ request('status') == 'over' ? 'selected' : '' }}>Tồn cao</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã SP, tên..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Xóa</a>
                <div class="btn-group ms-2">
                    <a href="{{ route('inventory.export.excel', request()->query()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </a>
                    <a href="{{ route('inventory.export.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <div class="stat-card bg-info">
            <h6 class="text-white-50">Tổng giá trị tồn kho</h6>
            <h3 class="mb-0">{{ number_format($totalValue, 0, ',', '.') }}đ</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-danger">
            <h6 class="text-white-50">Sản phẩm tồn thấp</h6>
            <h3 class="mb-0">{{ $lowStockCount }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success">
            <h6 class="text-white-50">Sản phẩm bình thường</h6>
            <h3 class="mb-0">{{ $normalStockCount }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning">
            <h6 class="text-white-50">Sản phẩm tồn cao</h6>
            <h3 class="mb-0">{{ $overStockCount }}</h3>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Kho</th>
                    <th>Tồn kho</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Trạng thái</th>
                    <th>Giá trị</th>
                    <th>QR</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventory as $item)
                @php
                    $status = 'normal';
                    $statusText = 'Bình thường';
                    $statusClass = 'bg-success';
                    if ($item->quantity <= $item->product->min_stock) {
                        $status = 'low';
                        $statusText = 'Tồn thấp';
                        $statusClass = 'bg-danger';
                    } elseif ($item->quantity >= $item->product->max_stock) {
                        $status = 'over';
                        $statusText = 'Tồn cao';
                        $statusClass = 'bg-warning text-dark';
                    }
                @endphp
                <tr>
                    <td><code>{{ $item->product->code }}</code></td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->category->name ?? 'N/A' }}</td>
                    <td>{{ $item->warehouse->name }}</td>
                    <td><strong>{{ number_format($item->quantity) }}</strong> {{ $item->product->unit }}</td>
                    <td>{{ number_format($item->product->min_stock) }}</td>
                    <td>{{ number_format($item->product->max_stock) }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                    <td>{{ number_format($item->quantity * $item->product->cost_price, 0, ',', '.') }}đ</td>
                    <td>
                        <a href="{{ route('products.qrcode', $item->product->id) }}" class="btn btn-sm btn-outline-success" title="Xem QR">
                            <i class="bi bi-qr-code"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
