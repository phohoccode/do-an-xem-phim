<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'connect.php'; // File kết nối database

$user_id = $_SESSION['user_id'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <style>
    body {
        background-color: #121212;
        color: #FFD700;
    }
    .card {
        background-color: #1E1E1E;
        border: 1px solid #FFD700;
    }
    .card-header {
        background-color: #FFD700;
        color: #121212;
        font-weight: bold;
    }
    .card-body {
        color: white; /* Changed text color to white */
    }
    .btn-custom {
        background-color: #FFD700;
        color: #121212;
        border: none;
        padding: 10px 20px;
        font-weight: bold;
        border-radius: 5px;
    }
    .btn-custom:hover {
        background-color: #E6C200;
    }
    .avatar {
        border: 3px solid #FFD700;
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    .background-img {
        width: 100%;
        height: 350px;
        object-fit: cover;
        border-radius: 10px;
    }
</style>

</head>
<body>
    <?php include 'navbar.php' ?>
    <div class="container mt-4">
        <!-- Ảnh nền -->
        <div class="mb-4 text-center">
            <img src="img/background.jpg" class="img-fluid background-img" alt="Background">
        </div>
        
        <div class="d-flex align-items-start">
            <!-- Avatar -->
            <div class="me-4">
                <img src="img/user1.png" class="rounded-circle avatar" alt="Avatar">
            </div>
            
            <!-- Thông tin người dùng -->
            <div class="card flex-grow-1">
                <div class="card-header text-center">
                    <strong>Thông tin người dùng</strong>
                </div>
                <div class="card-body">
                    <p><strong>Tên:</strong> <?php echo htmlspecialchars($_SESSION['username']) ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']) ?></p>
                    <div class="text-center mt-3">
                        <button class="btn btn-custom" href="changeinf.php">Chỉnh sửa thông tin</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>