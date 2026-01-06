@extends('layouts.app')

@section('title', 'Quản lý người dùng')
@section('header')
<i class="bi bi-people me-2"></i>Quản lý người dùng
@endsection

@section('content')
<div class="filter-row">
    <select name="role" class="form-select filter-select" style="max-width:150px;">
        <option value="">Tất cả vai trò</option>
        <option value="admin">Quản trị viên</option>
        <option value="warehouse_keeper">Thủ kho</option>
        <option value="sales">Nhân viên KD</option>
    </select>
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:200px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
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
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $roles = ['admin' => 'Quản trị viên', 'warehouse_keeper' => 'Thủ kho', 'sales' => 'Nhân viên KD'];
                    $roleClass = ['admin' => 'bg-danger', 'warehouse_keeper' => 'bg-primary', 'sales' => 'bg-success'];
                    @endphp
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge {{ $roleClass[$user->role] ?? 'bg-secondary' }}">{{ $roles[$user->role] ?? $user->role }}</span></td>
                        <td>
                            @if($user->status)
                            <span class="badge bg-success">Hoạt động</span>
                            @else
                            <span class="badge bg-secondary">Khóa</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='editUser(@json($user))'><i class="bi bi-pencil"></i></button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="modalTitle">Thêm người dùng</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Họ tên *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mật khẩu <span id="pwdRequired">*</span></label>
                        <input type="password" name="password" id="password" class="form-control">
                        <small class="text-muted" id="pwdHint" style="display:none;">Để trống nếu không đổi</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Vai trò</label>
                        <select name="role" id="role" class="form-select">
                            <option value="warehouse_keeper">Thủ kho</option>
                            <option value="admin">Quản trị viên</option>
                            <option value="sales">Nhân viên KD</option>
                        </select>
                    </div>
                    <div class="mb-2" id="statusGroup" style="display:none;">
                        <div class="form-check">
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="status">Hoạt động</label>
                        </div>
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
var baseUrl = '{{ url("users") }}';
function resetForm() {
    document.getElementById('userForm').action = '{{ route("users.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm người dùng';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('pwdRequired').style.display = '';
    document.getElementById('pwdHint').style.display = 'none';
    document.getElementById('role').value = 'warehouse_keeper';
    document.getElementById('statusGroup').style.display = 'none';
}
function editUser(user) {
    document.getElementById('userForm').action = baseUrl + '/' + user.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa người dùng';
    document.getElementById('name').value = user.name;
    document.getElementById('email').value = user.email;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('pwdRequired').style.display = 'none';
    document.getElementById('pwdHint').style.display = '';
    document.getElementById('role').value = user.role;
    document.getElementById('status').checked = user.status == 1;
    document.getElementById('statusGroup').style.display = '';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>
@endpush
