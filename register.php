<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
session_start();

include 'connect.php';

// Gửi mã xác thực qua email
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_code"])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Địa chỉ email không hợp lệ. Vui lòng kiểm tra lại!');</script>";
    } elseif (strlen($password) < 6) {
        echo "<script>alert('Mật khẩu phải có ít nhất 6 ký tự!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email này đã được đăng ký trước đó. Vui lòng sử dụng email khác!');</script>";
        } else {
            if (!empty($email) && !empty($password)) {
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                $_SESSION['verification_code'] = rand(100000, 999999);
                
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phohoccode@gmail.com';
                    $mail->Password = 'edfr elqg nlvh sltj';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('your_email@example.com', 'Admin');
                    $mail->addAddress($email);
                    $mail->Subject = 'Mã xác thực của bạn';
                    $mail->Body = "Mã xác thực của bạn là: " . $_SESSION['verification_code'];

                    $mail->send();
                    echo "<script>alert('Mã xác thực đã được gửi đến email của bạn!');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Lỗi khi gửi email: " . $mail->ErrorInfo . "');</script>";
                }
            } else {
                echo "<script>alert('Vui lòng nhập đầy đủ email và mật khẩu!');</script>";
            }
        }
        $stmt->close();
    }
}

// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = trim($_POST['username']);
    $verification_code = trim($_POST['verification']);

    if (empty($verification_code)) {
        echo "<script>alert('Vui lòng nhập mã xác thực!');</script>";
    } elseif ($verification_code != $_SESSION['verification_code']) {
        echo "<script>alert('Không đúng mã xác thực!');</script>";
    } else {
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        if (strlen($password) < 6) {
            echo "<script>alert('Mật khẩu phải có ít nhất 6 ký tự!');</script>";
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "<script>alert('Email này đã được đăng ký trước đó. Vui lòng sử dụng email khác!');</script>";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $password);

                if ($stmt->execute()) {
                    echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
                    unset($_SESSION['verification_code'], $_SESSION['email'], $_SESSION['password']);
                } else {
                    echo "<script>alert('Lỗi đăng ký. Vui lòng thử lại!');</script>";
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/script.js"></script>
</head>
<body>
<div class="container">
    <h2>Đăng ký</h2>
    <p>Đăng ký tài khoản để bình luận, đánh giá và lưu những bộ phim yêu thích của bạn.</p>
    <form method="POST" action="">
        <div class="input-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" name="username" required placeholder="Nhập tên đăng nhập"
                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" required placeholder="Nhập email"
                   value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">
        </div>
        <div class="input-group password-container">
            <label for="password">Mật khẩu</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" required placeholder="Nhập mật khẩu" value="<?= isset($_SESSION['password']) ? htmlspecialchars($_SESSION['password']) : '' ?>">

            <span class="eye-icon" onclick="togglePassword()">
            <i id="eye-icon" class="fa fa-eye-slash"></i>
        </span>
        </div>
        </div>
        <div class="input-group verify-container">
            <input type="text" id="verification" name="verification" placeholder="Mã xác thực">
            <button type="submit" class="verify-btn" name="send_code">Gửi mã</button>
            <small id="verification-error" style="color: red;"></small>
        </div>
        <button class="btn" type="submit" name="register">Đăng ký</button>
    </form>
    <p class="register">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>
</body>
</html>
