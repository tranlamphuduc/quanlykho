<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Thông tin sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .product-card { border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .product-header { background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); color: white; padding: 30px; text-align: center; }
        .product-body { padding: 30px; background: white; }
        .price { font-size: 2rem; color: #28a745; font-weight: bold; }
        .stock-badge { font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="product-card">
                    <div class="product-header">
                        <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 mb-0">{{ $product->name }}</h4>
                        <p class="mb-0 opacity-75">Mã: {{ $product->code }}</p>
                    </div>
                    <div class="product-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Danh mục</small>
                                <p class="mb-0 fw-bold">{{ $product->category->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Đơn vị</small>
                                <p class="mb-0 fw-bold">{{ $product->unit }}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Nhà cung cấp</small>
                                <p class="mb-0 fw-bold">{{ $product->supplier->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Mã vạch</small>
                                <p class="mb-0 fw-bold">{{ $product->barcode ?? 'Chưa có' }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center mb-3">
                            <small class="text-muted">Giá bán</small>
                            <p class="price mb-0">{{ number_format($product->sell_price, 0, ',', '.') }}đ</p>
                        </div>

                        <hr>

                        <h6><i class="bi bi-building me-2"></i>Tồn kho</h6>
                        @forelse($product->inventory as $inv)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $inv->warehouse->name }}</span>
                            @php
                                $badgeClass = 'bg-success';
                                if ($inv->quantity <= $product->min_stock) $badgeClass = 'bg-danger';
                                elseif ($inv->quantity >= $product->max_stock) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }} stock-badge">{{ number_format($inv->quantity) }} {{ $product->unit }}</span>
                        </div>
                        @empty
                        <p class="text-muted">Chưa có tồn kho</p>
                        @endforelse

                        @if($product->description)
                        <hr>
                        <h6><i class="bi bi-info-circle me-2"></i>Mô tả</h6>
                        <p class="text-muted mb-0">{{ $product->description }}</p>
                        @endif
                    </div>
                </div>
                <p class="text-center text-white mt-3 opacity-75">
                    <i class="bi bi-box-seam me-1"></i>Hệ thống Quản lý Kho Đồ Gia Dụng
                </p>
            </div>
        </div>
    </div>
</body>
</html>
