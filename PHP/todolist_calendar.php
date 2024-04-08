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

?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./css/todolist.css">
</head>

<body>
  <header>
    <div class="main-title">
      <img src="./css/content-title.png" class="title-img">
    </div>
    <div class="header-profile-name">로미</div>
    <a href="./join.html">
      <img class="header-profile-img" src="./css/11zon_cropped__2_-removebg-preview.png" />
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
          <form action="/todolist_list.php" method="get">
            <input type="date" name="list_start_date" style="display: none;">
            <div class="todo-get-calendar">
              <div class="nav">
                <!-- 년 월 구하기 -->
                <!-- 이전 달로 이동하는 링크 -->
                <?php if ($month == 1) { ?>
                    <a href="/todolist_calendar.php?year=<?php echo $year - 1 ?>&month=12">
                <?php } else { ?>
                    <a href="/todolist_calendar.php?year=<?php echo $year ?>&month=<?php echo $month - 1 ?>">
                <?php } ?>
                        <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAxklEQVR4nO2VTQrCMBCFcwh/NnoZF66ajF3UwygYZkKPouIMBS+ip1DQegglhUIrLie40AezfV94eZMY85e2bOAZkFwB+QxhP1U1L0oeOuI7kDzjOOK1KsAhH1rzOIsgczVziwJd8whTM8/8dgAotw7gYcvdWA0AJNw/vRTJogHiY7rWoNS5r0ZqAEfse60hWaqZNwDkTVJAExFKnSyiqKSX3Oq9pupRZakX7XtRBcXH7lOrAGVlknw4KBcgPuW+mqgDzM/pBWGTysH2H670AAAAAElFTkSuQmCC">
                    </a>
                <p><?php echo  "$year 년 $month 월" ?> </p>
                <!-- 다음 달로 이동하는 링크 -->
                <?php if ($month == 12) { ?>
                    <a href="/todolist_calendar.php?year=<?php echo $year + 1 ?>&month=1">
                <?php } else { ?>
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
                for ($n = 1, $i = 0; $i < $total_week; $i++) {
                    for ($k = 0; $k < 7; $k++) {
                        $chk_day = $year . '-' . $month . '-' . $n;
                        $todo_list = db_select_todos_list_with_date($conn, $chk_day); // 수정된 함수 사용
                        ?>
                        <li>
                            <!-- 날짜 입력 -->
                            <input type="hidden" name="chk_day" value="<?php echo $chk_day; ?>">
                            <!-- 날짜 표시 -->
                            <?php if (($n > 1 || $k >= $start_week) && ($total_day >= $n)) {
                                echo $n;
                                // 할일 목록이 있으면 숫자 1 출력
                                if (!empty($todo_list)) {
                                    echo '<br><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABh0lEQVR4nO2UvUoDQRDH7wm0U7DVdwiIEnuxURvjY4hkJykEC61VEHYmnaC+hCTezCUieQYxNgY/Egu10ELZi3dZY87cJm0GFo7Znf9vPo7xvLGNYnniJUC5B+TqzsnVROzX9UlFUlPITdB+dmgAkNSB5MscRXwY+1GOIj8QX48A4PeukDQiv0K5i/0or0MDbCFF/DYI7F4B9m+F3TpAPnDJeBOIXwD5cus4mDKDVcRBZ9CyGAO0nzU+c2feANWmFbEfxhLnkjMmaViln6avlM/jViLf/lMBP1ulP/0WCdZ/Emjktaz1JNZKikuVSV7zgiL5tObxUdTBfL/KFfFZIsD0XZFUFEpbob9hgatW6yKhoAvgXBhDUjEanosViFd6xaNTKAXL3iimqDoXroMEgPmTilpmhxI3gwSSx0TxeFbyAMSrbpmj7A8Shr/V7LkA2s4AklZqAKCU3QF8kRqwXZIZsy4c2lM2MakBcau0nwGUXbNnFMmN2aidE3774Z32M87CY/Ms+wb0eBNXho5PPQAAAABJRU5ErkJggg==">';
                                }
                                $n++;
                            } ?>
                        </li>
                        <?php
                    }
                }
                ?>
              </ul>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</body>

</html>
