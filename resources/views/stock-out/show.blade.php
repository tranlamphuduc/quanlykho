@extends('layouts.app')

@section('title', 'Chi tiết phiếu xuất')
@section('header')
<i class="bi bi-file-text me-2"></i>Chi tiết phiếu xuất #{{ $stockOut->code }}
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('stock-out.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
    <a href="{{ route('stock-out.export.excel', $stockOut) }}" class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i>Excel
    </a>
    <a href="{{ route('stock-out.export.pdf', $stockOut) }}" class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Thông tin phiếu xuất</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="150">Mã phiếu:</th><td><code>{{ $stockOut->code }}</code></td></tr>
                    <tr><th>Kho xuất:</th><td>{{ $stockOut->warehouse->name }}</td></tr>
                    <tr><th>Khách hàng:</th><td>{{ $stockOut->customer_name ?? 'N/A' }}</td></tr>
                    <tr><th>Người tạo:</th><td>{{ $stockOut->user->name }}</td></tr>
                    <tr><th>Ngày tạo:</th><td>{{ $stockOut->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Ghi chú:</th><td>{{ $stockOut->note ?? 'Không có' }}</td></tr>
                    <tr><th>Tổng tiền:</th><td><strong class="text-success fs-5">{{ number_format($stockOut->total_amount, 0, ',', '.') }}đ</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Chi tiết sản phẩm</div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                    <th>Serial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOut->details as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><code>{{ $detail->product->code }}</code></td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ number_format($detail->quantity) }}</td>
                    <td>{{ number_format($detail->unit_price, 0, ',', '.') }}đ</td>
                    <td><strong>{{ number_format($detail->quantity * $detail->unit_price, 0, ',', '.') }}đ</strong></td>
                    <td>{{ $detail->serial_number ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <th colspan="5" class="text-end">Tổng cộng:</th>
                    <th colspan="2">{{ number_format($stockOut->total_amount, 0, ',', '.') }}đ</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
