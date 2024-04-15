<?php
// require_once 를 쓴 이유는 한번만 실행을 할꺼고 불러오는 php에 오류가 났을때 처리는 중단하기 위해서
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // todolist_config.php 파일을 현재 PHP 스크립트에 포함시킴 이 파일에는 설정 정보가 들어 있
require_once(FILE_LIB_DB); // DB관련 라이브러리


// HTTP 요청 메서드가 POST인지 확인하는 조건문. 만약 POST 요청이면 아래의 코드 블록이 실행됨
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    try { // 오류가 발생하면 catch 블록으로 이동합니다.
        $conn = my_db_conn(); // DB 연결
        // 할 일의 날짜를 가져옵니다. 만약 POST 요청에서 "todo_date" 매개변수가 존재하면 그 값을 사용하고, 없으면 현재 날짜를 사용
        $todo_date = isset($_POST["todo_date"]) ? trim($_POST["todo_date"]) : date("Y-m-d");
        // 할 일의 내용을 가져옵니다. 만약 POST 요청에서 "content" 매개변수가 존재하면 그 값을 사용하고, 없으면 빈 문자열을 사용
        $content = isset($_POST["content"]) ? trim($_POST["content"]) : "";
        
        // 만약 할 일의 내용이 비어 있다면, 오류 발생 메시지에 "content"를 추가
        $arr_err_param = [];
        if ($content === "") {
            $arr_err_param[] = "content";
        }
        // 오류가 발생한 매개변수가 존재하면 예외를 발생시킵니다. 오류 메시지는 "Parameter Error: [매개변수]" 형식으로 구성
        if (count($arr_err_param) > 0) {
            throw new Exception("Parameter Error: " . implode(". ", $arr_err_param));
        }
        // 데이터베이스 트랜잭션을 시작합니다. 이후의 작업은 모두 트랜잭션 내에서 처리됩니다.
        $conn->beginTransaction();

        // 할 일의 내용과 날짜를 포함하는 매개변수 배열을 생성
        $arr_param = [
            "content" => $content,
            "todo_date" => $todo_date
        ];
        // db_insert_list() 함수를 사용하여 데이터베이스에 할 일을 추가
        $result = db_insert_list($conn, $arr_param);
        // 할 일 추가 작업이 성공적으로 이루어지지 않았을 경우, 예외를 발생
        if ($result !== 1) {
            throw new Exception("Insert Boards count");
        }
        // 데이터베이스 트랜잭션을 커밋하여 변경 사항을 영구적으로 반영
        $conn->commit();
        // HTTP 리다이렉트 헤더를 사용하여 사용자를 할 일 목록 페이지로 이동
        header("Location: todolist_list.php?selected_date=" . $todo_date);
        // PHP 스크립트를 종료합니다.
        exit;
        // 예외가 발생했을 때 처리할 코드 블록을 시작합니다. \Throwable은 모든 예외 클래스의 부모 클래스
    } catch (\Throwable $e) {
        // 데이터베이스 연결이 아직 열려 있고 트랜잭션이 진행 중인 경우, 트랜잭션을 롤백
        if (!empty($conn) && $conn->inTransaction()) {
            // 데이터베이스 연결이 아직 열려 있고 트랜잭션이 진행 중인 경우, 트랜잭션을 롤백
            $conn->rollBack();
        }
        // 발생한 예외의 메시지를 출력
        echo $e->getMessage();
        // PHP 스크립트를 종료
        exit;
        // try 블록에서의 작업이 완료된 후에 항상 실행되는 코드 블록을 시작
    } finally {
        // 데이터베이스 연결을 닫음
        if (!empty($conn)) {
            $conn = null;
        }
    }
    // HTTP 요청 메서드가 GET인지 확인하는 조건문입니다. 만약 GET 요청이면 아래의 코드 블록이 실행
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try { // 이번에도 오류가 발생할 수 있는 코드를 시도합니다. 만약 오류가 발생하면 catch 블록으로 이동
        $conn = my_db_conn(); // my_db_conn() 함수를 사용하여 데이터베이스 연결을 수행
        // 할 일 목록의 개수를 데이터베이스에서 가져옵니다. db_select_todos_cnt() 함수를 사용하여 처리
        $result_board_cnt = db_select_todos_cnt($conn); // 게시글수조회
        // GET 요청으로부터 'selected_date' 매개변수를 가져옵니다. 매개변수가 없으면 현재 날짜를 기본값으로 사용
        $selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');
        // 선택된 날짜를 년, 월, 일로 나누어 배열에 저장합니다.
        $arr_date = explode('-', $selected_date);

        // GET 요청으로부터 'year' 매개변수를 가져옵니다. 매개변수가 없으면 선택된 날짜에서 년도를 추출하고, 그것도 없으면 현재 년도를 기본값으로 사용
        $year = isset($_GET['year']) ? $_GET['year'] : (isset($arr_date[0]) ? $arr_date[0] : date('Y'));
        // GET 요청으로부터 'month' 매개변수를 가져옵니다. 매개변수가 없으면 선택된 날짜에서 월을 추출하고, 그것도 없으면 현재 월을 기본값으로 사용
        $month = isset($_GET['month']) ? $_GET['month'] : (isset($arr_date[1]) ? $arr_date[1] : date('m'));
        // 연도와 월을 조합하여 현재 날짜를 구성합니다. 날짜의 일 부분은 항상 01로 설정
        $date = "$year-$month-01"; // 현재 날짜
        // 문자열 형식의 날짜를 타임스탬프로 변환
        $time = strtotime($date); // 현재 날짜의 타임스탬프
        // 해당 월의 첫 날이 몇 번째 요일인지 확인
        $start_week = date('w', $time); // 1. 시작 요일
        // 해당 월의 총 날짜 수를 확인
        $total_day = date('t', $time); // 2. 현재 달의 총 날짜
        // 해당 월의 총 주 수를 계산
        $total_week = ceil(($total_day + $start_week) / 7);  // 3. 현재 달의 총 주차
        // 선택된 날짜를 매개변수 배열에 추가
        $arr_param['selected_date'] = $selected_date; // 선택한 날짜를 매개변수에 추가

        $result1 = db_select_todos_list1($conn, $arr_param); // 게시글 내용 조회
        $result2 = db_select_todos_list2($conn, $arr_param); // name, profile 조회


    } catch (\Throwable $e) {
        echo $e->getMessage();
        exit;
    } finally {
        if (!empty($conn)) {
            $conn = null;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>케어해달라냥 할일 페이지</title>
    <link rel="stylesheet" href="./css/list.css" />
    <link rel="icon" href="/img/favicon.png">
</head>

<body>
    <header>
        <div class="main-title">
            <img src="/img/content-title.png" class="title-img" />
        </div>
        <div class="header-profile-name"><?php echo $result1[0]["NAME"]; ?></div>
        <a href="./todolist_mypage.php">
            <img class="header-profile-img" src="<?php echo $result1[0]["PROFILE"] ?>" />
        </a>
    </header>


    <main class="main-box">
        <div class="box">
            <div class="menu-content">
                <div class="menu">
                    <a href="./todolist_mypage.php">내정보</a> <!-- 메뉴 링크 -->
                </div>
                <div class="menu">
                    <a href="#">할일</a> <!-- 메뉴 링크 -->
                </div>
                <div class="menu">
                    <a href="./todolist_calendar.php">캘린더</a> <!-- 메뉴 링크 -->
                </div>
            </div>
            <div class="content">
                <div class="content-list">
                    <form action="./todolist_list.php" method="POST">
                        <div class="list-box">
                            <label for="todo_date">
                                <input type="date" id="todo_date" name="todo_date" value="<?php echo date('Y-m-d'); ?>" />
                            </label> <!-- 날짜 입력 필드 -->
                            <label for="content" class="text-list">
                                <input type="text" id="content" name="content" placeholder="할일을 추가하세요!" />
                            </label> <!-- 할일 내용 입력 필드 -->
                            <button type="submit">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAACXBIWXMAAAsTAAALEwEAmpwYAAADEElEQVR4nO2aS08UQRDH++DjLolI0JuvT2MgJho8eTBg1MUDxK1eSdYPIMJeJFO90XD1bCCccLdqF/CBHjyQqJ9AIQEx8UEWUzNLNKtopul5ZeefdLKZ3enp31ZPdXVVK5UrV65cubpU448XT2hDVzRSRSMtaKR32vCGNvQ9aLzhXwu+qwDWh+QelSXB7EoPmMYoIL3QhnftGj3XyAXpS6VVd3D5JBieBkNf7EE7GvK2NjxVmmn2q7Ro2Ht5WHt0Gwx/dgba0YI/ke4VKnNHE4WdqPI5jfwmKtC/gL8GrJ9NBLbo8cUorbr/NKetItJgrLAl5Ktg+EfssL/e7R1Avh4LrDY0khjon+CFyKexRt5JHPQ3S+tqfSASWDDN04C0mThkR/P9iNc47xS2/OTtkTi9cXhL06osj86AtSFIHOp/lq7SuBPY0kyzvx3xuBkY0lPpsx2Zzbmc2iWv1ndgYDA87dISArrX991HtVNuLU2TDjYC5C42Nrzb+QynwMjbB9pwgGmMurVAxMA+NN20B8aDbPGSAQakFSvYklfr00itrAEHY17qDQ2sJVPhejBxAAce+3J4YKRKVoElaWADvJBVYFnfLYD5Q1aBJTEYGhiQ1m0iKOVYVhEZ0icb4G+2EZRrWURkX0M/BLoQeD2so4gC2odFng8J/DE8sKH3UTiUzuekxmnp7luWqLsCD8D6UGaBkS6Ft7BZ6u2qzYPIr+JlDBgMLylbaeRC5oC9+g1rYJhd6XGZwEt9ikckHi8rSTxAvq9Sl6Y1QURmGUH9w7q05ey4BCAVnU69SBqNKadVfqTV5KH2nTWvnJZa0l5Mk5MIKgoVkQbTVi4Fjy+oKAWGhtMBS62i4WuqW448aEMjKk7pan1AloLY31mkzcin8X6amHl2Jk7vLd5YnKdKUuXy4iE5mBaltfcOpslJBJUWlbxaHyA9cBp7+33RZKoPnEJQU76lDS9b7aeRWrLFk7Jn2TSPqSxp7GHjuBS2/A0I8jwgrUk2VFLA7Saf19ox9ZT8Vu5Jety5cuXKlUslpJ/fX9oCxt+DDAAAAABJRU5ErkJggg==" />
                            </button> <!-- 제출 버튼 -->
                        </div>
                    </form>
                    <div class="scroll">
                        <?php
                        // 게시글 출력
                        $cnt = 1;
                        foreach ($result2 as $item) {
                            $cnt++
                        ?>
                            <form method="post">
                                <div class="chk-list">
                                    <!-- list_no 담을 인풋 히든처리 -->
                                    <input type="hidden" value="<?php echo $item["list_no"] ?>" name="list_no">
                                    <input type="hidden" value="<?php echo $item["todo_date"] ?>" name="todo_date">
                                    <button type="submit" formaction="./todolist_com.php" id="check<?php echo $item["list_no"]; ?>"></button>
                                    <label for="check<?php echo $item["list_no"]; ?>" class="<?php echo $item["checked"] === "1" ? "checked-com" : "" ?>"></label>

                                    <input type="text" name="content" value="<?php echo $item["content"]; ?>" />
                                    <!-- 수정 버튼 -->
                                    <button type="submit" formaction="./todolist_list_update.php">
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAACXBIWXMAAAsTAAALEwEAmpwYAAABa0lEQVR4nO3XvUoDQRDA8VX8KAQ730QQfRcLC9NZ2cxoYEF7wTI7Z2M6BUtRgwQzk2BhaamFL6Hi18ndJYZILmfhcbOwf9j+x3CzxxoTCoVKaevwfN6e3M8ZH7LN20V00kaSGJ18IEkLGp0VoxvMvRQ8cvgNos668QcsQ7i2iQPJfj44O+Dkymiq1ribRZKziWiSd3XLWSuCO/5Shy6EO2mbqto+vlwA4oudSJb/DHfcS5a1MjA6vuljnvCos1QIrxI8/lrja2vbM7lwJ3taJhyPXmV8YLQ1CYw/11l3w/gExuzH8ZK3mCrBODy5i6kVHGu61gL43wsTLrsw4bILEy477yachMQAxJ/egAcBcc0rcFocT6GTR3/A/ZJ3nlfgJHBiEzgQP3gBHvOpbKbLqQmMjut5D9BBQIxqwLuN7lr2juPTIriawHFz+AD1AG5tPI3Ez78WrW7ULxlJC0lek4lDJKtVk0KhkNHXN22uzocDxBx4AAAAAElFTkSuQmCC" /></button>
                                    <!-- 삭제 버튼 -->
                                    <button type="submit" formaction="./todolist_list_delete.php">
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC1UlEQVR4nO2ZO2wTQRCGFwJCEAkEJA00UEGgBUTDo6cEt0CFCIiCRNi7x+MoaVAIr+hmHKEECmJRICIhpSAWnrETJBc8QkVDAyL0kSISxXB2nJh4D4ll1wfoPmka+/zP/rdzvt9nIRISEhIS/gcy2eJhifRAQfGE+CepVFZJJKWA5xVypVb0KBOUNzUe5vv5NV62eOxKlneJvw3/4eRGhfRk2cBySaCPCvhQfbcU0tvq68hzCvnGmaC8NpZF9w6Ptcv7tLleXpb3KaAPOhNLZpDnJNC4AlrQvE/h7jRq9g6PtTsz4GHpgAR+E7EYuwW0oIBfy6C436oJPyhvkMifnRvAJkOfLt4srbdmxAM62HITWKuwtzUjl+HlzpaMFDaPWGZgYoewiUS+FcOO9AkXKOTRlc0k0owEyvxRIc1oTIw6MbFo5F7z9vO871dWm2qGn128p6w4QXxXuEIhXdeNgBx6tdVUsyfId2g1gX3hCgV0XjvLQXG3qWYaJ7q0RoLCOeEKmaWU/tulFkFMqMUWnWbRXejMIB3VNc0EfNxUM1ywfpcLR4Qr0kB7I+b5rKmmQu7WGhks7LG7+samg4VOrRGkq8aayNd0mj1BvkO4IpXLtf38e2PJyG1TTQV8R/eVnsrl2oRLJPBXTZR4bKyHPNK8IzRtd9W6xsDvNY1fmOvRuGa0poRrFHC++WKnd8Z6yFMavXG7q9Y1Bs5pGn8x1kOa1sSTEburdpy3YslZLvJWLDmrTpiBbOWtqJylkLuFa6LyVpiZflcrjCEtz1ku8lYsOatOmIFs5a2onJXGiS7hmui8xUM+lrY0Pmj7VVWPRR6KCIydzo1U8xbybMRFaqNmneesOgp50pkRoJJoFR7waVdGPKCTLTNSvSMDP7NvhJ6Gf0+IVnKh//k6BdSvkL5ZMBBq9IWaIi68gdJ2iXTK9OFcOEqXsrwtNgMJCQkJCQk/zsB35gy2CL4XJHAAAAAASUVORK5CYII=" />
                                    </button>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                    <!-- 달력  -->
                    <form action="/todolist_list.php" method="get">
                        <input type="date" name="list_start_date" style="display: none;">
                        <div class="todo-get-calendar">
                            <div class="nav">
                                <!-- 년 월 구하기 -->
                                <?php if ($month == 1) { ?>
                                    <a href="/todolist_list.php?year=<?php echo $year - 1 ?>&month=12">
                                        <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAxklEQVR4nO2VTQrCMBCFcwh/NnoZF66ajF3UwygYZkKPouIMBS+ip1DQegglhUIrLie40AezfV94eZMY85e2bOAZkFwB+QxhP1U1L0oeOuI7kDzjOOK1KsAhH1rzOIsgczVziwJd8whTM8/8dgAotw7gYcvdWA0AJNw/vRTJogHiY7rWoNS5r0ZqAEfse60hWaqZNwDkTVJAExFKnSyiqKSX3Oq9pupRZakX7XtRBcXH7lOrAGVlknw4KBcgPuW+mqgDzM/pBWGTysH2H670AAAAAElFTkSuQmCC">
                                    </a>
                                <?php } else { ?>
                                    <a href="/todolist_list.php?year=<?php echo $year ?>&month=<?php echo $month - 1 ?>">
                                        <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAxklEQVR4nO2VTQrCMBCFcwh/NnoZF66ajF3UwygYZkKPouIMBS+ip1DQegglhUIrLie40AezfV94eZMY85e2bOAZkFwB+QxhP1U1L0oeOuI7kDzjOOK1KsAhH1rzOIsgczVziwJd8whTM8/8dgAotw7gYcvdWA0AJNw/vRTJogHiY7rWoNS5r0ZqAEfse60hWaqZNwDkTVJAExFKnSyiqKSX3Oq9pupRZakX7XtRBcXH7lOrAGVlknw4KBcgPuW+mqgDzM/pBWGTysH2H670AAAAAElFTkSuQmCC">
                                    </a>
                                <?php } ?>
                                <p><?php echo  "$year 년 $month 월" ?> </p>
                                <?php if ($month == 12) { ?>
                                    <a href="/todolist_list.php?year=<?php echo $year + 1 ?>&month=1">
                                        <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAuUlEQVR4nO2UQQrCQAxF5xBWV3oYBVftpILjaZSSKT2KSn8QvIieQkH0EEpBoXWdLIo+yDYP/vyJc380SAtMiHGmKFdimTltPGNNUZ7NeJZ7qJCoCvJS5h/Be+C08Yy6LfEsQVWQFtsBsdxakkdW7UaqkoyFulHh6LShKDCNKlRImiaZtsqzhK9WbfojCNYRkeUj51FW3eWoTT/aojgM7aKJWPbn2Hnrc03lfkwRJ2K5ZCWmqsvdz/MCS6HK05bgwhsAAAAASUVORK5CYII=">
                                    </a>
                                <?php } else { ?>
                                    <a href="/todolist_list.php?year=<?php echo $year ?>&month=<?php echo $month + 1 ?>">
                                        <img class="material-icons" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAuUlEQVR4nO2UQQrCQAxF5xBWV3oYBVftpILjaZSSKT2KSn8QvIieQkH0EEpBoXWdLIo+yDYP/vyJc380SAtMiHGmKFdimTltPGNNUZ7NeJZ7qJCoCvJS5h/Be+C08Yy6LfEsQVWQFtsBsdxakkdW7UaqkoyFulHh6LShKDCNKlRImiaZtsqzhK9WbfojCNYRkeUj51FW3eWoTT/aojgM7aKJWPbn2Hnrc03lfkwRJ2K5ZCWmqsvdz/MCS6HK05bgwhsAAAAASUVORK5CYII=">
                                    </a>
                                <?php } ?>

                            </div>
                            <div class="calendar">
                                <div class="calendar-detail">
                                    <ul class="weeks">
                                        <li>일</li>
                                        <li>월</li>
                                        <li>화</li>
                                        <li>수</li>
                                        <li>목</li>
                                        <li>금</li>
                                        <li>토</li>
                                    </ul>
                                    <ul class="days">
                                        <!-- 달력 날짜 표시 -->
                                        <!-- $total_week: 현재 월에 포함된 주의 총 수를 나타냅니다. 이 수만큼의 행이 생성 -->
                                        <!-- $n: 날짜를 나타내는 변수로, 각 주의 첫 번째 날부터 시작하여 증가합 -->
                                        <!-- $i: 외부 루프의 반복 횟수를 추적하는 변수 -->
                                        <?php for ($n = 1, $i = 0; $i < $total_week; $i++) { ?>
                                            <!-- $k: 내부 루프의 반복 횟수를 추적하는 변수 -->
                                            <?php for ($k = 0; $k < 7; $k++) { ?> 
                                                <li>
                                                    <!-- $total_day보다 $n이 작고, $k가 $start_week보다 크거나 같은 경우에만 버튼이 생성 -->
                                                    <?php if (($n > 1 || $k >= $start_week) && ($total_day >= $n)) { ?>
                                                        <!-- 선택된 날짜와 현재 날짜($year . '-' . $month . '-' . $n)가 같은 경우에는 btn-selected-date 클래스가 추가되어 선택된 날짜를 강조 표시 -->
                                                        <button type="submit" class="<?php echo ($selected_date === ($year . '-' . $month . '-' . $n)) ? 'btn-selected-date' : ''; ?>" name="selected_date" value="<?php echo $year . '-' . $month . '-' . $n; ?>">
                                                            <!-- 현재 날짜를 보여주고 1씩 더해줌 -->
                                                            <?php echo $n++ ?>
                                                        <?php } ?>
                                                    </button>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="cat-frame">
                        <!-- 고양이 영상을 표시하는 iframe -->
                        <iframe width="200" height="270" src="https://www.youtube.com/embed/GzWVk9wPBAk" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>