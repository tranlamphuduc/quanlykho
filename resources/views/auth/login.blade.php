<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý Kho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .login-header .icon-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.4);
        }
        .login-header .icon-box i {
            font-size: 2.5rem;
            color: white;
        }
        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .login-header p {
            opacity: 0.8;
            font-size: 0.9rem;
            margin: 0;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            height: 58px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-floating .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }
        .form-floating label {
            padding-left: 3rem;
            color: #6c757d;
        }
        .form-floating .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.2rem;
            z-index: 5;
        }
        .form-check {
            margin-bottom: 25px;
        }
        .form-check-input {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #dee2e6;
        }
        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }
        .form-check-label {
            margin-left: 8px;
            color: #6c757d;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(52, 152, 219, 0.4);
            background: linear-gradient(135deg, #2980b9 0%, #1f6dad 100%);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .login-footer {
            text-align: center;
            padding: 20px 30px 30px;
            border-top: 1px solid #f1f3f4;
        }
        .login-footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
            margin-bottom: 20px;
            border: none;
        }
        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        .alert-success {
            background: #d1fae5;
            color: #059669;
        }
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .shape-1 { width: 200px; height: 200px; top: 10%; left: 10%; animation: float 8s ease-in-out infinite; }
        .shape-2 { width: 150px; height: 150px; top: 60%; right: 10%; animation: float 6s ease-in-out infinite reverse; }
        .shape-3 { width: 100px; height: 100px; bottom: 20%; left: 20%; animation: float 10s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="icon-box">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h1>Quản lý Kho</h1>
                <p>Hệ thống quản lý kho đồ gia dụng</p>
            </div>
            
            <div class="login-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Email" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email đăng nhập</label>
                    </div>
                    
                    <div class="form-floating">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Mật khẩu" required>
                        <label for="password">Mật khẩu</label>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <p class="text-muted mb-0">
                    <i class="bi bi-shield-check me-1"></i>
                    Bảo mật bởi Laravel
                </p>
            </div>
        </div>
    </div>
</body>
</html>
