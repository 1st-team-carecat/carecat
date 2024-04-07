<?php
// 공통
function my_db_conn(){
    $option = [																			
		PDO::ATTR_EMULATE_PREPARES	=>	FALSE,								
		PDO::ATTR_ERRMODE	=>	PDO::ERRMODE_EXCEPTION,								
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC								
	];																			
    return new PDO(MARIADB_DSN, MARIADB_USER, MARIADB_PASSWORD, $option);

}

// 리스트 페이지 시작
function db_select_todos_cnt($conn){
    $sql =  // sql 작성
        " SELECT "
        ."	COUNT(list_no) as cnt "
        ." FROM "
        ."  todos "
        ." WHERE "
        ." deleted_at IS NULL "
    ;						 

    // Query 실행
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll();

    // 리턴
    return (int)$result[0]["cnt"];
}

function db_select_todos_list(&$conn, &$array_param){
    $sql =
        " SELECT "
        ." list_no "
        ." cat_no "
        ." ,content "
        ." ,todo_date "
        ." ,checked "
        ." FROM "
        ." todos "
        ." WHERE "
        ." deleted_at IS NULL "
        ." ORDER BY "
        ." list_no DESC "
;
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);
    $result = $stmt->fetchAll();
    
    return $result;
}

function db_update_todos(&$conn, &$array_param){
    $sql =
        " UPDATE "
        ." todos "
        ." SET "
        ." content = :content "
        ." WHERE "
        ." list_no = :list_no "
        ;
    $stmt = $conn->prepare($sql);
    $stmt->execute(array(':cat_no' => $array_param['cat_no']));
    $stmt->execute(array(':content' => $array_param['content'], ':list_no' => $array_param['list_no']));

    
    return $stmt->rowCount();
}

function db_insert_list(&$conn, &$array_param){
    // SQL
    $sql = "INSERT INTO todos (
        cat_no, 
        todo_date,
        content,
        checked
    ) 
    VALUES (
        1,
        CURDATE(), 
        :content, 
        0
    )";


    // Query 실행
    $stmt = $conn->prepare($sql);

    // :content 매개변수에 해당하는 값 바인딩
    $stmt->bindParam(':content', $array_param['content']);

    // Query 실행
    $stmt->execute(); 

    // 리턴
    return $stmt->rowCount();
}

// 리스트 페이지 끝