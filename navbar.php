<?php
session_start();
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">MOVIES-VLUTE</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Trang chủ</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Danh mục</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Phim lẻ</a></li>
            <li><a class="dropdown-item" href="#">Phim bộ</a></li>
            <li><a class="dropdown-item" href="#">Tv Shows</a></li>
            <li><a class="dropdown-item" href="#">Phim Vietsub</a></li>
            <li><a class="dropdown-item" href="#">Phim thuyết minh</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Xem thêm</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Phim đã lưu</a></li>
            <li><a class="dropdown-item" href="#">Lịch sử đã xem</a></li>
          </ul>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Tìm kiếm phim..." aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
      <div class="ms-4">
        <?php if (isset($_SESSION['user'])): ?>
          <a href="logout.php" class="btn btn-danger"> Đăng xuất </a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary"> Đăng nhập </a>
          <a href="register.php" class="btn btn-light"> Đăng ký </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>