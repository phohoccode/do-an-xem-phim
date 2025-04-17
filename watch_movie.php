<?php
session_start();
include 'connect.php';

$slug = $_POST['slug'] ?? '';
if (!isset($_SESSION['user_id'])) {
  header('Location: watching.php?slug=' . urlencode($slug));
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_SESSION['user_id'];
  $name = $_POST['name'] ?? '';
  $slug = $_POST['slug'] ?? '';
  $quality = $_POST['quality'] ?? '';
  $lang = $_POST['lang'] ?? '';
  $poster = $_POST['poster_url'] ?? '';
  $thumbnail = $_POST['thumbnail'] ?? '';
  $type = $_POST['type'] ?? 'history';

  $stmt = $conn->prepare("INSERT INTO user_movies (user_id, movie_name, movie_slug, movie_quality, movie_lang, movie_poster, movie_thumbnail, type, updated_at)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE 
                            updated_at = NOW(),
                            movie_poster = VALUES(movie_poster),
                            movie_thumbnail = VALUES(movie_thumbnail)");
  
  $stmt->bind_param("isssssss", $userId, $name, $slug, $quality, $lang, $poster, $thumbnail, $type);
  $stmt->execute();

  //  Redirect sau khi lưu
  header('Location: watching.php?slug=' . urlencode($slug));
  exit;
}
?>
