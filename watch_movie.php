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
  $poster = $_POST['poster'] ?? '';
  $thumbnail = $_POST['thumbnail'] ?? '';
  $episode = $_POST['episode'] ?? '';
  $movie_type = $_POST['type_movie'] ?? ''; 
  if (empty($episode)) {
  $episode = ($movie_type === 'single') ? 'Full' : 'Tập 01';
  }
  
  $save_type = $_POST['save_type'] ?? 'history';

  // Kiểm tra bản ghi đã tồn tại hôm nay chưa
  $checkStmt = $conn->prepare("SELECT id FROM user_movies 
                               WHERE user_id = ? AND movie_slug = ? AND movie_episode = ? AND save_type = ? AND movie_type = ?
                               AND DATE(updated_at) = CURDATE()");
  $checkStmt->bind_param("issss", $userId, $slug, $episode, $save_type, $movie_type);
  $checkStmt->execute();
  $checkStmt->store_result();

  if ($checkStmt->num_rows > 0) {
    // Nếu đã có -> chỉ update updated_at và ảnh (nếu có)
    $updateStmt = $conn->prepare("UPDATE user_movies 
                                  SET updated_at = NOW(), 
                                      movie_poster = ?, 
                                      movie_thumbnail = ? 
                                  WHERE user_id = ? AND movie_slug = ? AND movie_episode = ? AND save_type = ? AND movie_type = ?
                                  AND DATE(updated_at) = CURDATE()");
    $updateStmt->bind_param("ssissss", $poster, $thumbnail, $userId, $slug, $episode, $save_type, $movie_type);
    $updateStmt->execute();
    $updateStmt->close();
  } else {
    // Nếu chưa có -> thêm mới
    $insertStmt = $conn->prepare("INSERT INTO user_movies 
      (user_id, movie_name, movie_slug, movie_quality, movie_lang, movie_poster, movie_thumbnail, movie_episode, save_type, movie_type, updated_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $insertStmt->bind_param("isssssssss", $userId, $name, $slug, $quality, $lang, $poster, $thumbnail,  $episode, $save_type, $movie_type);
    $insertStmt->execute();
    $insertStmt->close();
  }

  $checkStmt->close();

  // Quay lại trang xem phim
  header('Location: watching.php?slug=' . urlencode($slug) . '&episode=' . urlencode($episode));
  exit;
}
?>
