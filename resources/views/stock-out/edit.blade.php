@extends('layouts.app')

@section('title', 'Sửa phiếu xuất kho')
@section('header')
<i class="bi bi-pencil me-2"></i>Sửa phiếu xuất kho: {{ $stockOut->code }}
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('stock-out.update', $stockOut) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Mã phiếu</label>
                    <input type="text" class="form-control" value="{{ $stockOut->code }}" readonly>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Kho xuất *</label>
                    <select name="warehouse_id" class="form-select" required>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $stockOut->warehouse_id == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ $stockOut->customer_name }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="note" class="form-control" value="{{ $stockOut->note }}">
                </div>
            </div>
            
            <table class="table table-bordered table-sm" id="productTable">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th width="100">Số lượng</th>
                        <th width="120">Đơn giá</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody id="productRows">
                    @foreach($stockOut->details as $detail)
                    <tr>
                        <td>
                            <select name="product_id[]" class="form-select form-select-sm" onchange="fillSellPrice(this)" required>
                                <option value="">-- Chọn SP --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}" {{ $detail->product_id == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="{{ $detail->quantity }}" required></td>
                        <td><input type="number" name="unit_price[]" class="form-control form-control-sm" min="0" value="{{ $detail->unit_price }}" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addRow()"><i class="bi bi-plus"></i> Thêm dòng</button>
            
            <div class="d-flex gap-2">
                <a href="{{ route('stock-out.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
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
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('productRows').insertAdjacentHTML('beforeend', row);
}
function removeRow(btn) {
    if (document.querySelectorAll('#productRows tr').length > 1) btn.closest('tr').remove();
}
function fillSellPrice(select) {
    const price = select.options[select.selectedIndex].dataset.price || 0;
    select.closest('tr').querySelector('input[name="unit_price[]"]').value = price;
}
</script>
@endpush
