# Vai trò của Laravel trong Hệ thống Quản lý Kho

## 1. TỔNG QUAN

Laravel đóng vai trò là **Backend Framework** chính, xử lý toàn bộ logic nghiệp vụ, tương tác database, xác thực người dùng và render giao diện.

```
┌──────────────────────────────────────────────────────────┐
│                      LARAVEL FRAMEWORK                    │
├──────────────────────────────────────────────────────────┤
│  Routes → Middleware → Controller → Model → View         │
│     ↓         ↓            ↓          ↓        ↓         │
│  web.php   Auth/Admin   Logic     Database   Blade       │
└──────────────────────────────────────────────────────────┘
```

---

## 2. CÁC THÀNH PHẦN LARAVEL SỬ DỤNG

### 2.1. Routing (Định tuyến)

**File:** `routes/web.php`

```php
// Resource routes - tự động tạo 7 routes CRUD
Route::resource('products', ProductController::class);
Route::resource('categories', CategoryController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('warehouses', WarehouseController::class);

// Custom routes
Route::get('stock-in', [StockInController::class, 'index']);
Route::post('stock-in', [StockInController::class, 'store']);
Route::get('inventory/export-pdf', [InventoryController::class, 'exportPdf']);
```

**Vai trò:** Định nghĩa URL và map đến Controller xử lý.

---

### 2.2. Middleware (Lớp trung gian)

**File:** `app/Http/Middleware/`

| Middleware | Chức năng |
|------------|-----------|
| `auth` | Kiểm tra đăng nhập, redirect về login nếu chưa |
| `AdminMiddleware` | Kiểm tra role admin, chặn user thường |

```php
// Áp dụng middleware
Route::middleware(['auth'])->group(function () {
    // Chỉ user đã đăng nhập mới vào được
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

Route::middleware(['admin'])->group(function () {
    // Chỉ admin mới quản lý users
    Route::resource('users', UserController::class);
});
```

**Vai trò:** Bảo vệ routes, phân quyền truy cập.

---

### 2.3. Controllers (Điều khiển)

**Thư mục:** `app/Http/Controllers/`

| Controller | Chức năng |
|------------|-----------|
| `DashboardController` | Thống kê tổng quan, biểu đồ |
| `ProductController` | CRUD sản phẩm |
| `CategoryController` | CRUD danh mục |
| `SupplierController` | CRUD nhà cung cấp |
| `WarehouseController` | CRUD kho hàng |
| `StockInController` | Tạo/Sửa/Xóa/Duyệt/Hủy phiếu nhập, Import Excel |
| `StockOutController` | Tạo/Sửa/Xóa/Duyệt/Hủy phiếu xuất, Import Excel |
| `InventoryController` | Báo cáo tồn kho, xuất PDF/Excel |
| `UserController` | Quản lý người dùng (Admin) |
| `QRCodeController` | Tạo mã QR cho sản phẩm |
| `ReportController` | Báo cáo thống kê |

**Ví dụ ProductController:**
```php
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'supplier'])->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required|max:255',
        ]);
        Product::create($validated);
        return redirect()->back()->with('success', 'Thêm thành công!');
    }
}
```

**Vai trò:** Xử lý logic nghiệp vụ, nhận request, trả response.

---

### 2.4. Eloquent ORM (Models)

**Thư mục:** `app/Models/`

| Model | Bảng DB | Quan hệ |
|-------|--------|---------|
| `User` | users | hasMany(Warehouse) |
| `Category` | categories | hasMany(Product) |
| `Supplier` | suppliers | hasMany(Product) |
| `Warehouse` | warehouses | belongsTo(User), hasMany(Inventory) |
| `Product` | products | belongsTo(Category, Supplier) |
| `StockIn` | stock_ins | belongsTo(Warehouse, Supplier, User), hasMany(StockInDetail) |
| `StockOut` | stock_outs | belongsTo(Warehouse, User), hasMany(StockOutDetail) |
| `Inventory` | inventory | belongsTo(Product, Warehouse) |

**Ví dụ Product Model:**
```php
class Product extends Model
{
    protected $fillable = ['code', 'name', 'category_id', 'supplier_id', ...];

    // Quan hệ N:1 với Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ N:1 với Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
```

**Vai trò:** 
- Thao tác database như object (không cần viết SQL)
- Định nghĩa quan hệ giữa các bảng
- Tự động escape SQL Injection

---

### 2.5. Blade Templates (Views)

**Thư mục:** `resources/views/`

```
views/
├── layouts/
│   ├── app.blade.php      # Layout chính (sidebar, navbar)
│   └── sidebar.blade.php  # Menu điều hướng
├── auth/
│   └── login.blade.php    # Trang đăng nhập
├── dashboard.blade.php    # Trang chủ
├── products/
│   ├── index.blade.php    # Danh sách sản phẩm
│   ├── qrcode.blade.php   # Trang QR code
│   └── info.blade.php     # Thông tin SP (public)
├── stock-in/
│   ├── index.blade.php    # Danh sách phiếu nhập
│   └── show.blade.php     # Chi tiết phiếu nhập
└── inventory/
    ├── index.blade.php    # Báo cáo tồn kho
    └── pdf.blade.php      # Template xuất PDF
```

