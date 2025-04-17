<?php
session_start();
include './connect.php';
include "./utils/index.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');

  if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để sử dụng tính năng này.']);
    exit;
  }

  $user_id = $_SESSION['user_id'];
  $slug = $_POST['slug'] ?? '';
  $name = $_POST['name'] ?? '';
  $poster = $_POST['poster'] ?? '';
  $thumbnail = $_POST['thumbnail'] ?? '';
  $type = $_POST['type'] ?? 'favorite';
  $action = $_POST['action'] ?? '';

  if (!$slug || !$action) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin gửi lên.']);
    exit;
  }

  if ($action === 'save') {
    $stmt = $conn->prepare("SELECT id FROM user_movies WHERE user_id = ? AND movie_slug = ? AND type = ?");
    $stmt->bind_param("iss", $user_id, $slug, $type);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
      $stmt = $conn->prepare("INSERT INTO user_movies (user_id, movie_slug, movie_name, movie_poster, movie_thumbnail, type) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("isssss", $user_id, $slug, $name, $poster, $thumbnail, $type);
      if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Phim đã được lưu.', 'saved' => true]);
      } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu phim.']);
      }
    } else {
      echo json_encode(['success' => false, 'message' => 'Phim đã có trong danh sách.', 'saved' => true]);
    }
  } elseif ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM user_movies WHERE user_id = ? AND movie_slug = ? AND type = ?");
    $stmt->bind_param("iss", $user_id, $slug, $type);
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => 'Đã xóa phim khỏi danh sách.', 'saved' => false]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa phim.']);
    }
  }

  exit;
}


// Lấy thông tin phim từ API
$baseUrl = "https://phimapi.com/phim";
$slug = $_GET['slug'] ?? '';
$response = fetchData("$baseUrl/$slug");

$movie = $response['movie'];
$category = $movie['category'] ?? [];
$country = $movie['country'] ?? [];
$director = $movie['director'] ?? [];
$actor = $movie['actor'] ?? [];
$tmdb = $movie['tmdb'] ?? [];
$trailerUrl = isset($movie['trailer_url']) ? convertToEmbedUrl($movie['trailer_url']) : '';
$posterUrl = $movie['poster_url'] ?? '';
$thumbUrl = $movie['thumb_url'] ?? '';
$episodes = $response['episodes'] ?? [];

// random phim
$categories = [
  "hanh-dong",
  "mien-tay",
  "tre-em",
  "lich-su",
  "co-trang",
  "chien-tranh",
  "vien-tuong",
  "kinh-di",
  "tai-lieu",
  "bi-an",
  "tinh-cam",
  "tam-ly",
  "the-thao",
  "phieu-luu",
  "am-nhac",
  "gia-dinh",
  "hoc-duong",
  "hai-huoc",
  "hinh-su",
  "vo-thuat",
  "khoa-hoc",
  "than-thoai",
  "chinh-kich",
  "kinh-dien"
];

$countries = [
  "viet-nam",
  "trung-quoc",
  "thai-lan",
  "hong-kong",
  "phap",
  "duc",
  "ha-lan",
  "mexico",
  "thuy-dien",
  "philippines",
  "dan-mach",
  "thuy-si",
  "ukraina",
  "han-quoc",
  "au-my",
  "an-do",
  "canada",
  "tay-ban-nha",
  "indonesia",
  "ba-lan",
  "malaysia",
  "bo-dao-nha",
  "uae",
  "chau-phi",
  "a-rap-xe-ut",
  "nhat-ban",
  "dai-loan",
  "anh",
  "quoc-gia-khac",
  "tho-nhi-ky",
  "nga",
  "uc",
  "brazil",
  "y",
  "na-uy",
  "namh",
  "kinh-dien"
];
$describe = 'the-loai';
$type = '';

if (!empty($categories) && !empty($countries)) {
  $isFromCountry = rand(0, 1); // 50/50 random quốc gia hoặc thể loại

  if ($isFromCountry) {
    $describe = 'quoc-gia';
    $randomItem = $countries[array_rand($countries)];
  } else {
    $describe = 'the-loai';
    $randomItem = $categories[array_rand($categories)];
  }

  $type = $randomItem;
}

$data = [];
if ($describe && $type) {
  $limit = 24;
  $page = 1;
  $randomMovies = fetchData("https://phimapi.com/v1/api/$describe/$type?limit=$limit&page=$page");
  $data = $randomMovies['data'] ?? [];
}

