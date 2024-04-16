<?php
// 설정 파일 호출
require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php");
// DB관련 라이브러리 호출
require_once(FILE_LIB_DB); 

try {
    //데이터베이스 연결합니다.
    $conn = my_db_conn();
    //post 요청에서 할일 목록(list_no)를 가져옵니다.
    $list_no = isset($_POST["list_no"]) ? $_POST["list_no"]  : "";
    //페이지 번호와
    //할일 날짜도 가져옵니다.
    $todo_date = isset($_POST["todo_date"]) ? $_POST["todo_date"] : "";
    //유효성 검사(요청한 데이터가 어떤 조건에 충족하는지 확인하는 작업)를 위한 오류
    //파라미터 배열 초기화
    $arr_err_param = [];
    //만약 list_no가 빈 문자열이라면, 예외(exception)를 발생시킵니다.
    if ($list_no === "") {
        throw new Exception("Parameter Error: list_no");
    }
    //트랜잭션 시작
    $conn->beginTransaction();
    //삭제할 할 일 목록 번호를 담은 배열을 생성
    $arr_param = [
        "list_no" => $list_no
    ];
    //DB에서 할 일 목록 삭제
    //구체설명: 'db_delete_todos_no' 함수를 호출하여 'todos'테이블에서 특정
    //조건에 맞는 행을 삭제합니다.
    //'$conn':데이터베이스 연결을 나타내는 PDO 객체입니다.
    //'$arr_param': 삭제할 행을 식별하기 위한 매개변수들의 배열입니다.
    $result = db_delete_todos_no($conn, $arr_param);

    // 위 함수가 올바르게 실행되지 않았을 경우 예외 발생시킵니다.
    //구체설명: 삭제된 행의 수가 1이 아닌 경우를 확인합니다. 삭제가 정상적으로
    //이루어지지 않았을 때를 의미합니다.
    if ($result !== 1) {
        //삭제된 행의 수가 1이 아닌 경우 임의로 예외를 발생시킵니다.
        //"Delete Boards no count"라는 메시지와 함께 예외가 발생합니다.
        throw new Exception("Delete Boards no count");
    }
    //트랜잭션을 커밋합니다.
    $conn->commit();
    //할 일 목록 페이지로 redirect합니다.
    header("Location: todolist_list.php?selected_date=" . $todo_date);
    exit;
    
    //예외처리
} catch (\Throwable $e) {
    //'$connection'이 비어있지 않은지 확인 후 현재 트랜
    //잭션 중인지 여부 확인합니다.
    if (!empty($conn) && $conn->inTransaction()) {
        //만약, 데이터베이스 연결 존재하고, 현재 트랜잭션 중이라면
        //롤백(트랜잭션 내에서 수행한 모든 변경사항을 취소하고
        //이전 상태로 되돌리는 작업/데이터 일관성 유지에 중요한 단계) 실행합니다.
        $conn->rollBack();
    }
    //예외 메시지 출력
    echo $e->getMessage();
    //스크립트 종료
    exit;
} finally {
    //데이터베이스 연결이 존재하는 경우
    if (!empty($conn)) {
        //연결 닫기
        $conn = null;
    }
}