@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('header')
<i class="bi bi-box me-2"></i>Quản lý sản phẩm
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nhà cung cấp</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Tất cả NCC</option>
                    @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã SP, tên..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
                <div class="btn-group ms-2">
                    <a href="{{ route('products.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                    <a href="{{ route('products.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Đơn vị</th>
                    <th>Giá vốn</th>
                    <th>Giá bán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td><code>{{ $product->code }}</code></td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->barcode)
                        <br><small class="text-muted">Barcode: {{ $product->barcode }}</small>
                        @endif
                    </td>
                    <td>{{ $product->category->name ?? 'Chưa phân loại' }}</td>
                    <td>{{ $product->unit }}</td>
                    <td>{{ number_format($product->cost_price, 0, ',', '.') }}đ</td>
                    <td>{{ number_format($product->sell_price, 0, ',', '.') }}đ</td>
                    <td>
                        <a href="{{ route('products.qrcode', $product->id) }}" class="btn btn-sm btn-outline-success" title="Mã QR">
                            <i class="bi bi-qr-code"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-primary" onclick="editProduct(@json($product))">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="productForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control" value="{{ $newCode }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã vạch</label>
                            <input type="text" name="barcode" id="barcode" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nhà cung cấp</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">-- Chọn NCC --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Đơn vị tính</label>
                            <input type="text" name="unit" id="unit" class="form-control" value="Cái">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Giá vốn</label>
                            <input type="number" name="cost_price" id="cost_price" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Giá bán</label>
                            <input type="number" name="sell_price" id="sell_price" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tồn kho tối thiểu</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control" value="10" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tồn kho tối đa</label>
                            <input type="number" name="max_stock" id="max_stock" class="form-control" value="1000" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
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
function resetForm() {
    document.getElementById('productForm').action = '{{ route("products.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
    document.getElementById('code').value = '{{ $newCode }}';
    document.getElementById('barcode').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('supplier_id').value = '';
    document.getElementById('unit').value = 'Cái';
    document.getElementById('cost_price').value = '0';
    document.getElementById('sell_price').value = '0';
    document.getElementById('min_stock').value = '10';
    document.getElementById('max_stock').value = '1000';
    document.getElementById('description').value = '';
}

function editProduct(product) {
    document.getElementById('productForm').action = '{{ url("products") }}/' + product.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa sản phẩm';
    document.getElementById('code').value = product.code;
    document.getElementById('barcode').value = product.barcode || '';
    document.getElementById('name').value = product.name;
    document.getElementById('category_id').value = product.category_id || '';
    document.getElementById('supplier_id').value = product.supplier_id || '';
    document.getElementById('unit').value = product.unit;
    document.getElementById('cost_price').value = product.cost_price;
    document.getElementById('sell_price').value = product.sell_price;
    document.getElementById('min_stock').value = product.min_stock;
    document.getElementById('max_stock').value = product.max_stock;
    document.getElementById('description').value = product.description || '';
    new bootstrap.Modal(document.getElementById('productModal')).show();
}
</script>
@endpush
