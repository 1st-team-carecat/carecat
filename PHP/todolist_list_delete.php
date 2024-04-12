<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리

try {
    $conn = my_db_conn();

    $list_no = isset($_POST["list_no"]) ? $_POST["list_no"]  : "";
    $page = isset($_POST["page"]) ? $_POST["page"]  : "";
    $todo_date = isset($_POST["todo_date"]) ? $_POST["todo_date"] : "";

    $arr_err_param = [];
    if ($list_no === "") {
        throw new Exception("Parameter Error: list_no");
    }

    $conn->beginTransaction();
    $arr_param = [
        "list_no" => $list_no
    ];
    $result = db_delete_todos_no($conn, $arr_param);

    if ($result !== 1) {
        throw new Exception("Delete Boards no count");
    }

    $conn->commit();
    header("Location: todolist_list.php?selected_date=" . $todo_date);
    exit;
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit;
} finally {
    if (!empty($conn)) {
        $conn = null;
    }
}
