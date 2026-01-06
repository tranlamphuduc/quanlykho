@extends('layouts.app')

@section('title', 'Nhập kho')
@section('header')
<i class="bi bi-box-arrow-in-down me-2"></i>Quản lý nhập kho
@endsection

@section('content')
<div class="filter-row">
    <select name="warehouse_id" class="form-select filter-select" data-column="1">
        <option value="">Tất cả kho</option>
        @foreach($warehouses as $wh)
        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
        @endforeach
    </select>
    <input type="text" name="search" class="form-control" placeholder="Tìm mã phiếu..." style="max-width:180px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockInModal">
        <i class="bi bi-plus-lg"></i> Tạo phiếu nhập
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Kho</th>
                        <th>NCC</th>
                        <th>Người tạo</th>
                        <th>Tổng tiền</th>
                        <th>Ngày</th>
                        <th width="60">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockIns as $si)
                    <tr>
                        <td><code>{{ $si->code }}</code></td>
                        <td>{{ $si->warehouse->name }}</td>
                        <td>{{ $si->supplier->name ?? '-' }}</td>
                        <td>{{ $si->user->name }}</td>
                        <td><strong>{{ number_format($si->total_amount) }}đ</strong></td>
                        <td>{{ $si->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('stock-in.show', $si) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal tạo phiếu nhập -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('stock-in.store') }}" method="POST">
                @csrf
                <div class="modal-header py-2">
                    <h6 class="modal-title">Tạo phiếu nhập kho</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Mã phiếu</label>
                            <input type="text" name="code" class="form-control" value="{{ $newCode }}" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kho nhập *</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Nhà cung cấp</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">-- Chọn --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-sm" id="productTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th width="80">SL</th>
                                <th width="100">Đơn giá</th>
                                <th width="40"></th>
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
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()"><i class="bi bi-plus"></i> Thêm dòng</button>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
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
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('productRows').insertAdjacentHTML('beforeend', row);
}
function removeRow(btn) {
    if (document.querySelectorAll('#productRows tr').length > 1) btn.closest('tr').remove();
}
function fillPrice(select) {
    const price = select.options[select.selectedIndex].dataset.price || 0;
    select.closest('tr').querySelector('input[name="unit_price[]"]').value = price;
}
</script>
@endpush
