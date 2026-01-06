@extends('layouts.app')

@section('title', 'Nhập kho')
@section('header')
<i class="bi bi-box-arrow-in-down me-2"></i>Quản lý nhập kho
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
                <label class="form-label">Nhà cung cấp</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Tất cả NCC</option>
                    @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
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
            <div class="col-md-2">
                <label class="form-label">Mã phiếu</label>
                <input type="text" name="search" class="form-control" placeholder="Tìm mã..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('stock-in.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockInModal">
        <i class="bi bi-plus-lg me-1"></i>Tạo phiếu nhập
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Kho</th>
                    <th>Nhà cung cấp</th>
                    <th>Người tạo</th>
                    <th>Tổng tiền</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIns as $si)
                <tr>
                    <td><code>{{ $si->code }}</code></td>
                    <td>{{ $si->warehouse->name }}</td>
                    <td>{{ $si->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $si->user->name }}</td>
                    <td><strong>{{ number_format($si->total_amount, 0, ',', '.') }}đ</strong></td>
                    <td>{{ $si->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('stock-in.show', $si) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal tạo phiếu nhập -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('stock-in.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tạo phiếu nhập kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Mã phiếu</label>
                            <input type="text" name="code" class="form-control" value="{{ $newCode }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kho nhập <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nhà cung cấp</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">-- Chọn NCC --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                    </div>
                    
                    <h6>Chi tiết sản phẩm</h6>
                    <table class="table table-bordered" id="productTable">
                        <thead class="table-light">
                            <tr>
                                <th width="250">Sản phẩm</th>
                                <th width="100">Số lượng</th>
                                <th width="130">Đơn giá</th>
                                <th width="100">Số lô</th>
                                <th width="130">Hạn SD</th>
                                <th width="120">Serial</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-select form-select-sm" onchange="fillPrice(this)" required>
                                        <option value="">-- Chọn SP --</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->cost_price }}">{{ $p->code }} - {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required></td>
                                <td><input type="number" name="unit_price[]" class="form-control form-control-sm" min="0" value="0" required></td>
                                <td><input type="text" name="batch_number[]" class="form-control form-control-sm"></td>
                                <td><input type="date" name="expiry_date[]" class="form-control form-control-sm"></td>
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
                    <button type="submit" class="btn btn-primary">Lưu phiếu nhập</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productOptions = `<option value="">-- Chọn SP --</option>@foreach($products as $p)<option value="{{ $p->id }}" data-price="{{ $p->cost_price }}">{{ $p->code }} - {{ $p->name }}</option>@endforeach`;

function addRow() {
    const row = `<tr>
        <td><select name="product_id[]" class="form-select form-select-sm" onchange="fillPrice(this)" required>${productOptions}</select></td>
        <td><input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required></td>
        <td><input type="number" name="unit_price[]" class="form-control form-control-sm" min="0" value="0" required></td>
        <td><input type="text" name="batch_number[]" class="form-control form-control-sm"></td>
        <td><input type="date" name="expiry_date[]" class="form-control form-control-sm"></td>
        <td><input type="text" name="serial_number[]" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('productRows').insertAdjacentHTML('beforeend', row);
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#productRows tr');
    if (rows.length > 1) btn.closest('tr').remove();
}

function fillPrice(select) {
    const price = select.options[select.selectedIndex].dataset.price || 0;
    select.closest('tr').querySelector('input[name="unit_price[]"]').value = price;
}
</script>
@endpush
