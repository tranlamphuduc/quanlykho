@extends('layouts.app')

@section('title', 'Xuất kho')
@section('header')
<i class="bi bi-box-arrow-up me-2"></i>Quản lý xuất kho
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
                    <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã phiếu, khách hàng..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('stock-out.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockOutModal">
        <i class="bi bi-plus-lg me-1"></i>Tạo phiếu xuất
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Kho</th>
                    <th>Khách hàng</th>
                    <th>Người tạo</th>
                    <th>Tổng tiền</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOuts as $so)
                <tr>
                    <td><code>{{ $so->code }}</code></td>
                    <td>{{ $so->warehouse->name }}</td>
                    <td>{{ $so->customer_name ?? 'N/A' }}</td>
                    <td>{{ $so->user->name }}</td>
                    <td><strong>{{ number_format($so->total_amount, 0, ',', '.') }}đ</strong></td>
                    <td>{{ $so->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('stock-out.show', $so) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal tạo phiếu xuất -->
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('stock-out.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tạo phiếu xuất kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu</label>
                            <input type="text" name="code" class="form-control" value="{{ $newCode }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kho xuất <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Khách hàng</label>
                            <input type="text" name="customer_name" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                    </div>
                    
                    <h6>Chi tiết sản phẩm</h6>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="300">Sản phẩm</th>
                                <th width="100">Số lượng</th>
                                <th width="150">Đơn giá</th>
                                <th width="150">Serial</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-select form-select-sm" onchange="fillSellPrice(this)" required>
                                        <option value="">-- Chọn SP --</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">{{ $p->code }} - {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required></td>
                                <td><input type="number" name="unit_price[]" class="form-control form-control-sm" min="0" value="0" required></td>
                                <td><input type="text" name="serial_number[]" class="form-control form-control-sm"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()">
                        <i class="bi bi-plus"></i> Thêm dòng
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu phiếu xuất</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productOptions = `<option value="">-- Chọn SP --</option>@foreach($products as $p)<option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">{{ $p->code }} - {{ $p->name }}</option>@endforeach`;

function addRow() {
    const row = `<tr>
        <td><select name="product_id[]" class="form-select form-select-sm" onchange="fillSellPrice(this)" required>${productOptions}</select></td>
        <td><input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required></td>
        <td><input type="number" name="unit_price[]" class="form-control form-control-sm" min="0" value="0" required></td>
        <td><input type="text" name="serial_number[]" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('productRows').insertAdjacentHTML('beforeend', row);
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#productRows tr');
    if (rows.length > 1) btn.closest('tr').remove();
}

function fillSellPrice(select) {
    const price = select.options[select.selectedIndex].dataset.price || 0;
    select.closest('tr').querySelector('input[name="unit_price[]"]').value = price;
}
</script>
@endpush
