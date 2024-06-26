<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리



try {
    $conn = my_db_conn();

    // 프로그래스 바 출력

    // 함수 호출하기
    $result = db_count_checked($conn);

    if (isset($result['chk_ttl']) && isset($result['chk_cnt'])) {
        // 데이터 수 가져와서 변수에 담기
        $checked_total = $result['chk_ttl']; // 전체 데이터
        $checked_count = $result['chk_cnt']; // 체크된 데이터

        // 정수로 반환
        $checked_count = floor($checked_count);
        $checked_total = floor($checked_total);

        // 백분율로 계산
        $percentage = ($checked_total > 0) ? ($checked_count / $checked_total) * 100 : 0;

        $percentage = floor($percentage);

    } else {
        // 결과 없을 경우 0 반환
        echo 0;
    }

    // 내 정보 출력

    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $profile = isset($_GET['profile']) ? $_GET['profile'] : '';
    $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
    $birth_at = isset($_GET['birth_at']) ? $_GET['birth_at'] : '';
    $weight = isset($_GET['weight']) ? $_GET['weight'] : '';
    $adopt_at = isset($_GET['adopt_at']) ? $_GET['adopt_at'] : '';

    $array_param = array(
        'name' => $name
        ,'profile' => $profile
        ,'gender' => $gender
        ,'birth_at' => $birth_at
        ,'weight' => $weight
        ,'adopt_at' => $adopt_at
    );

    // 데이터 가져오기
    $result = db_select_information($conn);

    // 가져온 데이터가 있으면 변수에 할당
    if (!empty($result)) {
        $name = $result[0]['name'];
        $profile = $result[0]['profile'];
        $gender = $result[0]['gender'];
        $birth_at = $result[0]['birth_at'];
        $weight = $result[0]['weight'];
        $adopt_at = $result[0]['adopt_at'];
    } else {
        // 가져온 데이터가 없을 경우 빈 문자열
        $name = "";
        $gender = "";
        $birth_at = "";
        $weight = "";
        $adopt_at = '';
    }

    // 생일 디데이 가져오기
    // 현재 날짜 
    // $now = date("Y-m-d");
    $now = new DateTime();

    // 생일의 연도를 현재 연도로 설정
    // $next_birth_at = date('Y') . '-' . date('m-d', strtotime($birth_at));
    $next_birth_at = new DateTime(date('Y') . '-' . date('m-d', strtotime($birth_at)));

    // 생일이 오늘보다 이전인 경우 연도를 다음해로 설정
    // if ($next_birth_at < $now) {
    //     $next_birth_at = date('Y', strtotime('+1 year')) . '-' . date('m-d', strtotime($birth_at));
    // }
    if ($next_birth_at < $now) {
        // $next_birth_at->modify('+1 year') . '-' . date('m-d', strtotime($birth_at));

        $next_birth_at->modify('+1 year');
        $next_birth_at->format("Y") . '-' . date('m-d', strtotime($birth_at));
    }

    // 남은 일 수 계산
    // $difference = strtotime($next_birth_at - strtotime($now);
    // $birth_dday = floor($difference / (60 * 60 * 24));

    $difference = $next_birth_at->diff($now);
    $birth_dday = $difference->days;
    
    // 유닉스 타임스탬프로 변환
    // $adopt_timestamp = strtotime($adopt_at);
    // $now_timestamp = strtotime($now->format('Y-m-d'));
    $adopt_timestamp = new DateTime($adopt_at);


    // 두 날짜 차이 계산
    // $adopt_dday = ($now_timestamp - $adopt_timestamp) / (60 * 60 * 24);
    $adopt_dday = $adopt_timestamp->diff($now)->days;


} catch (\Throwable $e) {
    echo $e->getMessage();
    // exit;

} finally {
    // 연결 종료
    if (!empty($conn)) {
        $conn = null;
    }
}

// 출력할 데이터 가공하기

// 성별 데이터 출력
if ($gender === '0') {
    $gender_echo = "수컷";
} else {
    $gender_echo = "암컷";
}

// 생년월일 데이터 출력
$birth_at_echo = date("y년 m월 d일", strtotime($birth_at));
?>



<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>케어해달라냥 내 정보 페이지</title>
    <link rel="stylesheet" href="./css/mypage.css">
    <link rel="icon" href="/img/favicon.png">
</head>

<body>
    <header>
        <div class="main-title">
            <img src="/img/content-title.png" class="title-img">
        </div>
        <div class="header-profile-name"><?php echo $name ?></div>
        <a href="#">
            <img class="header-profile-img" src="<?php echo $profile ?>" />
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
                            <img class="info-pic" src="<?php echo $profile ?>" alt="">
                            <span class="info-edit"><?php echo $name ?></span>
                        </div>
                        <div class="info-right">
                            <span class="info-con1 info-other"><?php echo $gender_echo ?></span>
                            <span class="info-con2 info-other"><?php echo $birth_at_echo ?></span>
                            <span class="info-con1 info-other">몸무게 : <?php echo $weight ?> kg</span>
                            <span class="info-con2 dday"><?php echo $name ?>의 생일까지
                                <span class="bold"><?php echo $birth_dday ?>일</span>!
                            </span>
                            <span class="info-con1 dday"><?php echo $name ?>와 만난지
                                <span class="bold"><?php echo $adopt_dday ?>일</span> 째
                            </span>
                        </div>
                    </div>
                </div>
                <div class="gauge">
                    <div class="gauge-back">
                        <div class="gauge-bar" style="width: <?php echo $percentage; ?>%;"></div>
                        <!-- 퍼센트를 게이지 바의 넓이로 출력 -->
                        <span class="gauge-text">행복달성지수</span>
                        <span class="gauge-percent"><?php echo $percentage . "%"; ?></span>
                        <!-- 퍼센트를 텍스트로 출력 -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>