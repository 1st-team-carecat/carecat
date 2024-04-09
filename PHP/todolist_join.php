<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    if(REQUEST_METHOD === "POST") {
        $cat_no = isset($_POST["cat_no"]) ? trim($_POST["cat_no"]) : "";
        $PROFILE = isset($_POST["PROFILE"]) ? trim($_POST["PROFILE"]) : "";
        $NAME = isset($_POST["NAME"]) ? trim($_POST["NAME"]) : "";
        $birth_at = isset($_POST["birth_at"]) ? trim($_POST["birth_at"]) : "";
        $gender = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
        $weight = isset($_POST["weight"]) ? trim($_POST["weight"]) : "";


        $arr_err_param = [];
        if($PROFILE !== "0" && $PROFILE !== "1" && $PROFILE !== "2" && $PROFILE !== "3" && $PROFILE !== "4"){
            $arr_err_param[] = "PROFILE";
        }
        if($NAME === ""){
            $arr_err_param[] = "NAME";
        }
        if($birth_at === ""){
            $arr_err_param[] = "birth_at";
        }
        if($gender !== "0" && $gender !== "1"){
            $arr_err_param[] = "gender";
        }
        if($weight === ""){
            $arr_err_param[] = "weight";
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
            ,"weight" => $weight
        ];
        $result = db_insert_profile($conn, $arr_param);

        if($result !== 1){
            throw new Exception("Insert Profile count");
        }

        $conn->commit();

        header("Location: todolist_mypage.php");
        exit;
    }

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
var_dump($item["cat_no"]);

?>




<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>케어해달라냥 가입페이지</title>
    <link rel="stylesheet" href="./css/todolist.css">
    <script>
        // 입력 요소를 찾아서 해당 요소에 이벤트를 추가하는 함수
        function addInputEvents() {
            var inputs = document.querySelectorAll('input[type="text"]');
    
            inputs.forEach(function(input) {
                // 입력란에 포커스가 들어오면 placeholder를 숨김
                input.addEventListener('focus', function() {
                    this.placeholder = '';
                });
    
                // 입력란에서 포커스가 나가면 placeholder를 재설정
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.placeholder = 'Weight (kg)';
                    }
                });
            });
        }
    
        // 문서가 로드될 때 이벤트 추가
        document.addEventListener('DOMContentLoaded', function() {
            addInputEvents();
        });
    </script>  
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
        <form action="./todolist_join.php" method="POST">
            <input type="hidden" name="cat_no" value="<?php echo $item["cat_no"]; ?>">
            <div class="join-img-box">
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select1" name="PROFILE" value="0" required>
                    <label for="img-select1" class="radio-img-label">
                        <img src="./css/11zon_cropped-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select2" name="PROFILE" value="1" required>
                    <label for="img-select2" class="radio-img-label">
                        <img src="./css/11zon_cropped__1_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__1_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select3" name="PROFILE" value="2" required>
                    <label for="img-select3" class="radio-img-label">
                        <img src="./css/11zon_cropped__2_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__2_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select4" name="PROFILE" value="3" required>
                    <label for="img-select4" class="radio-img-label">
                        <img src="./css/11zon_cropped__3_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__3_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
                <div class="join-img">
                    <input type="radio" class="radio-img" id="img-select5" name="PROFILE" value="4" required>
                    <label for="img-select5" class="radio-img-label">
                        <img src="./css/11zon_cropped__4_-removebg-preview.png" class="cat-img">
                        <img src="./css/11zon_cropped__4_-removebg-preview.png" class="cat-img-unchecke">
                    </label> 
                </div>
            </div>
            <div class="join-content-box">
                <div class="join-content">
                    <label for="NAME">이름</label>
                    <div class="content-title">
                        <input type="text" name="NAME" id="NAME" required>
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
                        <input type="date" class="join-date" name="birth_at" id="birth_at" required >
                    </div>
                </div>
                <div class="join-content">
                    <label for="weight">몸무게</label>
                    <div class="content-title">
                        <input type="number" name="weight" id="weight" required placeholder="kg"> 
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