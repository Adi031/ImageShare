<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$image_id = $_POST['image_id'] ?? 0;

if ($action === 'like') {
    // Check if already liked
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND image_id = ?");
    $stmt->bind_param("ii", $user_id, $image_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        // Unlike
        $stmt_del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt_del->bind_param("ii", $user_id, $image_id);
        $stmt_del->execute();
        $is_liked = false;
    } else {
        // Like
        $stmt_ins = $conn->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
        $stmt_ins->bind_param("ii", $user_id, $image_id);
        $stmt_ins->execute();
        $is_liked = true;
    }
    // Get total likes
    $stmt_cnt = $conn->prepare("SELECT COUNT(*) as cnt FROM likes WHERE image_id = ?");
    $stmt_cnt->bind_param("i", $image_id);
    $stmt_cnt->execute();
    $likes = $stmt_cnt->get_result()->fetch_assoc()['cnt'];
    
    echo json_encode(['status' => 'success', 'is_liked' => $is_liked, 'likes' => $likes]);
    exit();
}

if ($action === 'save') {
    $stmt = $conn->prepare("SELECT * FROM saved WHERE user_id = ? AND image_id = ?");
    $stmt->bind_param("ii", $user_id, $image_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        // Unsave
        $stmt_del = $conn->prepare("DELETE FROM saved WHERE user_id = ? AND image_id = ?");
        $stmt_del->bind_param("ii", $user_id, $image_id);
        $stmt_del->execute();
        $is_saved = false;
    } else {
        // Save
        $stmt_ins = $conn->prepare("INSERT INTO saved (user_id, image_id) VALUES (?, ?)");
        $stmt_ins->bind_param("ii", $user_id, $image_id);
        $stmt_ins->execute();
        $is_saved = true;
    }
    echo json_encode(['status' => 'success', 'is_saved' => $is_saved]);
    exit();
}

if ($action === 'comment') {
    $text = trim($_POST['text'] ?? '');
    if (!empty($text)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, image_id, comment_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $image_id, $text);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
            exit();
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Empty comment']);
    exit();
}

if ($action === 'get_info') {
    // Get like count, comment count, and user states
    $stmt_cnt = $conn->prepare("SELECT COUNT(*) as cnt FROM likes WHERE image_id = ?");
    $stmt_cnt->bind_param("i", $image_id);
    $stmt_cnt->execute();
    $likes = $stmt_cnt->get_result()->fetch_assoc()['cnt'];

    $stmt_like = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND image_id = ?");
    $stmt_like->bind_param("ii", $user_id, $image_id);
    $stmt_like->execute();
    $is_liked = $stmt_like->get_result()->num_rows > 0;

    $stmt_save = $conn->prepare("SELECT * FROM saved WHERE user_id = ? AND image_id = ?");
    $stmt_save->bind_param("ii", $user_id, $image_id);
    $stmt_save->execute();
    $is_saved = $stmt_save->get_result()->num_rows > 0;
    
    // Get Comments
    $comments = [];
    $stmt_com = $conn->prepare("SELECT c.comment_text, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.image_id = ? ORDER BY c.created_at ASC");
    $stmt_com->bind_param("i", $image_id);
    $stmt_com->execute();
    $res_com = $stmt_com->get_result();
    while ($r = $res_com->fetch_assoc()) {
        $r['comment_text'] = htmlspecialchars($r['comment_text']);
        $r['username'] = htmlspecialchars($r['username']);
        $comments[] = $r;
    }

    echo json_encode(['status' => 'success', 'likes' => $likes, 'is_liked' => $is_liked, 'is_saved' => $is_saved, 'comments' => $comments]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
