<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    $conn = my_db_conn();

    // 함수 호출하기
    $result = db_count_checked($conn);

    if (isset($result['chk_ttl']) && isset($result['chk_cnt'])) {
        // 데이터 수 가져와서 변수에 담기
        $checked_total = $result['chk_ttl']; // 전체 데이터
        $checked_count = $result['chk_cnt']; // 체크된 데이터

        // 백분율로 계산
        $percentage = ($checked_total > 0) ? ($checked_count / $checked_total) * 100 : 0;

        // 백분율 정수로 반환
        $percentage = intval($percentage);

    } else {
        // 결과 없을 경우 0 반환
        echo 0;
    }

} catch (\Throwable $e) {
    echo $e->getMessage();
    // exit;

} finally {
    // 연결 종료
    if(!empty($conn)) {
        $conn = null;
    }
}


// 내 정보 수정하기

try {

    $conn = my_db_conn();
    
    // 폼에서 전송한 데이터 가져오기
    $name = $_GET['name'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $birth_at = $_GET['birth_at'] ?? '';
    $weight = $_GET['weight'] ?? '';
    $cat_no = $_GET['cat_no'] ?? '';
    
    // 함수 호출하기
    $result = db_update_information($conn, $array_param);

    // 수정할 정보 배열 생성하기
    $array_param = array(
        'name' => $name
        ,'gender' => $gender
        ,'birth_at' => $birth_at
        ,'weight' => $weight
        ,'cat_no' => $cat_no
    );
    
    $item = array(
        'name' => $name
        ,'gender' => $gender
        ,'birth_at' => $birth_at
        ,'weight' => $weight
    );
    


} catch(\Throwable $e) {
    echo $e->getMessage();
    exit;


} finally {
    if(!empty($conn)) {
        $conn = null;
    }
}






$name = "로미";

?>



<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>케어해달라냥 내 정보 페이지</title>
    <link rel="stylesheet" href="./CSS/mypage.css">

</head>
<body>
    <header>
        <div class="main-title">
          <img src="./css/content-title.png" class="title-img">
        </div>
        <div class="header-profile-name">로미</div>
        <a href="./mypage.php">
          <img class="header-profile-img"
                src="./css/11zon_cropped__1_-removebg-preview.png"/>
        </a>
      </header>
    <main class="main-box">
      <div class="box">
        <div class="menu-content">
            <div class="menu selected-menu">
                <a href="./todolist_mypage.php">내정보</a>
            </div>
            <div class="menu">
                <a href="./todolist_list.php">할일</a>
            </div>
            <div class="menu">
                <a href="./todolist_calendar.php">캘린더</a>
            </div>
        </div>
        <div class="content-info">
            <div class="info-title">집사 반가워!</div>
            <div>
                <div class="info-box">
                    <div class="info-left">
                        <img class="info-pic" src="./css/11zon_cropped__1_-removebg-preview.png" alt="">
                        <a href="./todolist_info_update.php" class="info-edit">내 정보 수정</a> 
                    </div>
                    <div class="info-right">
                        <span class="info-text1"><?php echo $name ?></span>
                        <span class="info-text2"><?php echo $gender ?></span>
                        <span class="info-text1"><?php echo $birth_at ?></span>
                        <span class="info-text2"><?php echo $weight ?></span>
                        <span class="dday"><?php echo $name."의 생일이 100일 남았습니다!"?></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="gauge">
            <div class="gauge-back">
                <span class="gauge-percent"><?php echo $percentage."%"; ?></span>
                <!-- 퍼센트를 텍스트로 출력 -->
                <div class="gauge-bar" style="width: <?php echo $percentage; ?>%;"></div>
                <!-- 퍼센트를 게이지 바의 넓이로 출력 -->
                <span class="gauge-text">행복달성지수</span>
            </div>
        </div>
    </div>
</main>
</body>
</html>