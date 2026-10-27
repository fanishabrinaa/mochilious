<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Migrate cart from session to database
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $product_id => $quantity) {
                        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                                             ON DUPLICATE KEY UPDATE quantity = quantity + ?");
                        $stmt->execute([$user['id'], $product_id, $quantity, $quantity]);
                    }
                    unset($_SESSION['cart']);
                }
                
                setFlash('success', 'Selamat datang, ' . $user['full_name'] . '!');
                
                redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
            } else {
                $error = 'Username atau password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MochiLicious</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --pink: #FFB3C6;
            --pink-dark: #FF8FAB;
            --pink-light: #FFE5EC;
            --pink-accent: #FF6B9D;
            --mochi: #FFF8F3;
            --cream: #FFF5E6;
            --charcoal: #2C2C2C;
            --text: #1A1A1A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--mochi) 0%, var(--pink-light) 50%, var(--cream) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid var(--pink-light);
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 0.2rem rgba(255, 179, 198, 0.25);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pink);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--pink) 0%, var(--pink-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 143, 171, 0.4);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 12px 20px;
        }

        .alert-danger {
            background: #ffe5e5;
            color: #d32f2f;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: var(--pink-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            color: var(--pink-accent);
        }

        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: var(--text);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .back-home a:hover {
            color: var(--pink-dark);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>MochiLicious</h1>
                <p>Masuk ke Akun Anda</p>
            </div>
            <div class="login-body">
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Akun berhasil dibuat. Silakan masuk.
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Username atau Email</label>
                        <div class="input-icon">
                            <input type="text" name="username" class="form-control" 
                                   placeholder="Masukkan username atau email" required autofocus>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-icon">
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Masukkan password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </form>

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="register-link">
                    Belum punya akun? <a href="register.php">Daftar Sekarang</a>
                </div>

                <div class="back-home">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>