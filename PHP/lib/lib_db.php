<?php

function my_db_conn(){
    $option = [																			
		PDO::ATTR_EMULATE_PREPARES	=>	FALSE,								
		PDO::ATTR_ERRMODE	=>	PDO::ERRMODE_EXCEPTION,								
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC								
	];																			
    return new PDO(MARIADB_DSN, MARIADB_USER, MARIADB_PASSWORD, $option);

}

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
        ." cat_no "
        ." ,content "
        ." ,created_at "
        ." ,checked "
        ." FROM "
        ." todos "
        ." WHERE "
        ." cat_no = :cat_no "
        ." AND DATE(created_at) = CURDATE()";
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