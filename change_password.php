<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Bạn chưa đăng nhập.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');

// Kiểm tra định dạng mật khẩu mới
$pattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/';
if (!preg_match($pattern, $new_password)) {
    $_SESSION['message'] = "Mật khẩu mới phải có ít nhất 6 ký tự, gồm ít nhất 1 chữ in hoa, 1 chữ số và 1 ký tự đặc biệt.";
    header("Location: profile.php");
    exit();
}

// Lấy mật khẩu hiện tại từ DB
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Kiểm tra mật khẩu cũ
if (!$user || !password_verify($current_password, $user['password'])) {
    $_SESSION['message'] = "Mật khẩu hiện tại không đúng.";
    header("Location: profile.php");
    exit();
}

// Hash mật khẩu mới và cập nhật
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update->bind_param("si", $new_hashed, $user_id);
$update->execute();

$_SESSION['message'] = "Đổi mật khẩu thành công!";
header("Location: profile.php");
exit();
?>
