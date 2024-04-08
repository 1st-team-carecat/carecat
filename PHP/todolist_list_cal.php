<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        $conn = my_db_conn(); // connection 함수 호출
        // $page_num = isset($_GET["page"]) ? $_GET["page"] : $page_num; 
        $todo_date= isset($_GET["todo_date"]) ? $_GET["todo_date"] : ""; // 파라미터에서 todo_date 획득
        $list_start_date = !empty($_GET["list_start_date"]) ? $_GET["list_start_date"] : date('Y-m-d');
        
        // 쿼리를 선택된 날짜에 따라 조절합니다.
        $sql =
            "SELECT "
            . "list_no "
            . ",cat_no "
            . ",content "
            . ",todo_date "
            . ",checked "
            . "FROM "
            . "todos "
            . "WHERE "
            . "deleted_at IS NULL "
            . "AND cat_no = 1 "; 

        // 선택한 날짜가 있을 경우에만 WHERE 절을 추가합니다.
        if (!empty($todo_date)) {
            $sql .= "AND todo_date = :todo_date ";
        }

        $sql .= "ORDER BY list_no DESC";

        $stmt = $conn->prepare($sql);

        // 선택한 날짜가 있을 경우에만 바인딩합니다.
        if (!empty($todo_date)) {
            $stmt->bindParam(':todo_date', $todo_date);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();

        // 결과 반환
        // return $result;
        // print_r($result);


        header("Location: ./todolist_list.php");
        exit;
    } catch (\Throwable $e) {
        echo $e->getMessage();
        exit;
    } finally {
        if (!empty($conn)) {
            $conn = null;
        }
    }
}
