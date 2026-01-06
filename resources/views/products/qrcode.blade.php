@extends('layouts.app')

@section('title', 'Mã QR - ' . $product->name)
@section('header')
<i class="bi bi-qr-code me-2"></i>Mã QR sản phẩm
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
    <button class="btn btn-primary" onclick="printQR()">
        <i class="bi bi-printer me-1"></i>In mã QR
    </button>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Thông tin sản phẩm</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="150">Mã SP:</th><td><code>{{ $product->code }}</code></td></tr>
                    <tr><th>Tên:</th><td><strong>{{ $product->name }}</strong></td></tr>
                    <tr><th>Mã vạch:</th><td>{{ $product->barcode ?? 'Chưa có' }}</td></tr>
                    <tr><th>Danh mục:</th><td>{{ $product->category->name ?? 'N/A' }}</td></tr>
                    <tr><th>Nhà cung cấp:</th><td>{{ $product->supplier->name ?? 'N/A' }}</td></tr>
                    <tr><th>Đơn vị:</th><td>{{ $product->unit }}</td></tr>
                    <tr><th>Giá bán:</th><td class="text-success fw-bold">{{ number_format($product->sell_price, 0, ',', '.') }}đ</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" id="qr-print">
            <div class="card-header">Mã QR Code</div>
            <div class="card-body text-center">
                <div class="mb-3">
                    {!! QrCode::size(250)->generate(url('products/' . $product->id . '/info')) !!}
                </div>
                <p class="mb-1"><strong>{{ $product->code }}</strong></p>
                <p class="text-muted small">{{ $product->name }}</p>
                <p class="text-muted small">Quét mã để xem thông tin sản phẩm</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printQR() {
    var printContents = document.getElementById('qr-print').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = '<div style="text-align:center; padding:20px;">' + printContents + '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>
@endpush
