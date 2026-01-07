<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phiếu nhập kho {{ $stockIn->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px 0; }
        .info-table .label { font-weight: bold; width: 150px; }
        table.details { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.details th, table.details td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.details th { background: #f5f5f5; font-weight: bold; }
        table.details .number { text-align: right; }
        .total-row { background: #e3f2fd; font-weight: bold; }
        .footer { margin-top: 30px; }
        .signature { display: inline-block; width: 45%; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PHIẾU NHẬP KHO</h2>
        <p>Mã phiếu: <strong>{{ $stockIn->code }}</strong></p>
        <p>Ngày: {{ $stockIn->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Kho nhập:</td>
            <td>{{ $stockIn->warehouse->name }}</td>
        </tr>
        <tr>
            <td class="label">Nhà cung cấp:</td>
            <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Người tạo:</td>
            <td>{{ $stockIn->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Ghi chú:</td>
            <td>{{ $stockIn->note ?? 'Không có' }}</td>
        </tr>
    </table>

    <h4>Chi tiết sản phẩm:</h4>
    <table class="details">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã SP</th>
                <th>Tên sản phẩm</th>
                <th class="number">SL</th>
                <th class="number">Đơn giá</th>
                <th class="number">Thành tiền</th>
                <th>Số lô</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockIn->details as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->product->code }}</td>
                <td>{{ $detail->product->name }}</td>
                <td class="number">{{ number_format($detail->quantity) }}</td>
                <td class="number">{{ number_format($detail->unit_price, 0, ',', '.') }}đ</td>
                <td class="number">{{ number_format($detail->quantity * $detail->unit_price, 0, ',', '.') }}đ</td>
                <td>{{ $detail->batch_number ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                <td class="number"><strong>{{ number_format($stockIn->total_amount, 0, ',', '.') }}đ</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <p><strong>Người lập phiếu</strong></p>
            <p style="margin-top: 60px;">{{ $stockIn->user->name }}</p>
        </div>
        <div class="signature">
            <p><strong>Thủ kho</strong></p>
            <p style="margin-top: 60px;">_______________</p>
        </div>
    </div>
</body>
</html>
