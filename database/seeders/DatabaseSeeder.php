<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => true,
        ]);

        $keeper1 = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'thukho@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'warehouse_keeper',
            'status' => true,
        ]);

        $keeper2 = User::create([
            'name' => 'Trần Văn B',
            'email' => 'thukho2@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'warehouse_keeper',
            'status' => true,
        ]);

        User::create([
            'name' => 'Lê Thị C',
            'email' => 'sales@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'status' => true,
        ]);

        // Categories - Danh mục đồ gia dụng
        $categories = [
            ['name' => 'Đồ điện gia dụng', 'description' => 'Thiết bị điện tử như quạt, máy sấy, nồi cơm điện'],
            ['name' => 'Đồ dùng nhà bếp', 'description' => 'Nồi, chảo, dao, thớt, dụng cụ nấu ăn'],
            ['name' => 'Đồ vệ sinh', 'description' => 'Nước giặt, nước rửa chén, chổi, cây lau nhà'],
            ['name' => 'Đồ nội thất', 'description' => 'Bàn ghế, kệ, tủ nhỏ'],
            ['name' => 'Đồ trang trí', 'description' => 'Đèn trang trí, khung ảnh, bình hoa'],
            ['name' => 'Đồ phòng ngủ', 'description' => 'Chăn, ga, gối, nệm'],
            ['name' => 'Đồ phòng tắm', 'description' => 'Vòi sen, kệ phòng tắm, rèm tắm'],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Suppliers - Nhà cung cấp
        $suppliers = [
            ['name' => 'Công ty TNHH Điện máy Sunhouse', 'phone' => '0901234567', 'email' => 'sunhouse@supplier.com', 'address' => '123 Nguyễn Văn Linh, Q7, TP.HCM'],
            ['name' => 'Nhà phân phối Kangaroo', 'phone' => '0912345678', 'email' => 'kangaroo@supplier.com', 'address' => '456 Lê Văn Việt, Q9, TP.HCM'],
            ['name' => 'Công ty CP Lock&Lock Việt Nam', 'phone' => '0923456789', 'email' => 'locklock@supplier.com', 'address' => '789 Điện Biên Phủ, Q3, TP.HCM'],
            ['name' => 'Đại lý Unilever', 'phone' => '0934567890', 'email' => 'unilever@supplier.com', 'address' => '321 Võ Văn Tần, Q3, TP.HCM'],
            ['name' => 'Công ty TNHH Happycook', 'phone' => '0945678901', 'email' => 'happycook@supplier.com', 'address' => '654 Cách Mạng Tháng 8, Q10, TP.HCM'],
        ];
        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        // Warehouses - Kho hàng
        $warehouse1 = Warehouse::create([
            'name' => 'Kho Quận 7',
            'address' => '789 Nguyễn Thị Thập, Q7, TP.HCM',
            'manager_id' => $keeper1->id,
        ]);

        $warehouse2 = Warehouse::create([
            'name' => 'Kho Quận 9',
            'address' => '321 Lê Văn Việt, Q9, TP.HCM',
            'manager_id' => $keeper2->id,
        ]);

        // Products - Sản phẩm đồ gia dụng
        $products = [
            // Đồ điện gia dụng (category_id = 1)
            ['code' => 'SP000001', 'name' => 'Quạt điện Sunhouse SHD7624', 'category_id' => 1, 'supplier_id' => 1, 'unit' => 'Cái', 'cost_price' => 450000, 'sell_price' => 550000, 'min_stock' => 10, 'max_stock' => 100],
            ['code' => 'SP000002', 'name' => 'Nồi cơm điện Sunhouse 1.8L', 'category_id' => 1, 'supplier_id' => 1, 'unit' => 'Cái', 'cost_price' => 650000, 'sell_price' => 790000, 'min_stock' => 5, 'max_stock' => 50],
            ['code' => 'SP000003', 'name' => 'Máy xay sinh tố Sunhouse SHD5321', 'category_id' => 1, 'supplier_id' => 1, 'unit' => 'Cái', 'cost_price' => 380000, 'sell_price' => 450000, 'min_stock' => 8, 'max_stock' => 60],
            ['code' => 'SP000004', 'name' => 'Bàn ủi hơi nước Sunhouse SHD2065', 'category_id' => 1, 'supplier_id' => 1, 'unit' => 'Cái', 'cost_price' => 290000, 'sell_price' => 350000, 'min_stock' => 10, 'max_stock' => 80],
            ['code' => 'SP000005', 'name' => 'Máy lọc nước Kangaroo KG104', 'category_id' => 1, 'supplier_id' => 2, 'unit' => 'Cái', 'cost_price' => 4500000, 'sell_price' => 5200000, 'min_stock' => 3, 'max_stock' => 20],
            ['code' => 'SP000006', 'name' => 'Bình nước nóng Kangaroo 20L', 'category_id' => 1, 'supplier_id' => 2, 'unit' => 'Cái', 'cost_price' => 2100000, 'sell_price' => 2500000, 'min_stock' => 5, 'max_stock' => 30],
            
            // Đồ dùng nhà bếp (category_id = 2)
            ['code' => 'SP000007', 'name' => 'Bộ nồi inox 5 đáy Sunhouse', 'category_id' => 2, 'supplier_id' => 1, 'unit' => 'Bộ', 'cost_price' => 890000, 'sell_price' => 1050000, 'min_stock' => 5, 'max_stock' => 40],
            ['code' => 'SP000008', 'name' => 'Chảo chống dính Happycook 26cm', 'category_id' => 2, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 180000, 'sell_price' => 220000, 'min_stock' => 15, 'max_stock' => 100],
            ['code' => 'SP000009', 'name' => 'Bộ dao 6 món Sunhouse', 'category_id' => 2, 'supplier_id' => 1, 'unit' => 'Bộ', 'cost_price' => 320000, 'sell_price' => 390000, 'min_stock' => 10, 'max_stock' => 60],
            ['code' => 'SP000010', 'name' => 'Hộp đựng thực phẩm Lock&Lock 3 cái', 'category_id' => 2, 'supplier_id' => 3, 'unit' => 'Bộ', 'cost_price' => 150000, 'sell_price' => 189000, 'min_stock' => 20, 'max_stock' => 150],
            ['code' => 'SP000011', 'name' => 'Bình giữ nhiệt Lock&Lock 500ml', 'category_id' => 2, 'supplier_id' => 3, 'unit' => 'Cái', 'cost_price' => 280000, 'sell_price' => 350000, 'min_stock' => 15, 'max_stock' => 100],
            ['code' => 'SP000012', 'name' => 'Thớt gỗ cao cấp 30x40cm', 'category_id' => 2, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 85000, 'sell_price' => 120000, 'min_stock' => 20, 'max_stock' => 120],
            
            // Đồ vệ sinh (category_id = 3)
            ['code' => 'SP000013', 'name' => 'Nước giặt OMO 3.7kg', 'category_id' => 3, 'supplier_id' => 4, 'unit' => 'Chai', 'cost_price' => 145000, 'sell_price' => 175000, 'min_stock' => 30, 'max_stock' => 200],
            ['code' => 'SP000014', 'name' => 'Nước rửa chén Sunlight 3.6kg', 'category_id' => 3, 'supplier_id' => 4, 'unit' => 'Chai', 'cost_price' => 95000, 'sell_price' => 115000, 'min_stock' => 30, 'max_stock' => 200],
            ['code' => 'SP000015', 'name' => 'Nước lau sàn Sunlight 3.8kg', 'category_id' => 3, 'supplier_id' => 4, 'unit' => 'Chai', 'cost_price' => 78000, 'sell_price' => 95000, 'min_stock' => 25, 'max_stock' => 150],
            ['code' => 'SP000016', 'name' => 'Cây lau nhà xoay 360 độ', 'category_id' => 3, 'supplier_id' => 5, 'unit' => 'Bộ', 'cost_price' => 250000, 'sell_price' => 320000, 'min_stock' => 10, 'max_stock' => 60],
            ['code' => 'SP000017', 'name' => 'Chổi quét nhà cao cấp', 'category_id' => 3, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 45000, 'sell_price' => 65000, 'min_stock' => 20, 'max_stock' => 100],
            
            // Đồ nội thất (category_id = 4)
            ['code' => 'SP000018', 'name' => 'Kệ để giày 5 tầng', 'category_id' => 4, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 350000, 'sell_price' => 450000, 'min_stock' => 5, 'max_stock' => 30],
            ['code' => 'SP000019', 'name' => 'Ghế nhựa cao cấp', 'category_id' => 4, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 120000, 'sell_price' => 160000, 'min_stock' => 15, 'max_stock' => 80],
            ['code' => 'SP000020', 'name' => 'Tủ nhựa 5 ngăn Duy Tân', 'category_id' => 4, 'supplier_id' => 5, 'unit' => 'Cái', 'cost_price' => 680000, 'sell_price' => 850000, 'min_stock' => 5, 'max_stock' => 25],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }

        // Tạo phiếu nhập kho và tồn kho
        $this->createStockInData($warehouse1, $warehouse2, $keeper1, $keeper2);
        
        // Tạo phiếu xuất kho
        $this->createStockOutData($warehouse1, $warehouse2, $keeper1, $keeper2);
    }

    private function createStockInData($warehouse1, $warehouse2, $keeper1, $keeper2)
    {
        $products = Product::all();
        
        // Tạo phiếu nhập cho các tháng trong năm
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(2026, $month, rand(1, 28));
            
            // Phiếu nhập kho 1
            $stockIn1 = StockIn::create([
                'code' => 'PN' . $date->format('Ymd') . str_pad($month, 4, '0', STR_PAD_LEFT),
                'warehouse_id' => $warehouse1->id,
                'supplier_id' => rand(1, 5),
                'user_id' => $keeper1->id,
                'total_amount' => 0,
                'note' => 'Nhập hàng tháng ' . $month,
                'status' => 'completed',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $total = 0;
            $selectedProducts = $products->random(rand(5, 10));
            foreach ($selectedProducts as $product) {
                $qty = rand(10, 50);
                $stockIn1->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->cost_price,
                    'batch_number' => 'LOT' . $date->format('Ymd') . rand(100, 999),
                ]);
                $total += $qty * $product->cost_price;

                // Cập nhật tồn kho
                Inventory::updateOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $warehouse1->id],
                    ['quantity' => \DB::raw('quantity + ' . $qty)]
                );
            }
            $stockIn1->update(['total_amount' => $total]);

            // Phiếu nhập kho 2
            if ($month % 2 == 0) {
                $date2 = Carbon::create(2026, $month, rand(10, 25));
                $stockIn2 = StockIn::create([
                    'code' => 'PN' . $date2->format('Ymd') . str_pad($month + 100, 4, '0', STR_PAD_LEFT),
                    'warehouse_id' => $warehouse2->id,
                    'supplier_id' => rand(1, 5),
                    'user_id' => $keeper2->id,
                    'total_amount' => 0,
                    'note' => 'Nhập hàng kho 2 tháng ' . $month,
                    'status' => 'completed',
                    'created_at' => $date2,
                    'updated_at' => $date2,
                ]);

                $total2 = 0;
                $selectedProducts2 = $products->random(rand(3, 7));
                foreach ($selectedProducts2 as $product) {
                    $qty = rand(5, 30);
                    $stockIn2->details()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->cost_price,
                    ]);
                    $total2 += $qty * $product->cost_price;

                    Inventory::updateOrCreate(
                        ['product_id' => $product->id, 'warehouse_id' => $warehouse2->id],
                        ['quantity' => \DB::raw('quantity + ' . $qty)]
                    );
                }
                $stockIn2->update(['total_amount' => $total2]);
            }
        }
    }

    private function createStockOutData($warehouse1, $warehouse2, $keeper1, $keeper2)
    {
        $customers = ['Cửa hàng Minh Anh', 'Siêu thị CoopMart', 'Đại lý Hùng Phát', 'Shop Gia Dụng 24h', 'Cửa hàng Tiện Lợi', 'Chị Lan - Q7', 'Anh Tuấn - Q9'];
        
        for ($month = 1; $month <= 12; $month++) {
            // 2-4 phiếu xuất mỗi tháng
            $numOrders = rand(2, 4);
            for ($i = 0; $i < $numOrders; $i++) {
                $date = Carbon::create(2026, $month, rand(1, 28));
                $warehouseId = rand(0, 1) ? $warehouse1->id : $warehouse2->id;
                $userId = $warehouseId == $warehouse1->id ? $keeper1->id : $keeper2->id;

                $stockOut = StockOut::create([
                    'code' => 'PX' . $date->format('Ymd') . str_pad($month * 10 + $i, 4, '0', STR_PAD_LEFT),
                    'warehouse_id' => $warehouseId,
                    'user_id' => $userId,
                    'customer_name' => $customers[array_rand($customers)],
                    'total_amount' => 0,
                    'note' => 'Xuất hàng đơn ' . ($i + 1) . ' tháng ' . $month,
                    'status' => 'completed',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $total = 0;
                // Lấy sản phẩm có tồn kho
                $inventoryItems = Inventory::where('warehouse_id', $warehouseId)
                    ->where('quantity', '>', 5)
                    ->inRandomOrder()
                    ->limit(rand(2, 5))
                    ->get();

                foreach ($inventoryItems as $inv) {
                    $product = Product::find($inv->product_id);
                    $qty = min(rand(2, 10), $inv->quantity - 2);
                    if ($qty > 0) {
                        $stockOut->details()->create([
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'unit_price' => $product->sell_price,
                        ]);
                        $total += $qty * $product->sell_price;

                        // Trừ tồn kho
                        $inv->decrement('quantity', $qty);
                    }
                }
                $stockOut->update(['total_amount' => $total]);
            }
        }
    }
}
