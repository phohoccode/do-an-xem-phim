<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_SESSION['user_id'];
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';
  $poster = $_POST['poster'] ?? '';
  $thumbnail = $_POST['thumbnail'] ?? '';
  $type = $_POST['type'] ?? 'history';

  $stmt = $conn->prepare("INSERT INTO user_movies (user_id, movie_name, movie_slug, movie_poster, movie_thumbnail, type, updated_at)
                          VALUES (?, ?, ?, ?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE updated_at = NOW(), movie_poster = VALUES(movie_poster), movie_thumbnail = VALUES(movie_thumbnail)");
  $stmt->bind_param("issss", $userId, $name, $slug, $poster, $thumbnail, $type);
  $stmt->execute();

  echo json_encode(['status' => 'success']);
}
