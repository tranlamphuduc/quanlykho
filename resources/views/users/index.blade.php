@extends('layouts.app')

@section('title', 'Quản lý người dùng')
@section('header')
<i class="bi bi-people me-2"></i>Quản lý người dùng
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Vai trò</label>
                <select name="role" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                    <option value="warehouse_keeper" {{ request('role') == 'warehouse_keeper' ? 'selected' : '' }}>Thủ kho</option>
                    <option value="sales" {{ request('role') == 'sales' ? 'selected' : '' }}>Nhân viên KD</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Lọc</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i>Thêm người dùng
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
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
                        <button class="btn btn-sm btn-outline-primary" onclick="editUser(@json($user))">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
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

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger" id="pwdRequired">*</span></label>
                        <input type="password" name="password" id="password" class="form-control">
                        <small class="text-muted" id="pwdHint" style="display:none;">Để trống nếu không đổi mật khẩu</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role" id="role" class="form-select">
                            <option value="admin">Quản trị viên</option>
                            <option value="warehouse_keeper" selected>Thủ kho</option>
                            <option value="sales">Nhân viên kinh doanh</option>
                        </select>
                    </div>
                    <div class="mb-3" id="statusGroup" style="display:none;">
                        <div class="form-check">
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="status">Hoạt động</label>
                        </div>
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
    document.getElementById('userForm').action = '{{ url("users") }}/' + user.id;
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
