<?php
session_start();
include 'connect.php';
include "./utils/index.php";

$baseUrl = "https://phimapi.com";
$limit = 12;

// Lấy danh sách phim mới
$newMovies = fetchData("$baseUrl/danh-sach/phim-moi-cap-nhat?page=1&limit=$limit");

// Danh mục phim
$categories = [
  "phim-le" => "series",
  "phim-bo" => "single",
  "tv-shows" => "tv-shows",
  "hoat-hinh" => "cartoon",
  "phim-vietsub" => "vietsub",
  "phim-thuyet-minh" => "explanation",
  "phim-long-tieng" => "voiceover"
];

$moviesData = [];

foreach ($categories as $type => $className) {
  $moviesData[$className] = fetchData("$baseUrl/v1/api/danh-sach/$type?limit=$limit");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link rel="stylesheet" href="css/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <!-- Slide Show -->
  <section class="slide-show">
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php if (!empty($newMovies['items'])): ?>
          <?php foreach ($newMovies['items'] as $index => $movie): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <a href="#">
                <img src="<?= htmlspecialchars($movie['thumb_url']) ?>" alt="<?= htmlspecialchars($movie['name']) ?>">
                <div class="carousel-caption d-none d-md-block">
                  <h5><?= htmlspecialchars($movie['name']) ?></h5>
                  <p><?= htmlspecialchars($movie['origin_name']) ?></p>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted">Không có phim nào.</p>
        <?php endif; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </section>

  <div class="container">
    <?php foreach ($moviesData as $className => $data): ?>
      <section class="<?= $className ?>">
        <div class="row mt-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><?= htmlspecialchars($data['data']['breadCrumb'][0]['name'] ?? 'Danh mục') ?></h3>
            <a href="#" class="d-flex align-items-center btn btn-primary btn-sm">Xem thêm
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
              </svg>
            </a>
          </div>
          <?php if (!empty($data['data']['items'])): ?>
            <?php foreach ($data['data']['items'] as $movie): ?>
              <div class="col-6 col-sm-6 col-md-3 col-lg-2">
                <div class="mb-4 card-movie">
                  <a href="/do-an-xem-phim/thong-tin.php?name=<?= htmlspecialchars($movie['name']) ?>&slug=<?= htmlspecialchars($movie['slug']) ?>">
                    <img src="<?= htmlspecialchars("https://phimimg.com/" . $movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['name']) ?>">
                  </a>
                  <div class="card-movie-body">
                    <p class="text-truncate card-movie-title"><?= htmlspecialchars($movie['name']) ?></p>
                    <a href="/do-an-xem-phim/dang-xem.php?name=<?= htmlspecialchars($movie['name']) ?>&slug=<?= htmlspecialchars($movie['slug']) ?>" style="width: 100%;" class="btn btn-primary btn-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" style="margin-bottom: 2px;" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                        <path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z" />
                      </svg>
                      Xem ngay
                    </a>
                  </div>
                  <div class="card-movie-status">
                    <span class="badge rounded-pill bg-primary"><?= htmlspecialchars($movie['lang']) ?></span>
                    <span class="badge rounded-pill bg-secondary"><?= htmlspecialchars($movie['time']) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">Không có phim nào.</p>
          <?php endif; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
</body>

</html>