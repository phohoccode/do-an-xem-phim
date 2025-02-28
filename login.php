<?php include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
    <button class="back-button" onclick="history.back()">&#8592;</button>
        <h2>Đăng nhập</h2>
        <form>
            <div class="input-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" placeholder="Nhập tên đăng nhập">
            </div>
            <div class="input-group password-container">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" placeholder="Nhập mật khẩu">
            </div>
            <button class="btn">Đăng nhập</button>
        </form>
        <a href="forgot-pass.php" class="forgot-password">Quên mật khẩu?</a>
        <p class="register">Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</div>
</body>
</html>