**Cú pháp Blade:**
```blade
{{-- Kế thừa layout --}}
@extends('layouts.app')

{{-- Định nghĩa section --}}
@section('content')
    {{-- Hiển thị biến (tự động escape XSS) --}}
    <h1>{{ $product->name }}</h1>
    
    {{-- Vòng lặp --}}
    @foreach($products as $product)
        <tr>
            <td>{{ $product->code }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
        </tr>
    @endforeach
    
    {{-- Điều kiện --}}
    @if($product->quantity < $product->min_stock)
        <span class="badge bg-danger">Tồn thấp</span>
    @endif
@endsection
```

**Vai trò:** Render HTML động, kế thừa layout, tái sử dụng component.

---

### 2.6. Migration & Seeder

**Migration (Schema database):**
```php
// database/migrations/create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('name');
    $table->foreignId('category_id')->constrained()->onDelete('set null');
    $table->foreignId('supplier_id')->constrained()->onDelete('set null');
    $table->decimal('cost_price', 15, 2)->default(0);
    $table->decimal('sell_price', 15, 2)->default(0);
    $table->timestamps();
});
```

**Seeder (Dữ liệu mẫu):**
```php
// database/seeders/DatabaseSeeder.php
User::create([
    'name' => 'Admin',
    'email' => 'admin@warehouse.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);
```

**Vai trò:** Quản lý version database, tạo dữ liệu test.

---

### 2.7. Authentication (Laravel Breeze)

**Chức năng có sẵn:**
- Đăng nhập / Đăng xuất
- Đăng ký tài khoản
- Quên mật khẩu (reset qua email)
- Remember me
- Session management

```php
// Kiểm tra đăng nhập trong Controller
if (auth()->check()) {
    $user = auth()->user();
    echo $user->name;
}

// Kiểm tra role
if (auth()->user()->role === 'admin') {
    // Chỉ admin
}
```

**Vai trò:** Xử lý toàn bộ authentication, bảo mật session.

---

### 2.8. Validation (Xác thực dữ liệu)

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'code' => 'required|unique:products|max:20',
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'cost_price' => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
    ]);

    Product::create($validated);
}
```

**Vai trò:** Kiểm tra dữ liệu đầu vào, trả về lỗi tự động.

---

### 2.9. Package bên thứ 3

| Package | Chức năng |
|---------|-----------|
| `maatwebsite/excel` | Xuất/Nhập file Excel |
| `barryvdh/laravel-dompdf` | Xuất file PDF |
| `simplesoftwareio/simple-qrcode` | Tạo mã QR |

**Ví dụ xuất Excel:**
```php
// app/Exports/InventoryExport.php
class InventoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Inventory::with(['product', 'warehouse'])->get();
    }

    public function headings(): array
    {
        return ['Mã SP', 'Tên SP', 'Kho', 'Số lượng', 'Giá trị'];
    }
}

// Controller
return Excel::download(new InventoryExport, 'tonkho.xlsx');
```

**Ví dụ Import Excel:**
```php
// app/Imports/StockInImport.php
class StockInImport implements ToArray, WithHeadingRow
{
    public array $data = [];
    public array $errors = [];

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $product = Product::where('code', $row['ma_sp'])->first();
            if (!$product) {
                $this->errors[] = "Mã SP không tồn tại";
                continue;
            }
            $this->data[] = [
                'product_id' => $product->id,
                'quantity' => $row['so_luong'],
                'unit_price' => $row['don_gia'],
            ];
        }
    }
}

// Controller
$import = new StockInImport;
Excel::import($import, $request->file('file'));
```

---

## 3. LUỒNG XỬ LÝ MỘT REQUEST

```
1. User truy cập: /products
        ↓
2. Routes (web.php): Route::resource('products', ProductController::class)
        ↓
3. Middleware: auth → kiểm tra đăng nhập
        ↓
4. Controller: ProductController@index
        ↓
5. Model: Product::with(['category', 'supplier'])->get()
        ↓
6. Database: SELECT * FROM products JOIN categories...
        ↓
7. View: resources/views/products/index.blade.php
        ↓
8. Response: HTML trả về browser
```

---

## 4. TÓM TẮT VAI TRÒ LARAVEL

| Thành phần | Vai trò trong hệ thống |
|------------|------------------------|
| **Routing** | Định nghĩa URL, map đến Controller |
| **Middleware** | Bảo vệ routes, phân quyền |
| **Controller** | Xử lý logic nghiệp vụ |
| **Eloquent ORM** | Thao tác database an toàn |
| **Blade** | Render giao diện động |
| **Migration** | Quản lý schema database |
| **Seeder** | Tạo dữ liệu mẫu |
| **Breeze** | Authentication hoàn chỉnh |
| **Validation** | Kiểm tra dữ liệu đầu vào |
| **Session** | Quản lý phiên đăng nhập |
| **CSRF** | Bảo vệ form khỏi tấn công |

**Kết luận:** Laravel cung cấp một hệ sinh thái hoàn chỉnh để xây dựng ứng dụng web, từ routing, database, authentication đến security, giúp phát triển nhanh và bảo mật.
