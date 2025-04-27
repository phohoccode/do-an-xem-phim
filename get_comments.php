<?php
session_start();
require 'connect.php'; // kết nối database

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT comments.content, comments.created_at, users.username, users.avatar
                       FROM comments 
                       JOIN users ON comments.user_id = users.id 
                       WHERE comments.slug = ?
                       ORDER BY comments.created_at ASC");
$stmt->execute([$slug]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comments);
