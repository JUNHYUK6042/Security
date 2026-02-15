<?php
session_start();
include __DIR__ . "/dbconn.php";

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Login required.");
}

$title = isset($_POST['title']) ? $_POST['title'] : "";
$writer = $_SESSION['user'];

$filename_to_save = "";
$original_name = "";

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $original_name = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    // 파일명 정리 (경로 제거)
    $base = basename($original_name);

    // 확장자(마지막) 추출
    $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));

    // 1) 화이트리스트
    $allowed = array("jpg", "jpeg", "png", "gif", "txt");

    if (!in_array($ext, $allowed, true)) {
        http_response_code(400);
        echo "<script>alert('허용되지 않는 확장자입니다.'); window.history.back();</script>";
        exit;
    }


    // 2) 저장 파일명은 랜덤으로 (원본명 그대로 쓰면 보안/충돌/경로문제 생김)
    $safe_name = bin2hex(random_bytes(16)) . "." . $ext;
    $dest = __DIR__ . "/../public/upload/" . $safe_name;

    if (!move_uploaded_file($tmp, $dest)) {
        http_response_code(500);
        exit("Upload failed.");
    }

    $filename_to_save = $safe_name;
}

// DB 저장
$title_esc = mysqli_real_escape_string($conn, $title);
$writer_esc = mysqli_real_escape_string($conn, $writer);
$file_esc = mysqli_real_escape_string($conn, $filename_to_save);

$sql = "INSERT INTO board (title, filename, writer) VALUES ('$title_esc', '$file_esc', '$writer_esc')";
mysqli_query($conn, $sql);

header("Location: ../public/board.php");
exit;