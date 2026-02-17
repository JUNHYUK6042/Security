<?php
session_start();
include "dbconn.php";

$title = $_POST['title'];
$filename = $_FILES['file']['name'];
$tmp = $_FILES['file']['tmp_name'];

move_uploaded_file($tmp, "../public/upload/".$filename);

$writer = $_SESSION['user'];

$sql = "INSERT INTO board (title, filename, writer) 
        VALUES ('$title', '$filename', '$writer')";
mysqli_query($conn, $sql);

header("Location: ../public/board.php");
?>