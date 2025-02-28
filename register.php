<?php include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
        <h2>Đăng ký</h2>
        <form>
            <div class="input-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" placeholder="Nhập tên đăng nhập">
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Nhập email">
            </div>
            <div class="input-group password-container">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" placeholder="Nhập mật khẩu">
            </div>
            <div class="input-group verify-container">
                <input type="text" id="verification" placeholder="Mã xác thực">
                <button type="button" class="verify-btn">Gửi mã</button>
            </div>
            <button class="btn">Đăng ký</button>
        </form>
        <p class="register">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>
</html>