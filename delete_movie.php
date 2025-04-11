<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slug']) && isset($_POST['type'])) {
  $userId = $_SESSION['user_id'];
  $slug = $_POST['slug'];
  $type = $_POST['type']; // favorite hoặc watched

  // Kiểm tra giá trị type chỉ nhận 'favorite' hoặc 'history' để tránh SQL injection
  if (!in_array($type, ['favorite', 'history'])) {
    header("Location: savedmovies.php");
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM user_movies WHERE user_id = ? AND movie_slug = ? AND type = ?");
  $stmt->bind_param("iss", $userId, $slug, $type);
  $stmt->execute();
}

// Quay lại trang gốc dựa vào type
if ($type === 'history') {
  header("Location: viewhistory.php");
} else {
  header("Location: savedmovies.php");
}
exit;
