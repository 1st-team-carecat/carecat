<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./CSS/todolist.css">
</head>
<body class="start-page">
    <div class="line">
        <div class="start-title">
            <img src="./img/main-title.png" class="main-title-img">
        </div>
        <div class="start-button">
            <a href="./join.html">START</a>
        </div>
    </div>
</body>
</html>