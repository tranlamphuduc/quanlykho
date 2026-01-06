@extends('layouts.app')

@section('title', 'Quản lý nhà cung cấp')
@section('header')
<i class="bi bi-truck me-2"></i>Quản lý nhà cung cấp
@endsection

@section('content')
<div class="filter-row">
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:250px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="resetForm()">
        <i class="bi bi-plus-lg"></i> Thêm
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Tên NCC</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $sup)
                    <tr>
                        <td>{{ $sup->id }}</td>
                        <td><strong>{{ $sup->name }}</strong></td>
                        <td>{{ $sup->phone }}</td>
                        <td>{{ $sup->email }}</td>
                        <td>{{ $sup->address }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='editSupplier(@json($sup))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
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

<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="supplierForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="modalTitle">Thêm NCC</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tên NCC *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Điện thoại</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" id="address" class="form-control" rows="2"></textarea>
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
var baseUrl = '{{ url("suppliers") }}';
function resetForm() {
    document.getElementById('supplierForm').action = '{{ route("suppliers.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm NCC';
    document.getElementById('name').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('email').value = '';
    document.getElementById('address').value = '';
}
function editSupplier(sup) {
    document.getElementById('supplierForm').action = baseUrl + '/' + sup.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa NCC';
    document.getElementById('name').value = sup.name;
    document.getElementById('phone').value = sup.phone || '';
    document.getElementById('email').value = sup.email || '';
    document.getElementById('address').value = sup.address || '';
    new bootstrap.Modal(document.getElementById('supplierModal')).show();
}
</script>
@endpush
