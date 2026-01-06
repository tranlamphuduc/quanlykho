@extends('layouts.app')

@section('title', 'Quản lý danh mục')
@section('header')
<i class="bi bi-tags me-2"></i>Quản lý danh mục
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên danh mục..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Tìm</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i>Thêm danh mục
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th width="120">Số sản phẩm</th>
                    <th width="150">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td><strong>{{ $cat->name }}</strong></td>
                    <td>{{ $cat->description }}</td>
                    <td><span class="badge bg-primary">{{ $cat->products_count }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editCategory(@json($cat))">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
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
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
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
    document.getElementById('categoryForm').action = '{{ route("categories.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
}

function editCategory(cat) {
    document.getElementById('categoryForm').action = '{{ url("categories") }}/' + cat.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa danh mục';
    document.getElementById('name').value = cat.name;
    document.getElementById('description').value = cat.description || '';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
@endpush
