<?php include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
    <button class="back-button" onclick="history.back()">&#8592;</button>
        <h2>Quên mật khẩu?</h2>
        <form>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Nhập email">
            </div>
            <div class="input-group">
                <label for="new-password">Mật khẩu mới</label>
                <input type="password" id="new-password" placeholder="Nhập mật khẩu mới">
            </div>
            <div class="input-group verify-container">
                <input type="text" id="verification" placeholder="Mã xác thực">
                <button type="button" class="verify-btn">Gửi mã</button>
            </div>
            <button class="btn">Xác nhận</button>
        </form>
    </div>
</body>
</html>