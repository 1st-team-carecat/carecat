<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    // 데이터를 서버에 보내기 위함
    if (REQUEST_METHOD === "POST") {
        $PROFILE = isset($_POST["PROFILE"]) ? trim($_POST["PROFILE"]) : "";
        $NAME = isset($_POST["NAME"]) ? trim($_POST["NAME"]) : "";
        $birth_at = isset($_POST["birth_at"]) ? trim($_POST["birth_at"]) : "";
        $gender = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
        $weight = isset($_POST["weight"]) ? trim($_POST["weight"]) : "";
        $adopt_at = isset($_POST["adopt_at"]) ? trim($_POST["adopt_at"]) : "";

        // 필수 입력 필드가 비어 있는지 확인 후 있으면 $arr_err_param 배열에 해당 필드 이름 추가함
        $arr_err_param = [];
        if ($PROFILE === "") {
            $arr_err_param[] = "PROFILE";
        }
        if ($NAME === "") {
            $arr_err_param[] = "NAME";
        }
        if ($birth_at === "") {
            $arr_err_param[] = "birth_at";
        }
        if ($gender === "") {
            $arr_err_param[] = "gender";
        }
        if ($weight === "") {
            $arr_err_param[] = "weight";
        }
        if ($adopt_at === "") {
            $arr_err_param[] = "adopt_at";
        }
        if (count($arr_err_param) > 0) {
            throw new Exception("Parameter Error : " . implode(", ", $arr_err_param));
        }

        // 데이터 베이스 연결
        $conn = my_db_conn();

        // 데이터 베이스 트랜잭션
        $conn->beginTransaction();

        $arr_param = [
            "PROFILE" => $PROFILE, "NAME" => $NAME, "birth_at" => $birth_at, "gender" => $gender, "weight" => $weight, "adopt_at" => $adopt_at
        ];

        // 작성한 데이터 저장
        $result = db_insert_profile($conn, $arr_param);

        // $result 가 1이 아닌 경우는 데이터 저장 실패한 것으로 간주하여 예외 발생
        if ($result !== 1) {
            throw new Exception("Insert Profile count");
        }

        $conn->commit();

        header("Location: todolist_mypage.php");
        exit;
    }

} catch (\Throwable $e) {
    // 예외 발생한 경우, 현재 진행 중인 데이터 베이스 트랜잭션이 있는지 확인
    // 트랜잭션 중이라면 ($conn->inTransaction()이 참이면) 트랜잭션 롤백하여 이전 상태로 복구
    if (!empty($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }

    // 예외 메세지 출력
    echo $e->getMessage();
    exit;

} finally {
    if (!empty($conn)) {
        // 데이터 베이스 연결 해제
        $conn = null;
    }
}

?>




<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>케어해달라냥 가입페이지</title>
    <link rel="stylesheet" href="./css/todolist.css">
    <link rel="icon" href="/img/favicon.png">
</head>

<body>
    <header>
        <div class="main-title">
            <img src="/img/content-title.png" class="title-img">
        </div>
    </header>
    <main class="main-box">
        <div class="join-title">
            비슷한 아이를 선택해주세요!
        </div>
        <form action="./todolist_join.php" method="POST">
            <div class="join-img-box">
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select1" name="PROFILE" value="/img/1.png" required>
                    <label for="img-select1" class="radio-img-label">
                        <img src="/img/1.png" class="cat-img">
                        <img src="/img/1.png" class="cat-img-unchecked">
                    </label>
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select2" name="PROFILE" value="/img/2.png" required>
                    <label for="img-select2" class="radio-img-label">
                        <img src="/img/2.png" class="cat-img">
                        <img src="/img/2.png" class="cat-img-unchecked">
                    </label>
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select3" name="PROFILE" value="/img/3.png" required>
                    <label for="img-select3" class="radio-img-label">
                        <img src="/img/3.png" class="cat-img">
                        <img src="/img/3.png" class="cat-img-unchecked">
                    </label>
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select4" name="PROFILE" value="/img/4.png" required>
                    <label for="img-select4" class="radio-img-label">
                        <img src="/img/4.png" class="cat-img">
                        <img src="/img/4.png" class="cat-img-unchecked">
                    </label>
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select5" name="PROFILE" value="/img/5.png" required>
                    <label for="img-select5" class="radio-img-label">
                        <img src="/img/5.png" class="cat-img">
                        <img src="/img/5.png" class="cat-img-unchecked">
                    </label>
                </div>
            </div>
            <div class="join-content-box">
                <div class="join-content">
                    <label for="NAME">이름</label>
                    <div class="content-title">
                        <input type="text" name="NAME" id="NAME" required autocomplete="off">
                    </div>
                </div>
                <div class="join-content">
                    <label for="gender">성별</label>
                    <div class="content-title">
                        <input type="radio" name="gender" id="0" value="0" required>
                        <label for="0">수컷</label>
                        <input type="radio" name="gender" id="1" value="1" reauired>
                        <label for="1">암컷</label>
                    </div>
                </div>
                <div class="join-content">
                    <label for="birthday">생년월일</label>
                    <div class="content-title">
                        <input type="date" class="join-date" name="birth_at" id="birth_at" required>
                    </div>
                </div>
                <div class="join-content">
                    <label for="weight">몸무게</label>
                    <div class="content-title">
                        <input type="number" name="weight" id="weight" required placeholder=" kg" step="0.1" autocomplete="off">
                    </div>
                </div>
                <div class="join-content">
                    <label for="birthday">입양일자</label>
                    <div class="content-title">
                        <input type="date" class="join-date" name="adopt_at" id="adopt_at" required>
                    </div>
                </div>
                <footer>
                    <button type="submit" class="join-save">저장</button>
                </footer>
            </div>
        </form>
    </main>
</body>

</html>