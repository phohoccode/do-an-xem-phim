<?php
session_start(); // Đặt ở dòng đầu tiên
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý đổi tên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_username'])) {
    $new_username = trim($_POST['new_username']);
    if (!empty($new_username)) {
        $update = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update->bind_param("si", $new_username, $user_id);
        $update->execute();
        $_SESSION['username'] = $new_username;
        $_SESSION['message'] = "Cập nhật tên thành công!";
    }
}

// Xử lý upload avatar
if (isset($_POST['upload_avatar']) && isset($_FILES['avatar'])) {
    $uploadDir = 'uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmp = $_FILES['avatar']['tmp_name'];
    $fileName = basename($_FILES['avatar']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($fileExt, $allowedExt)) {
        $newFileName = 'user_' . $user_id . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param("si", $targetPath, $user_id);
            $stmt->execute();
            $_SESSION['message'] = "Đổi ảnh đại diện thành công!";
        }
    } else {
        $_SESSION['message'] = "Chỉ cho phép ảnh JPG, JPEG, PNG hoặc WEBP.";
    }
}

// Lấy thông tin user
$query = $conn->prepare("SELECT username, email, avatar FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Lấy số phim đã lưu
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM user_movies WHERE user_id = ? AND save_type = 'favorite'");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$countResult = $countStmt->get_result();
$movieCount = $countResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto mt-6">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-center">
                <?= $_SESSION['message'] ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="mb-6 text-center">
            <img src="img/background.jpg" class="w-full h-72 object-cover rounded-xl" alt="Background">
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Avatar -->
            <div class="flex flex-col items-center">
                <img src="<?= htmlspecialchars($user['avatar'] ?? 'img/user1.png') ?>" class="rounded-full border-4 border-yellow-400 w-32 h-32 object-cover transition-transform duration-300 hover:scale-105" alt="Avatar">
                <form method="POST" enctype="multipart/form-data" class="mt-3 relative">
                    <label for="avatarInput" class="cursor-pointer inline-block bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded transition-all duration-300">
                        Đổi ảnh đại diện
                        <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                    </label>
                    <input type="hidden" name="upload_avatar" value="1">
                </form>
            </div>

            <!-- Thông tin người dùng -->
            <div class="flex-1 bg-gray-800 p-6 rounded-xl shadow-lg transition-all duration-500 hover:shadow-2xl">
                <h2 class="text-2xl font-bold text-yellow-400 mb-4">
                    <i class="fa-solid fa-user-circle me-2"></i>Thông tin người dùng
                </h2>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Tên người dùng:</label>
                        <div class="flex gap-2">
                            <input type="text" name="new_username" class="w-full px-3 py-2 text-black rounded" value="<?= htmlspecialchars($user['username']) ?>" required>
                            <button type="submit" class="bg-green-500 hover:bg-green-400 text-white px-4 rounded transition-all duration-300">Lưu</button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Email:</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-3 py-2 text-white bg-gray-700 rounded" disabled>
                    </div>
                </form>

                <p class="mb-2"><strong>Số phim đã lưu:</strong> <?= $movieCount ?></p>
                <button onclick="openModal()" class="inline-block mt-3 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded transition-all duration-300">Đổi mật khẩu</button>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md text-black relative animate-fade-in">
            <h2 class="text-xl font-semibold mb-4">Đổi mật khẩu</h2>
            <form action="change_password.php" method="POST">
                <div class="mb-3">
                    <label class="block font-medium">Mật khẩu hiện tại:</label>
                    <input type="password" name="current_password" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div class="mb-3">
                    <label class="block font-medium">Mật khẩu mới:</label>
                    <input type="password" name="new_password" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 rounded hover:bg-gray-500 text-white">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded text-white">Lưu</button>
                </div>
            </form>
            <button onclick="closeModal()" class="absolute top-2 right-3 text-gray-600 hover:text-black text-xl">&times;</button>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('changePasswordModal').classList.add('hidden');
        }
    </script>
</body>
</html>
