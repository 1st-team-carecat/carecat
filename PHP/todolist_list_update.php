<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") { 
        $list_no = isset($_POST["list_no"]) ? $_POST["list_no"] : "";
        $content = isset($_POST["content"]) ? $_POST["content"] : "";

        $arr_err_param = [];
        if ($list_no === "") {
            $arr_err_param[] = "list_no";
        }
        if ($content === "") {
            $arr_err_param[] = "content";
        }
        if (count($arr_err_param) > 0) {
            throw new Exception("Parameter Error: " . implode(",", $arr_err_param));
        }

        $conn = my_db_conn();

        $conn->beginTransaction();
        $arr_param = [
            "list_no" => $list_no,
            "content" => $content

        ];
        $result = db_update_todos_no($conn, $arr_param);

        $conn->commit();
        header("Location: ./todolist_list.php");
        exit;
    }
} catch (\Throwable $e) {
    if (!empty($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo $e->getMessage();
    exit;
} finally {
    if (!empty($conn)) {
        $conn = null;
    }
}
