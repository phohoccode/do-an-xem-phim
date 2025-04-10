<?php

session_start();
include 'connect.php';
include "./utils/index.php";

// Lưu phim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  if (!isset($_SESSION['user_id'])) {
      echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu phim.']);
      exit;
  }

  $user_id = $_SESSION['user_id'];
  $slug = $_POST['slug'] ?? '';
  $name = $_POST['name'] ?? '';
  $poster = $_POST['poster'] ?? '';
  $thumbnail = $_POST['thumbnail'] ?? '';
  $type = 'favorite';
  $action = $_POST['action'] ?? 'save';

  if (!$slug || !$name || !$poster || !$thumbnail) {
      echo json_encode(['success' => false, 'message' => 'Thiếu thông tin phim.']);
      exit;
  }

  $stmt = $conn->prepare("SELECT id FROM user_movies WHERE user_id = ? AND movie_slug = ? AND type = ?");
  $stmt->bind_param("iss", $user_id, $slug, $type);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
      echo json_encode(['success' => false, 'message' => 'Phim đã được lưu.']);
      exit;
  }

  $stmt = $conn->prepare("INSERT INTO user_movies (user_id, movie_slug, movie_name, movie_poster, movie_thumbnail, type)
                          VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssss", $user_id, $slug, $name, $poster, $thumbnail, $type);

  if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => 'Lưu phim thành công!']);
  } else {
      echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu: ' . $stmt->error]);
  }

  exit;
}

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

<body class="bg-dark">
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
            <button class="btn btn-light btn-sm save-movie-btn" 
              data-slug="<?= htmlspecialchars($movie['slug']) ?>"
              data-name="<?= htmlspecialchars($movie['name']) ?>"
              data-poster="<?= htmlspecialchars($movie['poster_url']) ?>"
              data-thumbnail="<?= htmlspecialchars($movie['thumb_url']) ?>">
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
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const saveBtn = document.querySelector('.save-movie-btn');

  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      const isSaved = this.dataset.saved === 'true';
      const slug = this.dataset.slug;
      const name = this.dataset.name;
      const poster = this.dataset.poster;
      const thumbnail = this.dataset.thumbnail;

      const action = isSaved ? 'delete' : 'save';

      const formData = new URLSearchParams({ slug, action });
      if (!isSaved) {
        formData.append('name', name);
        formData.append('poster', poster);
        formData.append('thumbnail', thumbnail);
      }

      fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          if (action === 'save') {
            this.innerHTML = `
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-x" viewBox="0 0 16 16">
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5z"/>
                <path d="M8.146 5.354a.5.5 0 0 1 .708 0L10 6.5l1.146-1.146a.5.5 0 0 1 .708.708L10.707 7.207l1.147 1.147a.5.5 0 0 1-.708.708L10 7.914 8.854 9.061a.5.5 0 1 1-.708-.708L9.293 7.207 8.146 6.06a.5.5 0 0 1 0-.707z"/>
              </svg>`;
            this.classList.remove('btn-light');
            this.classList.add('btn-secondary');
            this.dataset.saved = 'true';
          } else {
            this.innerHTML = `
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-plus" viewBox="0 0 16 16">
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5z"/>
                <path d="M8 4a.5.5 0 0 1 .5.5V6H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V7H6a.5.5 0 0 1 0-1h1.5V4.5A.5.5 0 0 1 8 4z"/>
              </svg>`;
            this.classList.remove('btn-secondary');
            this.classList.add('btn-light');
            this.dataset.saved = 'false';
          }
        }
      })
      .catch(error => {
        console.error('Lỗi:', error);
        alert("Không thể thực hiện thao tác.");
      });
    });
  }
});
</script>


</body>

</html>