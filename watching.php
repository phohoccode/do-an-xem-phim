<?php
include "./utils/index.php";


$baseUrl = "https://phimapi.com/phim";
$moviesData = [];
// lấy slug từ query string
$slug = $_GET['slug'];

$response = fetchData("$baseUrl/$slug");
$episodes = $response['episodes'][0]["server_data"];
// Lấy giá trị từ URL (nếu có)
$episodeParam = $_GET['episode'] ?? null;

// Mặc định lấy tập đầu tiên nếu không có episode được chọn
$currentEpisode = [
  "name" => $episodes[0]["name"],
  "slug" => $episodes[0]["slug"],
  "filename" => $episodes[0]["filename"],
  "link_embed" => $episodes[0]["link_embed"]
];

// Nếu `episode` có giá trị, tìm tập phim tương ứng
if ($episodeParam !== null) {
  foreach ($episodes as $episode) {
    if ($episode['slug'] === $episodeParam || $episode['filename'] === $episodeParam) {
      $currentEpisode = $episode;
      break;
    }
  }
}


$movie = $response['movie'];



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

  <div class="flex flex-col gap-4 text-gray-50 lg:px-14 max-w-[1560px] mt-12 mx-auto">
    <h1 class="text-2xl mb-6"><?= $currentEpisode['filename'] ?></h1>
    <iframe class="w-full h-[80vh] rounded-2xl" src="<?= $currentEpisode["link_embed"] ?>" frameBorder=" 0"
      allow="fullscreen"></iframe>
    <div class="mt-6 p-4 rounded-2xl lg:backdrop-blur-lg lg:bg-[#282b3a8a]">
      <h3 class="text-xl mb-4">Danh sách tập phim</h3>
      <div class="episode-list flex flex-wrap gap-2">
        <?php foreach ($episodes as $episode): ?>
          <a href="watching.php?slug=<?= $slug ?>&episode=<?= $episode['slug'] ?>"
            class="<?= $currentEpisode['slug'] == $episode['slug'] ? 'text-white bg-blue-700 hover:bg-blue-800font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none ' : 'py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10' ?>">
            <?= $episode['name'] ?>
          </a>
        <?php endforeach; ?>
      </div>

    </div>

    <?php include 'movie-suggestion.php'; ?>

  </div>

  <?php include 'footer.php'; ?>

</body>

</html>