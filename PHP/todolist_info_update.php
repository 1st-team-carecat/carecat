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





    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // 폼에서 전송한 데이터 가져오기
        $name = $_POST['name'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $birth_at = $_POST['birth_at'] ?? '';
        $weight = $_POST['weight'] ?? '';
        $cat_no = $_POST['cat_no'] ?? '';
        
        // 수정할 정보 배열 생성하기
        $info_array = array(
            'name' => $name
            ,'gender' => $gender
            ,'birth_at' => $birth_at
            ,'weight' => $weight
            ,'cat_no' => $cat_no
        );

        // 함수 호출
        $result = db_update_information($conn, $info_array);
        
        $item = array(
            'name' => $name
            ,'gender' => $gender
            ,'birth_at' => $birth_at
            ,'weight' => $weight
        );
    
    } else {
        // POST 요청이 아닌 경우 변수 초기화
        $name = '';
        $gender = '';
        $birth_at = '';
        $weight = '';
        $cat_no = '';
    }

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
                <a href="./mypage.php">내정보</a>
            </div>
            <div class="menu">
                <a href="./todolist_list.php">할일</a>
            </div>
            <div class="menu">
                <a href="./todolist_cal.php">캘린더</a>
            </div>
        </div>
        <div class="content-info">
            <div class="info-title">집사 반가워!</div>
            <div>
                <form action="./todolist_info_update.php" method="post" class="info-box">
                    <div class="info-left">
                        <img class="info-pic" src="./css/11zon_cropped__1_-removebg-preview.png" alt="">
                        <div>
                        <button type="submit" class="info-edit up-btn">수정</button>
                        <button class="info-edit up-btn cancel"><a href="./todolist_mypage.php" >취소</a></button>
                        </div> 
                    </div>
                    <div class="info-right">
                        <input type="text" name="name" id="" class="info-text1" value="<?php echo $name ?>">
                        <input type="text" name="gender" id="" class="info-text2" value="<?php echo $gender ?>">
                        <input type="text" name="birth_at" id="" class="info-text1" value="<?php echo $birth_at ?>">
                        <input type="text" name="weight" id="" class="info-text2" value="<?php echo $weight ?>">
                        <input type="hidden" name="cat_no" id="" class="info-text2" value="<?php echo $cat_no ?>">
                        <span class="dday dday-up"><?php echo $name."의 생일이 100일 남았습니다!"?></span>
                    </div>
                </form>
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
    </div>
</main>
</body>
</html>