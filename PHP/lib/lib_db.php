<?php
// 공통
function my_db_conn()
{
    $option = [
        PDO::ATTR_EMULATE_PREPARES    =>    FALSE,
        PDO::ATTR_ERRMODE    =>    PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC
    ];
    return new PDO(MARIADB_DSN, MARIADB_USER, MARIADB_PASSWORD, $option);
}

// 리스트 페이지 시작
function db_select_todos_cnt($conn)
{
    $sql =  // sql 작성
        " SELECT "
        . "	COUNT(list_no) as cnt "
        . " FROM "
        . "  todos "
        . " WHERE "
        . " deleted_at IS NULL "
        . " AND DATE(todo_date) = CURDATE() ";

    // Query 실행
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll();

    // 리턴
    return (int)$result[0]["cnt"];
}

function db_select_todos_list(&$conn, &$array_param)
{
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
    . "AND cat_no = 1 " // 이 부분을 추가해줍니다.
    . "ORDER BY "
    . "list_no DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);
    $result = $stmt->fetchAll();

    return $result;
}

function db_update_todos(&$conn, &$array_param)
{
    $sql =
        " UPDATE "
        . " todos "
        . " SET "
        . " content = :content "
        . " WHERE "
        . " list_no = :list_no ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(array(':cat_no' => $array_param['cat_no']));
    $stmt->execute(array(':content' => $array_param['content'], ':list_no' => $array_param['list_no']));


    return $stmt->rowCount();
}

function db_insert_list(&$conn, &$array_param)
{
    // SQL
    $sql = "INSERT INTO todos (
        list_no,
        cat_no,
        todo_date,
        content,
        checked
    ) 
    VALUES (
        :list_no,
        1,
        CURDATE(), 
        :content, 
        0
    )";


    // Query 실행
    $stmt = $conn->prepare($sql);

    // 바인딩
    $stmt->bindParam(':list_no', $array_param['list_no']);
    $stmt->bindParam(':content', $array_param['content']);

    // Query 실행
    $stmt->execute();

    // 리턴
    return $stmt->rowCount();
}



// 리스트 페이지 -수정

function db_update_todos_no(&$conn, &$array_param)
{
    $sql =
        " UPDATE todos " .
        " SET content = :content " .
        " ,updated_at = NOW() " .
        " WHERE list_no = :list_no";
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}



// 리스트페이지 - 달력
//  수정하기


// 리스트페이지 - 삭제
function db_delete_todos_no($conn, $array_param)
{
    $sql =
        " UPDATE "
        . " todos "
        . " SET "
        . " deleted_at = NOW()"
        . " WHERE "
        . " list_no = :list_no";
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}

// 리스트페이지 끝








function db_count_checked($conn) {
    // SQL
    $sql = 
    " SELECT "
    ." COUNT(checked) chk_ttl, "
    ." SUM(CASE WHEN checked = '1' THEN 1 ELSE 0 END) chk_cnt "
    ." FROM "
    ." todos "
    ;

    // 쿼리 실행
    $stmt = $conn->query($sql);
 
    // 쿼리 결과 가져옴
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 결과 반환
    return $result; 

}