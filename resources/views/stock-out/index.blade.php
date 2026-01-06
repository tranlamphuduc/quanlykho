@extends('layouts.app')

@section('title', 'Xuất kho')
@section('header')
<i class="bi bi-box-arrow-up me-2"></i>Quản lý xuất kho
@endsection

@section('content')
<div class="filter-row">
    <select name="warehouse_id" class="form-select filter-select" data-column="1">
        <option value="">Tất cả kho</option>
        @foreach($warehouses as $wh)
        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
        @endforeach
    </select>
    <input type="text" name="search" class="form-control" placeholder="Tìm mã phiếu, khách..." style="max-width:180px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockOutModal">
        <i class="bi bi-plus-lg"></i> Tạo phiếu xuất
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th width="50">STT</th>
                        <th>Mã phiếu</th>
                        <th>Kho</th>
                        <th>Khách hàng</th>
                        <th>Người tạo</th>
                        <th>Tổng tiền</th>
                        <th>Ngày</th>
                        <th width="60">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOuts as $index => $so)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $so->code }}</code></td>
                        <td>{{ $so->warehouse->name }}</td>
                        <td>{{ $so->customer_name ?? '-' }}</td>
                        <td>{{ $so->user->name }}</td>
                        <td><strong>{{ number_format($so->total_amount) }}đ</strong></td>
                        <td>{{ $so->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('stock-out.show', $so) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal tạo phiếu xuất -->
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('stock-out.store') }}" method="POST">
                @csrf
                <div class="modal-header py-2">
                    <h6 class="modal-title">Tạo phiếu xuất kho</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Mã phiếu</label>
                            <input type="text" name="code" class="form-control" value="{{ $newCode }}" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kho xuất *</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Khách hàng</label>
                            <input type="text" name="customer_name" class="form-control">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-sm">
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
                                    <select name="product_id[]" class="form-select form-select-sm" onchange="fillSellPrice(this)" required>
                                        <option value="">-- Chọn SP --</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">{{ $p->code }} - {{ $p->name }}</option>
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
