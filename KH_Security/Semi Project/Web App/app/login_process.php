<?php
session_start();
include "dbconn.php";

$id = $_POST['id'];
$pw = $_POST['pw'];

$sql = "SELECT * FROM users WHERE id='$id' AND pw='$pw'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
    $_SESSION['user'] = $id;
    header("Location: /public/board.php");
} else {
    echo "<script>alert('아이디 또는 패스워드가 일치하지 않습니다.'); window.history.back();</script>";
}
?>