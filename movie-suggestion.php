<?php
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
?>

<div class="flex flex-col gap-6 mt-12">
  <h4 class="text-gray-50 text-2xl">Gợi ý phim dành cho bạn</h4>

  <?php if (!empty($data['items'])): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
      <?php foreach ($data['items'] as $movie): ?>
        <div class="relative group">
          <div class="flex flex-col gap-2 group"
            href="/do-an-xem-phim/info.php?name=<?= $movie['name'] ?>&slug=<?= $movie['slug'] ?>">
            <div class="h-0 relative pb-[150%] rounded-xl overflow-hidden css-0 group flex items-center justify-center">
              <a href="/do-an-xem-phim/info.php?name=<?= $movie['name'] ?>&slug=<?= $movie['slug'] ?>">
                <img
                  class="border border-gray-800 h-full rounded-xl w-full absolute group-hover:brightness-75 inset-0 transition-all group-hover:scale-105"
                  src="<?= "https://phimimg.com/" . $movie['poster_url'] ?>" alt="<?= $movie['name'] ?>">
              </a>
              <a href="/do-an-xem-phim/watching.php?name=<?= $movie['name'] ?>&slug=<?= $movie['slug'] ?>"
                class="text-white text-center absolute bottom-2 left-2 right-2 opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-3 py-2 focus:outline-none">Xem
                ngay</a>
            </div>
            <span class="text-gray-50 text-xs group-hover:text-[#ffd875] lg:text-sm transition-all"
              style="-webkit-line-clamp: 2; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;"><?= $movie['name'] ?></span>
          </div>

          <div class="absolute top-2 left-2 flex gap-2 items-center flex-wrap">
            <span
              class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-300"><?= $movie['quality'] ?></span>
            <span
              class=" bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300"><?= $movie['lang'] ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-400 mt-4">Không có phim nào.</p>
  <?php endif; ?>
</div>

?>