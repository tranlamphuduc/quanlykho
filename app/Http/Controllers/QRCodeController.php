<?php

namespace App\Http\Controllers;

use App\Models\Product;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    public function generate($id)
    {
        $product = Product::with(['category', 'supplier'])->findOrFail($id);
        
        // Tạo nội dung QR Code
        $qrContent = json_encode([
            'code' => $product->code,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'category' => $product->category->name ?? 'N/A',
            'unit' => $product->unit,
            'price' => $product->sell_price,
        ]);

        return view('products.qrcode', compact('product', 'qrContent'));
    }

    public function generateImage($id)
    {
        $product = Product::findOrFail($id);
        
        $qrContent = url('products/' . $product->id . '/info');
        
        return response(QrCode::size(300)->generate($qrContent))
            ->header('Content-Type', 'image/svg+xml');
    }

    public function info($id)
    {
        $product = Product::with(['category', 'supplier', 'inventory.warehouse'])->findOrFail($id);
        return view('products.info', compact('product'));
    }
}
