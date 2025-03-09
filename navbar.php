<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> <!-- Liên kết Font Awesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <form class="d-flex" method="GET" action="index.php">
        <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm phim..." aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
      <div class="ms-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-flex align-items-center">
            <!-- Profile icon and username -->
            <a href="profile.php" class="btn btn-light d-flex align-items-center">
                <i class="fa fa-user me-2"></i>
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <a href="logout.php" class="btn btn-danger ms-2">Đăng xuất</a>
        </div>
    <?php else: ?>
        <!-- Show Login/Register if not logged in -->
        <a href="login.php" class="btn btn-primary">Đăng nhập</a>
        <a href="register.php" class="btn btn-light">Đăng ký</a>
    <?php endif; ?>
</div>

    </div>
  </div>
</nav>