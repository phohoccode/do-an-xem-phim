<?php
session_start();
include 'connect.php';
include "./utils/index.php";

$baseUrl = "https://phimapi.com";
$limit = 12;

// Lấy danh sách phim mới
$newMovies = fetchData("$baseUrl/danh-sach/phim-moi-cap-nhat-v3?page=1&limit=$limit");

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="js/script.js"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <!-- Slide Show -->
  <section class="slide-show lg:pt-[32%] md:pt-[46%] pt-[50%] relative overflow-hidden">
    <?php if (!empty($newMovies['items'])): ?>
    <div class="swiper inset-0">
      <div class="swiper-wrapper">
        <?php foreach ($newMovies['items'] as $movie): ?>
        <div class="swiper-slide">
         <div class="text-gray-50">
            <img class="brightness-[0.85] absolute inset-0" src="<?= htmlspecialchars($movie['thumb_url']) ?>" alt="<?= htmlspecialchars($movie['name']) ?>">
            <div class="absolute w-[50%] px-4 bottom-12 left-0">
              <h2 class="lg:text-3xl md:text-2xl text-xl"><?= htmlspecialchars($movie['name']) ?></h2>
              <p class="my-2"><?= htmlspecialchars($movie['origin_name']) ?></p>
              <div class="flex gap-2 items-center flex-wrap mt-2">
                    <?php foreach ($movie['category'] as $category): ?>
                        <a 
                            href="/danh-muc/<?php echo htmlspecialchars($category['slug']); ?>" 
                            class="px-3 py-1 bg-[#52525280] text-xs flex items-center justify-center h-5 rounded-full text-gray-50 transition"
                        >
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex gap-2 items-center mt-6">
                 <a href=""> <button class="px-4 py-2 rounded-md bg-[#ffbe0b] text-gray-900 hover:shadow-[0_5px_10px_10px_rgba(255,218,125,.15)]">Xem ngay</button></a>
                  <a href=""> <button class="px-4 py-2 rounded-md bg-gray-200 text-gray-900">Thông tin</button></a>
                </div>
              </div>
         </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <div class="swiper-pagination"></div>
    </div>
    <?php else: ?>
    <p class="text-center text-muted">Không có phim nào.</p>
    <?php endif; ?>
  </section>


  <div class="lg:px-14 max-w-[1560px] mt-12 mx-auto">
    <div class="bg-gradient-to-b rounded-2xl from-[#282b3a] via-20% via-transparent flex flex-col gap-8 lg:p-8 p-4  ">
          <?php foreach ($moviesData as $className => $data): ?>
          <section class="<?= $className ?>">
            <div class="flex flex-col gap-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="lg:text-2xl text-lg text-gray-50"><?= htmlspecialchars($data['data']['breadCrumb'][0]['name'] ?? 'Danh mục') ?></h3>
                <?php
                    if (!empty($data['data']['seoOnPage']['og_url'])) {
                      $ogUrlParts = explode("/", $data['data']['seoOnPage']['og_url']); // Tách chuỗi theo dấu "/"
                      $describe = $ogUrlParts[0] ?? "danh-sach"; // Lấy phần "danh-sach"
                      $type = $ogUrlParts[1] ?? "phim-bo"; // Lấy phần loại phim
      
                      $href = "/do-an-xem-phim/chi-tiet.php?describe=" . urlencode($describe) . "&type=" . urlencode($type);
                    } else {
                      $href = "/do-an-xem-phim/chi-tiet.php";
                    }
                  ?>
      
                <a href="<?= $href ?>" class="flex items-center gap-2 text-gray-50 hover:text-[#ffbe0b]">Xem thêm
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                      d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                  </svg>
                </a>
              </div>
              <?php if (!empty($data['data']['items'])): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    <?php foreach ($data['data']['items'] as $movie): ?>
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
          </section>
          <?php endforeach; ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>


  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const swiper = new Swiper('.swiper', {
      effect: 'fade',
      fadeEffect: {
        crossFade: true,
      },
      loop: true,
      slidesPerView: 1,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
    
    });
  });
  </script>
</body>

</html>