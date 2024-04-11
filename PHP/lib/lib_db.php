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
function db_select_todos_list1(&$conn) {
    $sql = "SELECT NAME, PROFILE 
            FROM informations 
            WHERE cat_no = 1"; // cat_no를 1로 설정

    // Query 실행
    $stmt = $conn->prepare($sql);
    $stmt->execute(); 
    $result1 = $stmt->fetchAll();

    // 리턴
    return $result1;
}



function db_select_todos_list2(&$conn, &$array_param) {
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
    
    // 선택한 날짜가 있을 경우에만 해당 조건을 추가합니다.
    if (isset($array_param['selected_date'])) {
        $sql .= "AND todo_date = :selected_date ";
    } else {
        // 선택한 날짜가 없을 경우에는 오늘의 날짜를 기본값으로 합니다.
        $array_param['selected_date'] = date('Y-m-d');
        $sql .= "AND todo_date = :selected_date ";
    }

    $sql .= "ORDER BY "
    . "list_no DESC ";
    
    $stmt = $conn->prepare($sql);
    
    // 바인딩
    $stmt->bindParam(':selected_date', $array_param['selected_date']);

    $stmt->execute();
    $result2 = $stmt->fetchAll();

    return $result2;
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
        :todo_date, 
        :content, 
        0
    )";


    // Query 실행
    $stmt = $conn->prepare($sql);

    // 바인딩
    $stmt->bindParam(':list_no', $array_param['list_no']);
    $stmt->bindParam(':todo_date', $array_param['todo_date']);
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

function db_update_contents_checked(&$conn, &$array_param) {
    // SQL
    $sql = 
    " UPDATE todos
    SET checked = CASE WHEN checked = '0' THEN '1' ELSE '0' END
    WHERE list_no = :list_no 
    AND deleted_at IS NULL"
    ;

    // Query 실행
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    // 리턴
    return $stmt->rowCount();
}

// 리스트페이지 끝


// 달력페이지 시작
function db_select_todos_list_with_date($conn, $chk_day) {
    // 해당 날짜의 할 일 목록 중에서 checked가 1인 항목을 가져오는 쿼리를 실행합니다.
    $sql = "SELECT * 
    FROM todos 
    WHERE todo_date = :chk_day 
    AND checked = 0";
    
    // PDO를 사용하는 예시
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':chk_day', $chk_day);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

// 달력페이지 끝


// join 페이지
function db_insert_profile(&$conn, &$array_param){
    $sql =
        " INSERT INTO informations( "
        ."  PROFILE "
        ."  ,NAME "
        ."  ,birth_at "		
        ."  ,gender "
        ."  ,weight "	
        ." ) "
        ." VALUES( "	
        ."  :PROFILE "
        ."  ,:NAME "
        ."  ,:birth_at "		
        ."  ,:gender "
        ."  ,:weight "	
        ." ) "
    ;	
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}

// calendar 페이지
function db_select_todolist_no(&$conn, &$array_param){
    $sql = 
        " SELECT "
        ." list_no "
        ." todo_date "
        ." content "
        ." FROM "
        ." todos "
        ." WHERE "
        ." list_no = :list_no "
        ;
        $stmt = $conn->prepare($sql);
        $stmt->execute($array_param);
        $result = $stmt->fetchAll();
        
        return $result;
    }

    
// 내 정보 페이지
function db_count_checked($conn) {
    // SQL
    $sql = 
    "SELECT 
    COUNT(checked) chk_ttl
    , SUM(CASE WHEN checked = '1' THEN 1 ELSE 0 END) chk_cnt
    FROM todos
    WHERE deleted_at IS NULL "
    ;

    // 쿼리 실행
    $stmt = $conn->query($sql);
    
    // 쿼리 결과 가져옴
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 결과 반환
    return $result; 

}

function db_select_information(&$conn, &$array_param) {
    //SQL
    $sql =
    "SELECT
        name
        ,profile
        ,gender
        ,birth_at
        ,weight
    FROM informations
    WHERE cat_no = 1 "
    ;

    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(); 

    return $result;

}

function db_update_information(&$conn, &$array_param) {
    //SQL
    $sql =
        "UPDATE informations
        SET
            name = :name
            ,gender = :gender
            ,birth_at = :birth_at
            ,weight = :weight
        WHERE cat_no = :cat_no " 
        ;
            
    ;

    // 쿼리 실행
    $stmt = $conn->prepare($sql);

    // 바인딩 매개 변수 설정
    $stmt->bindParam(':name', $array_param['name']);
    $stmt->bindParam(':gender', $array_param['gender']);
    $stmt->bindParam(':birth_at', $array_param['birth_at']);
    $stmt->bindParam(':weight', $array_param['weight']);
    $stmt->bindParam(':cat_no', $array_param['cat_no']);
    

    $stmt->execute();

    return true;
}
