<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'connect.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Xử lý gửi mã xác thực
    if (isset($_POST["send_code"])) {
        $email = trim($_POST['email']);

        if (!empty($email)) {
            // Kiểm tra email có trong cơ sở dữ liệu không
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $verification_code = rand(100000, 999999);

                // Gửi mã xác thực qua email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phohoccode@gmail.com'; // Thay bằng email của bạn
                    $mail->Password = 'edfr elqg nlvh sltj'; // Thay bằng mật khẩu ứng dụng Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('phohoccode@gmail.com', 'Admin');
                    $mail->addAddress($email);
                    $mail->Subject = 'Mã xác thực của bạn';
                    $mail->Body = "Mã xác thực của bạn là: $verification_code";

                    $mail->send();

                    // Lưu thông tin vào session để xác thực sau này
                    $_SESSION['verification_code'] = $verification_code;
                    $_SESSION['email'] = $email; // Lưu email vào 

                    echo "<script>alert('Mã xác thực đã được gửi đến email của bạn!');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Lỗi khi gửi email: " . $mail->ErrorInfo . "');</script>";
                }
            } else {
                echo "<script>alert('Email không tồn tại trong hệ thống!');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Vui lòng nhập email!');</script>";
        }
    }
    // Xử lý xác nhận mã và đổi mật khẩu
    elseif (isset($_POST['verification']) && isset($_POST['new_password'])) {
        $verification = trim($_POST['verification']);
        $new_password = trim($_POST['new_password']);
        $email = $_SESSION['email'];

        // Kiểm tra mã xác thực
        if (empty($verification)) {
            echo "<script>alert('Vui lòng nhập mã xác thực!');</script>";
        } elseif ($verification != $_SESSION['verification_code']) {
            echo "<script>alert('Mã xác thực không đúng!');</script>";
        } else {
            // Kiểm tra mật khẩu mới
            if (strlen($new_password) < 6) {
                echo "<script>alert('Mật khẩu phải có ít nhất 6 ký tự!');</script>";
            } else {
            // Kiểm tra mật khẩu cũ (mã hóa) và mật khẩu mới
            $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($current_password);
                $stmt->fetch();

            // Kiểm tra mật khẩu mới không trùng mật khẩu cũ
            if (password_verify($new_password, $current_password)) {
                echo "<script>alert('Mật khẩu mới không được trùng với mật khẩu cũ!');</script>";
            } else {
            // Cập nhật mật khẩu mới
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $new_password_hashed, $email);
                if ($update_stmt->execute()) {
                    echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='login.php';</script>";
                } else {
                    echo "<script>alert('Lỗi khi cập nhật mật khẩu!');</script>";
                }
            }
        }
        else {
            echo "<script>alert('Không tìm thấy người dùng với email này!');</script>";
            }
            $stmt->close();
            }
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="vi">
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
    <form method="POST">
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" placeholder="Nhập email" required>
        </div>
        <div class="input-group">
            <label for="new_password">Mật khẩu mới</label>
            <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
        </div>
        <div class="input-group verify-container">
            <input type="text" name="verification" placeholder="Mã xác thực">
            <button type="submit" class="verify-btn" name="send_code">Gửi mã</button>
        </div>
        <button class="btn" type="submit">Xác nhận</button>
    </form>
</div>
</body>
</html>
