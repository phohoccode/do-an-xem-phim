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
    <link rel="stylesheet" href="css/index.css">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  </head>

  <body>
    <?php include 'navbar.php'; ?>


    <div class="max-w-[1440px] mx-auto px-4 mt-5">

      <div class="p-3 rounded-4 d-flex justify-content-between align-items-center my-5" style="background-color: #F0F4F8;">
        <h3 class="fs-5"><?= htmlspecialchars($data["titlePage"]) ?></h3>
      </div>

      <div class="row">
        <?php if (!empty($data['items'])): ?>
           <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
              <?php foreach ($data['items'] as $movie): ?>
                <a
                  class="flex flex-col gap-2 group"
                  href="/do-an-xem-phim/thong-tin.php?name=<?= htmlspecialchars($movie['name']) ?>&slug=<?= htmlspecialchars($movie['slug']) ?>">
                  <div class="h-0 relative pb-[150%] rounded-xl overflow-hidden css-0 group flex items-center justify-center">
                    <img class="border border-gray-800 h-full rounded-xl w-full absolute group-hover:brightness-75 inset-0 transition-all group-hover:scale-105" src="<?= htmlspecialchars("https://phimimg.com/" . $movie['poster_url']) ?>"
                      alt="<?= htmlspecialchars($movie['name']) ?>">
                  </div>
                  <span class="text-gray-50 text-xs group-hover:text-[#ffd875] lg:text-sm transition-all" style="-webkit-line-clamp: 2; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">Cha Tôi, Người Ở Lại</span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
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

    <?php include 'footer.php'; ?>

  </body>

  </html>