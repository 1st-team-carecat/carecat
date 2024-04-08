<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

if(REQUEST_METHOD === "POST") {
    try {
        $PROFILE = isset($_POST["PROFILE"]) ? ($_POST["PROFILE"]) : "";
        $NAME = isset($_POST["NAME"]) ? ($_POST["NAME"]) : "";
        $birth_at = isset($_POST["birth_at"]) ? ($_POST["birth_at"]) : "";
        $gender = isset($_POST["gender"]) ? ($_POST["gender"]) : "";

        $arr_err_param = [];
        if($PROFILE === ""){
            $arr_err_param[] = "PROFILE";
        }
        if($NAME === ""){
            $arr_err_param[] = "NAME";
        }
        if($birth_at === ""){
            $arr_err_param[] = "birth_at";
        }
        if($gender === ""){
            $arr_err_param[] = "gender";
        }
        if(count($arr_err_param) > 0){
            throw new Exception("Parameter Error : ".implode(", ", $arr_err_param));
        }

        $conn = my_db_conn();
        $conn->beginTransaction();

        $arr_param = [
            "PROFILE" => $PROFILE
            ,"NAME" => $NAME
            ,"birth_at" => $birth_at
            ,"gender" => $gender
        ];
        $result = db_insert_profile($conn, $arr_param);

        if($result !==1 ){
            throw new Exception("Insert Profile count");
        }

        $conn->commit();

        header("Location: todolist_mypage.php");
        exit;

    } catch (\Throwable $e) {
        if(!empty($conn) && $conn->inTransaction()){
            $conn->rollBack();
        }
        echo $e->getMessage();
        exit;
    } finally {
        if(!empty($conn)){
            $conn = null;
        }
    }
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
    </header>
    <main class="main-box">
        <div class="join-title">
            비슷한 아이를 선택해주세요!
        </div>
        <form action="./todolist_mypage.php" method="POST">
            <div class="join-img-box">
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select1" name="img-select" value="0" required>
                    <label for="img-select1" class="radio-img-label">
                        <img src="./css/11zon_cropped-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select2" name="img-select" value="1" required>
                    <label for="img-select2" class="radio-img-label">
                        <img src="./css/11zon_cropped__1_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__1_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select3" name="img-select" value="2" required>
                    <label for="img-select3" class="radio-img-label">
                        <img src="./css/11zon_cropped__2_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__2_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select4" name="img-select" value="3" required>
                    <label for="img-select4" class="radio-img-label">
                        <img src="./css/11zon_cropped__3_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__3_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select5" name="img-select" value="4" required>
                    <label for="img-select5" class="radio-img-label">
                        <img src="./css/11zon_cropped__4_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__4_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
            </div>
            <div class="join-content-box">
                <div class="join-content">
                    <label for="name">이름</label>
                    <div class="content-title">
                        <input type="text" name="name" id="name" required>
                    </div>
                </div>
                <div class="join-content">
                    <label for="gender">성별</label>
                    <div class="content-title">
                        <input type="radio" class="btn" name="gender" id="0" required>
                        <label for="0" class="men">수컷</label>
                        <input type="radio" class="btn" name="gender" id="1" reauired>
                        <label for="1" class="women">암컷</label>
                    </div>
                </div>
                <div class="join-content">
                    <label for="birthday">생년월일</label>
                    <div class="content-title">
                        <input type="date" class="join-date" name="birthday" id="birthday" required>
                    </div>
                </div>
                <div class="join-content">
                    <label for="weight">몸무게</label>
                    <div class="content-title">
                        <input type="text" name="weight" id="weight" required>
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