<?php
session_start();
include "../app/dbconn.php";

$result = mysqli_query($conn, "SELECT * FROM board ORDER BY idx DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>게시판</title>
</head>
<body>

<div class="board-container">
    <h2>게시판</h2>

    <div class="board-actions">
        <a href="write.php" class="main-btn">게시글 작성</a>
    </div>

    <table class="board-table">
        <tr>
            <th>제목</th>
            <th>파일</th>
            <th>작성자</th>
            <th>작성날짜</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td>
                <?php if($row['filename']) { ?>
                    <a href="upload/<?php echo $row['filename']; ?>" class="file-link">
                        <?php echo $row['filename']; ?>
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $row['writer']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>

    </table>
</div>

</body>
</html>