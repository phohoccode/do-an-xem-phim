<?php 
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                echo "<script>alert('Đăng nhập thành công!'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Tên đăng nhập hoặc mật khẩu không đúng!');</script>";
            }
        } else {
            echo "<script>alert('Tên đăng nhập hoặc mật khẩu không đúng!');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
<body>
    
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <h3 class="text-center mb-4">Đăng nhập</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Nhập mật khẩu">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i id="eye-icon" class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
            </form>
            <div class="text-center mt-3">
                <a href="forgot-pass.php" class="text-decoration-none">Quên mật khẩu?</a>
            </div>
            <p class="text-center mt-3">Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
        </div>
    </div>
</body>
</html>

