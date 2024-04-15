<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    // GET으로 넘겨 받은 year값이 있다면 넘겨 받은걸 year변수에 적용하고 없다면 현재 년도
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
    // GET으로 넘겨 받은 month값이 있다면 넘겨 받은걸 month변수에 적용하고 없다면 현재 월
    $month = isset($_GET['month']) ? $_GET['month'] : date('m');
    $date = "$year-$month-01"; // 현재 날짜
    $time = strtotime($date); // 현재 날짜의 타임스탬프
    $start_week = date('w', $time); // 1. 시작 요일
    $total_day = date('t', $time); // 2. 현재 달의 총 날짜
    $total_week = ceil(($total_day + $start_week) / 7);  // 3. 현재 달의 총 주차
    // 할일 목록을 가져오기 위해 DB 연결
    $conn = my_db_conn(); // connection 함수 호출
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit;
}

try {
    // 전송된 데이터 수집
    // 변수가 설정되었는지 확인후 설정되어 있지 않으면 빈 문자열 할당
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $profile = isset($_GET['profile']) ? $_GET['profile'] : '';

    // 가져온 데이터를 각각의 함수에 저장
    $array_param = array(
        'name' => $name, 'profile' => $profile
    );

    // 데이터 베이스에서 정보 조회
    $result = db_select_information($conn, $array_param);


    if (!empty($result)) {
        // 조회된 결과의 해당하는 값 가져옴
        $name = $result[0]['name'];
        $profile = $result[0]['profile'];
    } else {
        // 가져온 데이터가 없을 경우 빈 문자열
        $name = "1";
    }

} catch (\Throwable $t) {
    echo $t->getMessage();
    exit;
}

?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>케어해달라냥 캘린더</title>
    <link rel="stylesheet" href="./css/todolist.css">
    <link rel="icon" href="/img/favicon.png">
</head>

