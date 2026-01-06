@extends('layouts.app')

@section('title', 'Quản lý danh mục')
@section('header')
<i class="bi bi-tags me-2"></i>Quản lý danh mục
@endsection

@section('content')
<div class="filter-row">
    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." style="max-width:250px;">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
        <i class="bi bi-plus-lg"></i> Thêm
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0">
                <thead>
                    <tr>
                        <th width="60">STT</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th width="80">Số SP</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $cat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $cat->name }}</strong></td>
                        <td>{{ $cat->description }}</td>
                        <td><span class="badge bg-primary">{{ $cat->products_count }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='editCategory(@json($cat))'><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
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

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="modalTitle">Thêm danh mục</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tên danh mục *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
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
var baseUrl = '{{ url("categories") }}';
function resetForm() {
    document.getElementById('categoryForm').action = '{{ route("categories.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Thêm danh mục';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
}
function editCategory(cat) {
    document.getElementById('categoryForm').action = baseUrl + '/' + cat.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Sửa danh mục';
    document.getElementById('name').value = cat.name;
    document.getElementById('description').value = cat.description || '';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
@endpush
