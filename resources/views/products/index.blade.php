@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('header')
<i class="bi bi-box me-2"></i>Quản lý sản phẩm
@endsection

@section('content')
<div class="filter-row">
    <select name="category_id" class="form-select filter-select" data-column="2">
        <option value="">Tất cả danh mục</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="supplier_id" class="form-select filter-select" data-column="3">
        <option value="">Tất cả NCC</option>
        @foreach($suppliers as $sup)
        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
        @endforeach
    </select>
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:200px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">
        <i class="bi bi-plus-lg"></i> Thêm
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>NCC</th>
                        <th>Giá vốn</th>
                        <th>Giá bán</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td><code>{{ $product->code }}</code></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->supplier->name ?? '-' }}</td>
                        <td>{{ number_format($product->cost_price) }}đ</td>
                        <td>{{ number_format($product->sell_price) }}đ</td>
                        <td>
                            <a href="{{ route('products.qrcode', $product->id) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-qr-code"></i></a>
                            <button class="btn btn-sm btn-outline-primary" onclick='editProduct(@json($product))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
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
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="productForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="modalTitle">Thêm sản phẩm</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Mã SP *</label>
                            <input type="text" name="code" id="code" class="form-control" value="{{ $newCode }}" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" name="unit" id="unit" class="form-control" value="Cái">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tên sản phẩm *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" id="modal_category_id" class="form-select">
                                <option value="">-- Chọn --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Nhà cung cấp</label>
                            <select name="supplier_id" id="modal_supplier_id" class="form-select">
                                <option value="">-- Chọn --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Giá vốn</label>
                            <input type="number" name="cost_price" id="cost_price" class="form-control" value="0">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Giá bán</label>
                            <input type="number" name="sell_price" id="sell_price" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Tồn min</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control" value="10">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Tồn max</label>
                            <input type="number" name="max_stock" id="max_stock" class="form-control" value="1000">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                    </div>
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
var baseUrl = '{{ url("products") }}';
function resetForm() {
    document.getElementById('productForm').action = '{{ route("products.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm sản phẩm';
    document.getElementById('code').value = '{{ $newCode }}';
    document.getElementById('name').value = '';
    document.getElementById('modal_category_id').value = '';
    document.getElementById('modal_supplier_id').value = '';
    document.getElementById('unit').value = 'Cái';
    document.getElementById('cost_price').value = '0';
    document.getElementById('sell_price').value = '0';
    document.getElementById('min_stock').value = '10';
    document.getElementById('max_stock').value = '1000';
    document.getElementById('description').value = '';
}

function editProduct(product) {
    document.getElementById('productForm').action = baseUrl + '/' + product.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa sản phẩm';
    document.getElementById('code').value = product.code;
    document.getElementById('name').value = product.name;
    document.getElementById('modal_category_id').value = product.category_id || '';
    document.getElementById('modal_supplier_id').value = product.supplier_id || '';
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
