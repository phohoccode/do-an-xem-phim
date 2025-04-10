<?php
session_start();
include 'connect.php'; // Đảm bảo bạn đã có file này để kết nối MySQL

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user_id'];

// Truy vấn danh sách phim đã lưu
$sql = "SELECT * FROM user_movies WHERE user_id = ? AND type = 'favorite' ORDER BY updated_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$movies = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Phim đã lưu - VLUTE-FILM</title>
  <link rel="stylesheet" href="css/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <?php include "navbar.php"; ?>
  <div class="container">
    <div class="p-3 rounded-4 d-flex justify-content-between align-items-center my-5" style="background-color: #F0F4F8;">
      <h3><i class="fa-solid fa-bookmark me-3"></i>Danh sách phim đã lưu</h3>
      <button class="btn btn-danger">Xóa tất cả</button>
    </div>

    <div class="row">
      <?php if (!empty($movies)): ?>
        <?php foreach ($movies as $movie): ?>
          <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <div class="mb-4 card-movie">
              <a href="/do-an-xem-phim/thong-tin.php?name=<?= urlencode($movie['movie_name']) ?>&slug=<?= urlencode($movie['movie_slug']) ?>">
                <img src="<?= htmlspecialchars($movie['movie_poster']) ?>" alt="<?= htmlspecialchars($movie['movie_name']) ?>" />
              </a>
              <div class="card-movie-body">
                <p class="text-truncate card-movie-title"><?= htmlspecialchars($movie['movie_name']) ?></p>
                <a href="/do-an-xem-phim/dang-xem.php?name=<?= urlencode($movie['movie_name']) ?>&slug=<?= urlencode($movie['movie_slug']) ?>" class="btn btn-primary btn-sm w-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                    <path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                  </svg>
                  Xem ngay
                </a>
              </div>
              <!-- Nếu bạn có trường lang hoặc time thì hiển thị -->
              <?php if (!empty($movie['lang']) || !empty($movie['time'])): ?>
                <div class="card-movie-status">
                  <?php if (!empty($movie['lang'])): ?>
                    <span class="badge rounded-pill bg-primary"><?= htmlspecialchars($movie['lang']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($movie['time'])): ?>
                    <span class="badge rounded-pill bg-secondary"><?= htmlspecialchars($movie['time']) ?></span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">Bạn chưa lưu bộ phim nào.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
