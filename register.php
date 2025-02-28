<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_code"])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $verification_code = rand(100000, 999999);
        
        // Gửi mã xác thực qua email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Thay bằng SMTP server của bạn
            $mail->SMTPAuth = true;
            $mail->Username = 'phohoccode@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'edfr elqg nlvh sltj'; // Thay bằng mật khẩu email của bạn
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@example.com', 'Admin');
            $mail->addAddress($email);
            $mail->Subject = 'Mã xác thực của bạn';
            $mail->Body = "Mã xác thực của bạn là: $verification_code";

            $mail->send();
            session_start();
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['email'] = $email;
            echo "Mã xác thực đã được gửi đến email của bạn.";
        } catch (Exception $e) {
            echo "Lỗi khi gửi email: " . $mail->ErrorInfo;
        }
    } else {
        echo "Vui lòng nhập đầy đủ thông tin.";
    }
}
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
    <form method="POST" action="">
        <div class="input-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" name="username" required placeholder="Nhập tên đăng nhập">
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" required placeholder="Nhập email">
        </div>
        <div class="input-group password-container">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" required placeholder="Nhập mật khẩu">
        </div>
        <div class="input-group verify-container">
            <input type="text" id="verification" placeholder="Mã xác thực">
            <button type="submit" class="verify-btn" name="send_code">Gửi mã</button>
        </div>
        <button class="btn">Đăng ký</button>
    </form>
    <p class="register">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>
</body>
</html>