<body>
    <header>
        <div class="main-title">
            <img src="/img/content-title.png" class="title-img">
        </div>
        <div class="header-profile-name"><?php echo $name ?></div>
        <a href="./todolist_mypage.php">
            <img class="header-profile-img" src="<?php echo $profile ?>" />
        </a>
    </header>
    <main class="main-box">
        <div class="box">
            <div class="menu-content">
                <div class="menu">
                    <a href="./todolist_mypage.php">내정보</a>
                </div>
                <div class="menu">
                    <a href="./todolist_list.php">할일</a>
                </div>
                <div class="menu page-point">
                    <a href="#">캘린더</a>
                </div>
            </div>
            <div class="calendar-dox">
                <div class="calendar">
                    <div class="todo-get-calendar">
                        <div class="nav">
                            <!-- 년 월 구하기 -->
                            <!-- 이전 달로 이동하는 링크 -->
                            <!-- $month가 1이라면(1월인 경우), 이전 해 (년도 1감소)의 12월을 가르키는 링크 -->
                            <?php if ($month === 1) { ?>
                                <a href="/todolist_calendar.php?year=<?php echo $year - 1 ?>&month=12">
                                <?php } else { ?>
                                    <!-- 그렇지 않은 경우 이전 월(월을 1감소)을 가르키는 링크 -->
                                    <a href="/todolist_calendar.php?year=<?php echo $year ?>&month=<?php echo $month - 1 ?>">
                                    <?php } ?>
                                    <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAxklEQVR4nO2VTQrCMBCFcwh/NnoZF66ajF3UwygYZkKPouIMBS+ip1DQegglhUIrLie40AezfV94eZMY85e2bOAZkFwB+QxhP1U1L0oeOuI7kDzjOOK1KsAhH1rzOIsgczVziwJd8whTM8/8dgAotw7gYcvdWA0AJNw/vRTJogHiY7rWoNS5r0ZqAEfse60hWaqZNwDkTVJAExFKnSyiqKSX3Oq9pupRZakX7XtRBcXH7lOrAGVlknw4KBcgPuW+mqgDzM/pBWGTysH2H670AAAAAElFTkSuQmCC">
                                    </a>

                                    <!-- 연도와 월 출력 -->
                                    <p><?php echo  "$year 년 $month 월" ?> </p>

                                    <!-- 다음 달로 이동하는 링크 -->
                                    <!-- $month 가 12라면(12월인 경우), 다음 해 (년도 1증가)의 1월을 가르키는 링크 -->
                                    <?php if ($month === 12) { ?>
                                        <a href="/todolist_calendar.php?year=<?php echo $year + 1 ?>&month=1">
                                        <?php } else { ?>
                                            <!-- 그렇지 않은 경우 다음 월 (월을 1 증가) 가르키는 링크 -->
                                            <a href="/todolist_calendar.php?year=<?php echo $year ?>&month=<?php echo $month + 1 ?>">
                                            <?php } ?>
                                            <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAuUlEQVR4nO2UQQrCQAxF5xBWV3oYBVftpILjaZSSKT2KSn8QvIieQkH0EEpBoXWdLIo+yDYP/vyJc380SAtMiHGmKFdimTltPGNNUZ7NeJZ7qJCoCvJS5h/Be+C08Yy6LfEsQVWQFtsBsdxakkdW7UaqkoyFulHh6LShKDCNKlRImiaZtsqzhK9WbfojCNYRkeUj51FW3eWoTT/aojgM7aKJWPbn2Hnrc03lfkwRJ2K5ZCWmqsvdz/MCS6HK05bgwhsAAAAASUVORK5CYII=">
                                            </a>
                        </div>

                        <!-- 요일 표시 -->
                        <ul class="weeks">
                            <li>Sun</li>
                            <li>Mon</li>
                            <li>Tue</li>
                            <li>Wed</li>
                            <li>Thu</li>
                            <li>Fri</li>
                            <li>Sat</li>
                        </ul>
                        <!-- 달력 날짜 표시 -->
                        <ul class="days">
                            <?php
                            // 주어진 월에 포함된 총 주 수($total_week)만큰 반복
                            // $n: 날짜를 나타내는 변수로, 각 주의 첫 번째 날부터 시작하여 증가
                            // $i: 외부 루프의 반복 횟수를 추적하는 변수
                            for ($n = 1, $i = 0; $i < $total_week; $i++) {
                                // 한 주에 해당하는 7일 반복
                                for ($k = 0; $k < 7; $k++) {
                                    // 현재 반복 중인 날짜 생성
                                    $chk_day = $year . '-' . $month . '-' . $n;
                                    // $chk_day 날자에 해당하는 할 일 목록을 데이터 베이스에서 가져옴
                                    $todo_list = db_select_todos_list_with_date($conn, $chk_day); // 수정된 함수 사용
                            ?>
                                    <li>
                                        <!-- 날짜 입력 -->
                                        <input type="hidden" name="chk_day" value="<?php echo $chk_day; ?>">
                                        <!-- 날짜 표시 -->
                                        <?php
                                        // $total_day보다 $n이 작고, $k가 $start_week보다 크거나 같은 경우에만 날짜 출력
                                        // 첫째주와 마지막주의 빈칸을 채우기 위함
                                        if (($n > 1 || $k >= $start_week) && ($total_day >= $n)) {
                                            echo $n;
                                            // 해당 날짜에 할 일 목록이 있고, 그 중에서도 todo_date에 내용이 있으면 이미지로 출력
                                            if (!empty($todo_list)) {
                                                echo '<br><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAACbUlEQVR4nKVWTWsUQRBdjd/oQZCgEDwEjaIeEvxCb+rBiwqi3rypoOBF9CQ5KYLgH/CQs4ZV8QPMQchU74dxu6ojootCgkTBg0Q0qFGQuD7pnsnuZLdndowLfZip6ffqvaqu3lyuzQ/Agv+JJ28c5k1QXAJxDUqqUM821mPV6hIouQHFUyD5CpJryOc7soMPjS2F4ndQgsbicRiz2MWJb86N2WUuZicoyKlWALEkR0Jl8rslRvIZRIuyERAPJBBcBslZf0xgybMRKL6TQtCfSBCrUzuCK14A0mdA5rQ/xjVbu4wWmc0g/uNRsBPDemuCunI737dB8WEYsyJ8lsEmgDe2590ieetRcNLtK71c7XCKuiee8bl6xsSvUTLrUS6vguIH4XseB+kdje/leJPCuwAWItAHoPhT9G7GPlu/+1rsIH5cBxvy+wpl9rlaWTIL7qzj7004xRyU3PYXzazJVLBoXNgaeOoybQkmvATB6HYfkJcgMAcTCj9l/fzmDRbMuoYd3BdlOAOSMSh9tKlBPKMjstovTSZms0WB97Z4a0cF6UOxoj9MSHK/LdYJz1zpDzeObgDxZEJ2kyC9NlJ41WPPrbjE67GNAUZGloMqXa49E8eC+/aJ6yCqrgSxjiU4OHueGiRF3YOC7AoPku6Fkg+p4A2wR+7c2H2B2Y0Sd6e3XNjf05nAVV3JLzeBn77oTAfP5ztA/P6fwNWc9Sr1CnVS5w+OUE2lK5mAaBlIfs4fnL+0vZtBcj666OMbayB5DpJ7UHwfSgTEP5oIPsbPRhsS3QuSCyC+hICP+eaSa88Sd0PpPShWtth/Gj6wv9yB9UMKw8w6AAAAAElFTkSuQmCC">';
                                            }
                                            // 다음 날짜로 이동
                                            $n++;
                                        }
                                        ?>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>