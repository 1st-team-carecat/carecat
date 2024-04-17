<?php
// 공통
function my_db_conn()
{
    $option = [
        PDO::ATTR_EMULATE_PREPARES       => FALSE,
        PDO::ATTR_ERRMODE                => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC
    ];
    return new PDO(MARIADB_DSN, MARIADB_USER, MARIADB_PASSWORD, $option);
}

// 리스트 페이지 시작

function db_select_profile(&$conn) {
    $sql = 
        " SELECT "
        ."  NAME "
        ."  ,PROFILE " 
        ." FROM " 
        ."  informations " 
        ." WHERE " 
        ."  cat_no = 2 " // cat_no를 2로 설정
    ;

    // Query 실행
    $stmt = $conn->prepare($sql); // 쿼리를 데이터베이스에 제출하지 않고 먼저 준비
    $stmt->execute(); //  데이터베이스에서 쿼리가 실행되고 결과가 반환
    $result1 = $stmt->fetchAll(); // 쿼리 결과를 배열 형태로 모두 가져옴

    // 리턴
    return $result1; // $result1로 반환
}



function db_select_todos_list(&$conn, &$array_param) {
    $sql =
        " SELECT "
        ."  list_no "
        ."  ,content "
        ."  ,todo_date "
        ."  ,checked "
        ." FROM "
        ."  todos "
        ." WHERE "
        ."  deleted_at IS NULL "

    ;

// 선택한 날짜가 있을 경우에만 해당 조건을 추가합니다.
if (isset($array_param['selected_date'])) {
    $sql .= "AND todo_date = :selected_date ";
} else {
    // 선택한 날짜가 없을 경우에는 오늘의 날짜를 기본값으로 합니다.
    $array_param['selected_date'] = date('Y-m-d');
    $sql .= "AND todo_date = :selected_date ";
}

$sql .=
    " ORDER BY "
    ." checked ASC, list_no DESC "; // checked는 오름차순, list_no는 내림차순

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
    $sql = 
        " INSERT INTO todos ( "
        ."  todo_date "
        ."  ,content "
        ."  ,checked "
        ." ) " 
        ." VALUES ( "
        ."  :todo_date  "
        ."  ,:content  "
        ."  ,0 "
        ." ) "
    ;


    // Query 실행. prepare() 함수를 사용하여 데이터베이스 연결 객체인 $conn에서 $stmt(Statement) 객체를 만듬
    $stmt = $conn->prepare($sql);

    // 바인딩 쿼리의 매개변수에 PHP 변수를 바인드하여 SQL 쿼리가 실행될 때 이러한 값들을 적절히 대체할 수 있도록 합니다. 여기서는 ":list_no", ":todo_date", ":content"와 같은 쿼리의 placeholder에 $array_param 배열에서 해당하는 값을 바인딩
    // SQL 삽입(SQL Injection) 공격을 방지하는 데 중요한 역할
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
        " UPDATE todos "
        ." SET " 
        ."  content = :content "
        ."  ,updated_at = NOW() "
        ." WHERE " 
        ."  list_no = :list_no"
    ;

    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}




// 리스트페이지 - 삭제
function db_delete_todos_no($conn, $array_param)
{
    $sql =
        " UPDATE todos "
        ." SET "
        ."  deleted_at = NOW()"
        ." WHERE "
        ."  list_no = :list_no"
    ;

    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}

function db_update_contents_checked(&$conn, &$array_param) {
    // SQL
    $sql = 
        " UPDATE todos "
        ." SET " 
        ."  checked = CASE WHEN checked = '0' THEN '1' ELSE '0' END "
        ." WHERE " 
        ."  list_no = :list_no "
        ." AND "
        ."  deleted_at IS NULL "
    ;

    // Query 실행
    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}

// 리스트페이지 끝


// 달력페이지 시작
function db_select_todos_list_with_date($conn, $chk_day) {
    // 해당 날짜의 할 일 목록 중에서 checked 상태와 상관없이 모든 항목을 가져오는 쿼리를 실행합니다.
    $sql = 
        " SELECT * " 
        ." FROM " 
        ."  todos " 
        ." WHERE " 
        ."  todo_date = :chk_day "
        ."  AND checked = '0' "
        ."  AND deleted_at IS NULL "
    ;
    
    // PDO를 사용하는 예시
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':chk_day', $chk_day);
    $stmt->execute();
    $result = $stmt->fetchAll();
    
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
        ."  ,adopt_at "	
        ." ) "
        ." VALUES( "	
        ."  :PROFILE "
        ."  ,:NAME "
        ."  ,:birth_at "		
        ."  ,:gender "
        ."  ,:weight "	
        ."  ,:adopt_at "	
        ." ) "
    ;

    $stmt = $conn->prepare($sql);
    $stmt->execute($array_param);

    return $stmt->rowCount();
}
 
// 내 정보 페이지
// function db_count_checked($conn) {
//     // SQL
//     $sql = 
//     "SELECT 
//         (SELECT COUNT(checked)
//             FROM todos
//             WHERE deleted_at IS NULL
//             AND YEAR(todo_date) = YEAR(CURRENT_DATE())
//             AND MONTH(todo_date) = MONTH(CURRENT_DATE()) ) chk_ttl
//             ,SUM(CASE WHEN checked = '1' THEN 1 ELSE 0 END) chk_cnt
//     FROM todos
//     WHERE deleted_at IS NULL
//     AND cat_no = 2 "
//     ;

function db_count_checked($conn) {
    // SQL
    $sql = 
        " SELECT "
       ."   COUNT(*) chk_ttl "
       ."   ,SUM(checked = 1) chk_cnt "
       ." FROM " 
       ."   todos "
       ." WHERE " 
       ."   deleted_at IS NULL "
       ."   AND todo_date BETWEEN DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01') " 
       ."   AND LAST_DAY(CURRENT_DATE()) "
    ;

    // 쿼리 실행
    $stmt = $conn->query($sql);
    
    // 쿼리 결과 가져옴
    $result = $stmt->fetchAll();
    
    // 결과 반환
    return $result[0]; 

}

function db_select_information(&$conn) {
    //SQL
    $sql =
        " SELECT "
        ."  name "
        ."  ,profile "
        ."  ,gender "
        ."  ,birth_at "
        ."  ,weight "
        ."  ,adopt_at "
        ." FROM " 
        ."  informations "
        ." WHERE "
        ."  cat_no = 2 "
    ;

    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(); 

    return $result;

}
