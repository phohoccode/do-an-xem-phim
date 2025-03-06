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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/script.js"></script>
</head>
<body>
<div class="container">
    <button class="back-button" onclick="history.back()">&#8592;</button>
    <h2>Đăng nhập</h2>
    <form method="POST" action="">
        <div class="input-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập">
        </div>
        <div class="input-group password-container">
            <label for="password">Mật khẩu</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required placeholder="Nhập mật khẩu">
                <span class="eye-icon" onclick="togglePassword()">
                    <i id="eye-icon" class="fa fa-eye-slash"></i>
                </span>
            </div>
        </div>
        <button class="btn" type="submit">Đăng nhập</button>
    </form>
    <a href="forgot-pass.php" class="forgot-password">Quên mật khẩu?</a>
    <p class="register">Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</div>
</body>
</html>
