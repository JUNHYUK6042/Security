<?php
$host = "localhost";
$user = "webuser";
$password = "1234";
$dbname = "websec";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}
?>
