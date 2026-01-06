@extends('layouts.app')

@section('title', 'Quản lý kho hàng')
@section('header')
<i class="bi bi-building me-2"></i>Quản lý kho hàng
@endsection

@section('content')
<div class="filter-row">
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:250px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#warehouseModal" onclick="resetForm()">
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
                        <th>Tên kho</th>
                        <th>Địa chỉ</th>
                        <th>Người quản lý</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warehouses as $wh)
                    <tr>
                        <td>{{ $wh->id }}</td>
                        <td><strong>{{ $wh->name }}</strong></td>
                        <td>{{ $wh->address }}</td>
                        <td>{{ $wh->manager->name ?? '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='editWarehouse(@json($wh))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('warehouses.destroy', $wh) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
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

<div class="modal fade" id="warehouseModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="warehouseForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="modalTitle">Thêm kho</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tên kho *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Người quản lý</label>
                        <select name="manager_id" id="manager_id" class="form-select">
                            <option value="">-- Chọn --</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
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
var baseUrl = '{{ url("warehouses") }}';
function resetForm() {
    document.getElementById('warehouseForm').action = '{{ route("warehouses.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm kho';
    document.getElementById('name').value = '';
    document.getElementById('address').value = '';
    document.getElementById('manager_id').value = '';
}
function editWarehouse(wh) {
    document.getElementById('warehouseForm').action = baseUrl + '/' + wh.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa kho';
    document.getElementById('name').value = wh.name;
    document.getElementById('address').value = wh.address || '';
    document.getElementById('manager_id').value = wh.manager_id || '';
    new bootstrap.Modal(document.getElementById('warehouseModal')).show();
}
</script>
@endpush
