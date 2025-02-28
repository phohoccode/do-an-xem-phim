<?php
$baseUrl = "https://phimapi.com";
$limit = 12;

function fetchData($url)
{
  // Gửi một request đến $url và nhận lại nội dung của response dưới dạng chuỗi JSON.
  $response = file_get_contents($url);

  // Chuyển đổi chuỗi JSON nhận được từ $response thành một mảng PHP (true giúp chuyển thành mảng thay vì object).
  return json_decode($response, true);
}

$newMovies = fetchData("$baseUrl/danh-sach/phim-moi-cap-nhat?page=1&litmit=$limit");
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
  $moviesData[$className] = fetchData("$baseUrl/v1/api/danh-sach/$type?litmit=12");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link rel="stylesheet" href="./css/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <section class="slide-show">
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($newMovies['items'] as $index => $movie): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <a href="#">
              <img src="<?= $movie['thumb_url'] ?>" class="d-block w-100" alt="<?= $movie['name'] ?>">
              <div class="carousel-caption d-none d-md-block">
                <h5><?= $movie['name'] ?></h5>
                <p><?= $movie['origin_name'] ?></p>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
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
          <h3><?= $data['data']['breadCrumb'][0]['name'] ?? 'Danh mục' ?></h3>
          <?php foreach ($data['data']['items'] as $movie): ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
              <div class="card mb-5">
                <img src="https://phimimg.com/<?= $movie['thumb_url'] ?>" class="card-img-top" alt="<?= $movie['name'] ?>">
                <div class="card-body">
                  <h5 class="card-title text-truncate fs-6"><?= $movie['name'] ?></h5>
                  <p class="card-text text-truncate fs-6"><?= $movie['origin_name'] ?></p>
                  <a href="#" class="btn btn-primary">Xem ngay</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
</body>

</html>