<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Nếu người dùng nhấn nút "Lưu" để đổi tên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_username'])) {
    $new_username = trim($_POST['new_username']);
    if (!empty($new_username)) {
        $update = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update->bind_param("si", $new_username, $user_id);
        $update->execute();

        // Cập nhật session
        $_SESSION['username'] = $new_username;
    }
}

// Lấy thông tin user từ DB
$query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</style>

</head>
<body class="bg-dark text-white">
    <?php include 'navbar.php' ?>
    <div class="container mt-4">
        <!-- Ảnh nền -->
        <div class="mb-4 text-center">
            <img src="img/background.jpg" class="background-img" alt="Background">
        </div>
        
        <div class="d-flex align-items-start">
            <!-- Avatar -->
            <div class="me-4">
                <img src="img/user1.png" class="rounded-circle avatar" alt="Avatar">
            </div>
            
            <!-- Thông tin người dùng -->
            <div class="card flex-grow-1">
                <div class="card-header bg-dark text-white">
                    <i class="fa-solid fa-user-circle me-2"></i>
                    <strong>Thông tin người dùng</strong>
                </div>
                <div class="card-body bg-dark text-white">
                <form method="POST">
                <!-- Tên -->
                <div class="mb-3 d-flex align-items-center">
                    <label class="form-label me-2"><strong>Tên:</strong></label>
                    <input type="text" name="new_username" class="form-control me-2" style="max-width: 250px;"
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>

                <!-- Email (không sửa được) -->
                <div class="mb-3">
                    <label><strong>Email:</strong></label>
                    <input type="email" class="form-control bg-secondary text-white" 
                            style="max-width: 350px;" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                </form>
                </div>
            </div>

        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>



