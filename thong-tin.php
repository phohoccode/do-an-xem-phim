<?php

session_start();
include 'connect.php';
include "./utils/index.php";

// định nghĩa base url
$baseUrl = "https://phimapi.com/phim";
$moviesData = [];

// lấy slug từ query string
$slug = $_GET['slug'];

$response = fetchData("$baseUrl/$slug");
$episodes = $response['episodes'];
$movie = $response['movie'];
$category = $movie['category'];
$country = $movie['country'];
$director = $movie['director'];
$actor = $movie['actor'];
$tmdb = $movie['tmdb'];
$trailerUrl = isset($movie['trailer_url']) ? convertToEmbedUrl($movie['trailer_url']) : '';


echo "<script>console.log(" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";
echo "<script>console.log(" . json_encode($episodes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";
echo "<script>console.log(" . json_encode($category, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="css/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container mt-5 mb-5">
    <div class="row">
      <div class="col-12 col-lg-2">
        <div class="movie-info-left">
          <figure class="movie-info-figure">
            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['name']) ?>">
          </figure>
          <div style="gap: 12px;" class="d-flex justify-content-between align-items-center">
            <a href="/do-an-xem-phim/dang-xem.php?name=<?= htmlspecialchars($movie['name']) ?>&slug=<?= htmlspecialchars($movie['slug']) ?>" style="flex: 1;" class="btn btn-primary btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" style="margin-bottom: 2px;" height="16" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16">
                <path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z" />
              </svg>
              Xem ngay</a>
            <button class="btn btn-light btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-plus" viewBox="0 0 16 16">
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z" />
                <path d="M8 4a.5.5 0 0 1 .5.5V6H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V7H6a.5.5 0 0 1 0-1h1.5V4.5A.5.5 0 0 1 8 4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-10">
        <div class="movie-info-right">
          <div class="movie-info-title" style="text-align: center;">
            <h4 style="margin-bottom: 8px; color: #0b6bcb;"><?= htmlspecialchars($movie['name']) ?></h3>
              <span><?= htmlspecialchars($movie['origin_name']) ?></span>
          </div>
          <div class="movie-info-body">
            <div class="row">
              <div class="col-6">
                <div class="movie-info-description">
                  <span>
                    <strong>Tình trạng:</strong>
                    <span><?= htmlspecialchars($movie['episode_current']) ?></span>
                  </span>
                  <span>
                    <strong>Số tập:</strong>
                    <span><?= htmlspecialchars($movie['episode_total']) ?></span>
                  </span>
                  <span>
                    <strong>Thời lượng:</strong>
                    <span><?= htmlspecialchars($movie['time']) ?></span>
                  </span>
                  <span>
                    <strong>Năm phát hành:</strong>
                    <span><?= htmlspecialchars($movie['year']) ?></span>
                  </span>
                  <span>
                    <strong>Chất lượng:</strong>
                    <span><?= htmlspecialchars($movie['quality']) ?></span>
                  </span>
                  <span>
                    <strong>Bình chọn trung bình:</strong>
                    <span><?= htmlspecialchars($tmdb['vote_average']) ?></span>
                  </span>
                  <span>
                    <strong>Lượt bình chọn:</strong>
                    <span><?= htmlspecialchars($tmdb['vote_count']) ?></span>
                  </span>
                </div>
              </div>
              <div class="col-6">
                <div class="movie-info-description">
                  <span>
                    <strong>Thể loại:</strong>
                    <?php foreach ($category as $index => $item): ?>
                      <a href="#" style="text-decoration: none;" class="badge rounded-pill bg-primary"><?= htmlspecialchars($item['name']) ?></a>
                    <?php endforeach; ?>
                  </span>
                  <span>
                    <strong>Quốc gia:</strong>
                    <?php foreach ($country as $index => $item): ?>
                      <a href="#" style="text-decoration: none;" class="badge rounded-pill bg-primary"><?= htmlspecialchars($item['name']) ?></a>
                    <?php endforeach; ?>
                  </span>
                  <span>
                    <strong>Đạo diễn:</strong>
                    <?php foreach ($director as $index => $item): ?>
                      <a href="#" style="text-decoration: none;" class="badge rounded-pill bg-primary"><?= htmlspecialchars($item) ?></a>
                    <?php endforeach; ?>
                  </span>
                  <span>
                    <strong>Diễn viên:</strong>
                    <?php foreach ($actor as $index => $item): ?>
                      <a href="#" style="text-decoration: none;" class="badge rounded-pill bg-primary "><?= htmlspecialchars($item) ?></a>
                    <?php endforeach; ?>
                  </span>
                </div>
              </div>

              <div class="col-12">
                <div class="mt-2">
                  <span>
                    <strong>Mô tả:</strong>
                  </span>
                  <p><?= htmlspecialchars($movie['content']) ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h4>Trailer</h4>
      <div class="movie-info-trailer">
        <iframe width="100%" height="500" src="<?= htmlspecialchars($trailerUrl) ?>"
          title="YouTube video player" frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>

</body>

</html>