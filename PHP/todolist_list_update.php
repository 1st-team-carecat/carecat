<?php
// 설정한 파일 호출

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php");
 // DB관련 라이브러리 호출
require_once(FILE_LIB_DB); 

try { 
    //POST 방식으로 요청이 들어왔는지 확인
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //필수 파라미터인 
        //1.리스트 번호(list_no) 
        //2.내용(content) 
        //3.할일 날짜(todo_date)를 가져옵니다.
        //구체 설명: 슈퍼파라미터 변수인 $_POST에 list_no가 존재하면
        //좌측 $list_no에 담아주고, 그렇지 않으면 빈문자열을 실행합니다.
        //content todo_date도 동일합니다.
        $list_no = isset($_POST["list_no"]) ? $_POST["list_no"] : "";
        $content = isset($_POST["content"]) ? $_POST["content"] : "";
        $todo_date = isset($_POST["todo_date"]) ? $_POST["todo_date"] : ""; // TODO : 확인
        //빈 파라미터 체크 위해서 오류파라미터 배열 초기화
        $arr_err_param = [];
        //if (조건식) { }
            //조건식이 참일 경우 실행되는 코드
        
        //1. '$list_no' 변수가 빈 문자열 ('""')과 일치하는지 (리스트 번호가
        //비어 있는지를 체크하고, 
        //2. '$list_no'가 아무런 값이 없는 경우, '$arr_err_param' 배열에 "list_no"라는
        //문자열이 추가된다.
        
        if ($list_no === "") {
            $arr_err_param[] = "list_no";
        }
        //content가 비어있는지 확인하여 에러 배열에 추가합니다.
        if ($content === "") {
            $arr_err_param[] = "content";
        }
        //에러 파라미어 배열의 길이가 0보다 크면, 즉 필수 파라미터가 하나라도 비어있으면 
        if (count($arr_err_param) > 0) {
            // 에러 메시지를 생성하여 예외로 던짐
            throw new Exception("Parameter Error: " . implode(",", $arr_err_param));
        }
        // 데이터베이스 연결
        $conn = my_db_conn();
        // 트랜잭션 시작합니다
        $conn->beginTransaction();
        //업데이트(수정)할 파라미터 배열 생성
        $arr_param = [
            "list_no" => $list_no,
            "content" => $content

        ];
        //DB(데이터베이스)에 수정(update) 실행
        $result = db_update_todos_no($conn, $arr_param);
        // 커밋 (트랜잭션이 성공하면  DB 변경사항을 확정합니다.) *commit 약속하다 전념하다 맡기다
        $conn->commit();
        // 수정이 완료되면 다시 리스트페이지로 이동합니다.
        //지정된 날짜로 위치하도록 (기존 ->오늘날짜 기준)
        header("Location: todolist_list.php?selected_date=" . $todo_date);
        exit;
    }
    //트랜잭션 중 에러가 발생한 경우 롤백 (변경사항 취소)

} catch (\Throwable $e) {
    if (!empty($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo $e->getMessage();
    exit;
    //에러메시지 출력 후 종료합니다.
    
} finally {
    //데이터베이스 연결을 해제합니다.
    if (!empty($conn)) {
        $conn = null;
    }
}