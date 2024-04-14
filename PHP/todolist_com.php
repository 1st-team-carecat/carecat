<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php");												
require_once(FILE_LIB_DB);

try {

    $conn = my_db_conn();
    $todo_date = isset($_POST["todo_date"]) ? trim($_POST["todo_date"]) : date("Y-m-d");
    $list_no = isset($_POST["list_no"]) ? $_POST["list_no"] : "";

    $arr_param = [
        "list_no" => $list_no
    ];

    $conn->beginTransaction();
    $result = db_update_contents_checked($conn, $arr_param);

    $conn->commit();

    // 리스트 페이지로 이동
    header("Location: todolist_list.php?selected_date=" . $todo_date);

} catch(\Throwable $e) {
    echo $e->getMessage();
    exit;

} finally {
    if(!empty($conn)) {
        $conn = null;
    }
}