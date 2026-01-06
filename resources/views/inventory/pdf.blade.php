<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo tồn kho</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .info { margin-bottom: 15px; }
        .info span { margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #2c3e50; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-danger { background: #e74c3c; color: white; }
        .badge-success { background: #27ae60; color: white; }
        .badge-warning { background: #f39c12; color: #333; }
        .total { margin-top: 15px; text-align: right; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BÁO CÁO TỒN KHO</h1>
        <p>Ngày xuất: {{ date('d/m/Y H:i') }}</p>
        @if($warehouse)
        <p>Kho: {{ $warehouse->name }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">Mã SP</th>
                <th width="22%">Tên sản phẩm</th>
                <th width="12%">Danh mục</th>
                <th width="10%">Kho</th>
                <th width="8%" class="text-center">Tồn</th>
                <th width="6%">ĐVT</th>
                <th width="6%" class="text-center">Min</th>
                <th width="6%" class="text-center">Max</th>
                <th width="10%">Trạng thái</th>
                <th width="12%" class="text-right">Giá trị</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($inventory as $item)
            @php
                $status = 'normal';
                $statusText = 'Bình thường';
                $statusClass = 'badge-success';
                if ($item->quantity <= $item->product->min_stock) {
                    $status = 'low';
                    $statusText = 'Tồn thấp';
                    $statusClass = 'badge-danger';
                } elseif ($item->quantity >= $item->product->max_stock) {
                    $status = 'over';
                    $statusText = 'Tồn cao';
                    $statusClass = 'badge-warning';
                }
                $value = $item->quantity * $item->product->cost_price;
                $grandTotal += $value;
            @endphp
            <tr>
                <td>{{ $item->product->code }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->category->name ?? 'N/A' }}</td>
                <td>{{ $item->warehouse->name }}</td>
                <td class="text-center">{{ number_format($item->quantity) }}</td>
                <td>{{ $item->product->unit }}</td>
                <td class="text-center">{{ $item->product->min_stock }}</td>
                <td class="text-center">{{ $item->product->max_stock }}</td>
                <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                <td class="text-right">{{ number_format($value, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Tổng giá trị tồn kho: {{ number_format($grandTotal, 0, ',', '.') }}đ
    </div>

    <div class="footer">
        Hệ thống Quản lý Kho Đồ Gia Dụng - Xuất bởi hệ thống
    </div>
</body>
</html>
