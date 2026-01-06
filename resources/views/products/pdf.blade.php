<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px 6px; text-align: left; }
        th { background: #2c3e50; color: white; font-weight: bold; font-size: 9px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; text-align: center; color: #999; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH SẢN PHẨM</h1>
        <p>Ngày xuất: {{ date('d/m/Y H:i') }} | Tổng: {{ $products->count() }} sản phẩm</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">Mã SP</th>
                <th width="25%">Tên sản phẩm</th>
                <th width="12%">Danh mục</th>
                <th width="15%">Nhà cung cấp</th>
                <th width="6%">ĐVT</th>
                <th width="10%" class="text-right">Giá vốn</th>
                <th width="10%" class="text-right">Giá bán</th>
                <th width="7%">Min</th>
                <th width="7%">Max</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                <td>{{ $product->unit }}</td>
                <td class="text-right">{{ number_format($product->cost_price, 0, ',', '.') }}đ</td>
                <td class="text-right">{{ number_format($product->sell_price, 0, ',', '.') }}đ</td>
                <td>{{ $product->min_stock }}</td>
                <td>{{ $product->max_stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Hệ thống Quản lý Kho Đồ Gia Dụng
    </div>
</body>
</html>
