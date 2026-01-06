@extends('layouts.app')

@section('title', 'Chi tiết phiếu nhập')
@section('header')
<i class="bi bi-file-text me-2"></i>Chi tiết phiếu nhập #{{ $stockIn->code }}
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Thông tin phiếu nhập</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="150">Mã phiếu:</th><td><code>{{ $stockIn->code }}</code></td></tr>
                    <tr><th>Kho nhập:</th><td>{{ $stockIn->warehouse->name }}</td></tr>
                    <tr><th>Nhà cung cấp:</th><td>{{ $stockIn->supplier->name ?? 'N/A' }}</td></tr>
                    <tr><th>Người tạo:</th><td>{{ $stockIn->user->name }}</td></tr>
                    <tr><th>Ngày tạo:</th><td>{{ $stockIn->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Ghi chú:</th><td>{{ $stockIn->note ?? 'Không có' }}</td></tr>
                    <tr><th>Tổng tiền:</th><td><strong class="text-primary fs-5">{{ number_format($stockIn->total_amount, 0, ',', '.') }}đ</strong></td></tr>
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
                    <th>Số lô</th>
                    <th>Hạn SD</th>
                    <th>Serial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIn->details as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><code>{{ $detail->product->code }}</code></td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ number_format($detail->quantity) }}</td>
                    <td>{{ number_format($detail->unit_price, 0, ',', '.') }}đ</td>
                    <td><strong>{{ number_format($detail->quantity * $detail->unit_price, 0, ',', '.') }}đ</strong></td>
                    <td>{{ $detail->batch_number ?? '-' }}</td>
                    <td>{{ $detail->expiry_date ? $detail->expiry_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $detail->serial_number ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <th colspan="5" class="text-end">Tổng cộng:</th>
                    <th colspan="4">{{ number_format($stockIn->total_amount, 0, ',', '.') }}đ</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
