  <?php

  // session_start();
  include 'connect.php';
  include "./utils/index.php";

  // định nghĩa base url
  $baseUrl = "https://phimapi.com/v1/api";
  $limit = 24;

  // lấy slug từ query string
  $describe = $_GET['describe'];
  $type = $_GET['type'];
  $page = $_GET['page'] ?? "1";


  $response = fetchData("$baseUrl/$describe/$type?limit=$limit&page=$page");
  $data = $response['data'];
  $totalPages = $data["params"]["pagination"]["totalPages"];;


  echo "<script>console.log(" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";
  echo "<script>console.log(" . json_encode($totalPages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";


  ?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  <body>
    <?php include 'navbar.php'; ?>


    <div class="container">

      <div class="p-3 rounded-4 d-flex justify-content-between align-items-center my-5" style="background-color: #F0F4F8;">
        <h3 class="fs-2"><?= htmlspecialchars($data["titlePage"]) ?></h3>
      </div>

      <div class="row">
        <?php if (!empty($data['items'])): ?>
          <?php foreach ($data['items'] as $movie): ?>
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


      <ul class="pagination my-5 justify-content-center">
        <!-- Nút Previous -->
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=<?= $describe ?>&type=<?= $type ?>&page=<?= $page - 1 ?>">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>

        <?php
        $maxPagesToShow = 5;

        if ($totalPages <= $maxPagesToShow) {
          // Hiển thị toàn bộ trang nếu tổng số trang nhỏ hơn hoặc bằng $maxPagesToShow
          for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
            echo '<a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=' . $describe . '&type=' . $type . '&page=' . $i . '">' . $i . '</a>';
            echo '</li>';
          }
        } else {
          // Hiển thị 5 trang đầu tiên hoặc từ vị trí hiện tại
          $start = max(1, $page - 2); // Bắt đầu hiển thị từ trang hiện tại - 2
          $end = min($totalPages, $start + $maxPagesToShow - 1); // Kết thúc hiển thị

          // Nếu không phải đang ở trang đầu tiên, hiển thị trang 1
          if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=' . $describe . '&type=' . $type . '&page=1">1</a></li>';
            if ($start > 2) { // Nếu khác xa thì hiển thị dấu "..."
              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
          }

          // Hiển thị các trang trong khoảng $start đến $end
          for ($i = $start; $i <= $end; $i++) {
            echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
            echo '<a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=' . $describe . '&type=' . $type . '&page=' . $i . '">' . $i . '</a>';
            echo '</li>';
          }

          // Nếu chưa hiển thị đến trang cuối cùng, thêm dấu "..." và trang cuối
          if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=' . $describe . '&type=' . $type . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
          }
        }
        ?>

        <!-- Nút Next -->
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
          <a class="page-link" href="/do-an-xem-phim/chi-tiet.php?describe=<?= $describe ?>&type=<?= $type ?>&page=<?= $page + 1 ?>">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>


    </div>
  </body>

  </html>