// Kiểm tra xem phim đã được lưu chưa (để cập nhật data-saved)
$isSaved = false;
if (isset($_SESSION['user_id'])) {
  $stmt = $conn->prepare("SELECT id FROM user_movies WHERE user_id = ? AND movie_slug = ? AND type = 'favorite'");
  $stmt->bind_param("is", $_SESSION['user_id'], $movie['slug']);
  $stmt->execute();
  $stmt->store_result();
  $isSaved = $stmt->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin phim</title>
  <link rel="stylesheet" href="css/index.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-dark">
  <?php include 'navbar.php'; ?>

  <div class="lg:px-14 max-w-[1560px] mt-12 mx-auto">
    <div class="grid grid-cols-12 gap-8">
      <div class="col-span-2">
        <div class="flex flex-col gap-2">
          <div class="relative pt-[150%] w-full overflow-hidden rounded-lg">
            <img class="absolute inset-0 w-full h-full object-cover" src="<?= $movie['poster_url'] ?>"
              alt="<?= $movie['name'] ?>">
          </div>
          <div class="flex justify-between items-center gap-4 mt-2">
            <a href="/do-an-xem-phim/watching.php?name=<?= $movie['name'] ?>&slug=<?= $movie['slug'] ?>"
              class="flex-1 watch-now-btn justify-center text-gray-50 text-center whitespace-nowrap flex items-center bg-blue-700 hover:bg-blue-80 font-medium rounded-lg text-sm px-3 py-2 focus:outline-none">
              <svg class="w-[24px] h-[24px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M8.6 5.2A1 1 0 0 0 7 6v12a1 1 0 0 0 1.6.8l8-6a1 1 0 0 0 0-1.6l-8-6Z"
                  clip-rule="evenodd" />
              </svg>
              Xem ngay</a>
            <button
              class="p-2 save-movie-btn border-none text-sm font-medium rounded-lg border transition-all duration-300
                <?= $isSaved ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-white text-gray-900 hover:bg-gray-100' ?>"
              data-saved="<?= $isSaved ? 'true' : 'false' ?>" data-slug="<?= $movie['slug'] ?>"
              data-name="<?= $movie['name'] ?>" data-poster="<?= $posterUrl ?>" data-thumbnail="<?= $thumbUrl ?>">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M7.833 2c-.507 0-.98.216-1.318.576A1.92 1.92 0 0 0 6 3.89V21a1 1 0 0 0 1.625.78L12 18.28l4.375 3.5A1 1 0 0 0 18 21V3.889c0-.481-.178-.954-.515-1.313A1.808 1.808 0 0 0 16.167 2H7.833Z" />
              </svg>
            </button>

          </div>
        </div>
      </div>
      <div class="text-gray-200 col-span-10">
        <div class="">
          <div class="lg:backdrop-blur-lg lg:bg-[#282b3a8a] mb-4 rounded-lg p-4 text-center">
            <h4 class="mb-2 text-gray-50 text-xl"><?= $movie['name'] ?></h4>
            <span class="text-sm text-gray-300"><?= $movie['origin_name'] ?></span>
          </div>
          <div class="lg:backdrop-blur-lg lg:bg-[#282b3a8a] rounded-lg p-4">
            <div class="grid grid-cols-12">
              <div class="col-span-6">
                <div class="flex flex-col gap-2">
                  <span>
                    <strong>Tình trạng:</strong>
                    <span><?= $movie['episode_current'] ?></span>
                  </span>
                  <span>
                    <strong>Số tập:</strong>
                    <span><?= $movie['episode_total'] ?></span>
                  </span>
                  <span>
                    <strong>Thời lượng:</strong>
                    <span><?= $movie['time'] ?></span>
                  </span>
                  <span>
                    <strong>Năm phát hành:</strong>
                    <span><?= $movie['year'] ?></span>
                  </span>
                  <span>
                    <strong>Chất lượng:</strong>
                    <span><?= $movie['quality'] ?></span>
                  </span>
                  <span>
                    <strong>Bình chọn trung bình:</strong>
                    <span><?= $tmdb['vote_average'] ?></span>
                  </span>
                  <span>
                    <strong>Lượt bình chọn:</strong>
                    <span><?= $tmdb['vote_count'] ?></span>
                  </span>
                </div>
              </div>
              <div class="col-span-6">
                <div class="flex flex-col gap-2">
                  <div class="flex gap-2 items-center flex-wrap">
                    <strong>Thể loại:</strong>
                    <?php foreach ($category as $index => $item): ?>
                      <a href="/do-an-xem-phim/chi-tiet.php?describe=the-loai&type=<?= $item['slug'] ?>"
                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300"><?= $item['name'] ?></a>
                    <?php endforeach; ?>
                  </div>
                  <div class="flex gap-2 items-center flex-wrap">
                    <strong>Quốc gia:</strong>
                    <?php foreach ($country as $index => $item): ?>
                      <a href="/do-an-xem-phim/chi-tiet.php?describe=quoc-gia&type=<?= $item['slug'] ?>"
                        class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-sm dark:bg-purple-900 dark:text-purple-300"><?= $item['name'] ?></a>
                    <?php endforeach; ?>
                  </div>
                  <div class="flex gap-2 items-center flex-wrap">
                    <strong>Đạo diễn:</strong>
                    <?php foreach ($director as $index => $item): ?>
                      <span><?= $item ?></span>
                    <?php endforeach; ?>
                  </div>
                  <div class="flex gap-2 items-center flex-wrap">
                    <strong>Diễn viên:</strong>
                    <?php foreach ($actor as $index => $item): ?>
                      <spam><?= $item ?></spam>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <div class="mt-2 col-span-12">
                <span>
                  <strong>Mô tả:</strong>
                </span>
                <p><?= $movie['content'] ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-12">
      <div class="flex items-center gap-1 text-gray-100 text-2xl mb-4">
        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
          fill="currentColor" viewBox="0 0 24 24">
          <path fill-rule="evenodd"
            d="M21.7 8.037a4.26 4.26 0 0 0-.789-1.964 2.84 2.84 0 0 0-1.984-.839c-2.767-.2-6.926-.2-6.926-.2s-4.157 0-6.928.2a2.836 2.836 0 0 0-1.983.839 4.225 4.225 0 0 0-.79 1.965 30.146 30.146 0 0 0-.2 3.206v1.5a30.12 30.12 0 0 0 .2 3.206c.094.712.364 1.39.784 1.972.604.536 1.38.837 2.187.848 1.583.151 6.731.2 6.731.2s4.161 0 6.928-.2a2.844 2.844 0 0 0 1.985-.84 4.27 4.27 0 0 0 .787-1.965 30.12 30.12 0 0 0 .2-3.206v-1.516a30.672 30.672 0 0 0-.202-3.206Zm-11.692 6.554v-5.62l5.4 2.819-5.4 2.801Z"
            clip-rule="evenodd" />
        </svg>

        <h4>Trailer</h4>
      </div>
      <div class="border border-gray-900 rounded-lg overflow-hidden pt-[32%] relative">
        <iframe class="w-full h-full absolute inset-0" src="<?= $trailerUrl ?>" title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>
      </div>
    </div>

    <?php include 'movie-suggestion.php'; ?>

  </div>
  <?php include 'footer.php'; ?>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const saveBtn = document.querySelector('.save-movie-btn');

      if (saveBtn) {
        saveBtn.addEventListener('click', function () {
          const btn = this;
          const isSaved = btn.dataset.saved === 'true';
          const slug = btn.dataset.slug;
          const name = btn.dataset.name;
          const poster = btn.dataset.poster;
          const thumbnail = btn.dataset.thumbnail;

          const action = isSaved ? 'delete' : 'save';
          const formData = new URLSearchParams({
            slug,
            action,
            type: 'favorite'
          });

          if (!isSaved) {
            formData.append('name', name);
            formData.append('poster', poster);
            formData.append('thumbnail', thumbnail);
          }

          fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          })
            .then(res => res.json())
            .then(data => {
              alert(data.message);
              if (data.success) {
                btn.dataset.saved = data.saved ? 'true' : 'false';

                if (data.saved) {
                  btn.classList.remove('bg-white', 'text-gray-900', 'hover:bg-gray-100');
                  btn.classList.add('bg-red-600', 'text-white', 'hover:bg-red-700');
                } else {
                  btn.classList.remove('bg-red-600', 'text-white', 'hover:bg-red-700');
                  btn.classList.add('bg-white', 'text-gray-900', 'hover:bg-gray-100');
                }
              }
            })
            .catch(err => {
              console.error(err);
              alert('Đã có lỗi xảy ra.');
            });
        });
      }
    });
  </script>



</body>

</html>