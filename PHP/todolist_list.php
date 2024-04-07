<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리
$page_num = 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        
        $content = isset($_POST["content"]) ? trim($_POST["content"]) : "";
        
        $arr_err_param = [];
        if ($content === "") {
            $arr_err_param[] = "content";
        }

        if (count($arr_err_param) > 0) {
            throw new Exception("Parameter Error: " . implode(". ", $arr_err_param));
        }
        $conn = my_db_conn();
        $conn->beginTransaction();

       
        $arr_param = [
            "content" => $content
            ,"todo_date" => date("Y-m-d")
        ];
        $result = db_insert_list($conn, $arr_param);

        if ($result !== 1) {
            throw new Exception("Insert Boards count");
        }

        $conn->commit();
        header("Location: todolist_list.php");
        exit;
    } catch (\Throwable $e) {
        if (!empty($conn)) {
            $conn->rollBack();
        }
        echo $e->getMessage();
        exit;
    } finally {
        if (!empty($conn)) {
            $conn = null;
        }
    }
}
else if ($_SERVER["REQUEST_METHOD"] === "GET") {
   try {
        $conn = my_db_conn(); // connection 함수 호출
        $page_num = isset($_GET["page"]) ? $_GET["page"] : $page_num; // 파라미터에서 page 획득
       
        $result = db_select_todos_list($conn, $arr_param);
      } catch (\Throwable $e) {
        echo $e->getMessage();
        exit;
      } finally {
        if(!empty($conn)){
          $conn = null;
        }
      }
}
?>








<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>할일 페이지</title>
    <link rel="stylesheet" href="./css/list.css" />
</head>

<body>
    <header>
        <div class="main-title">
            <img src="./css/content-title.png" class="title-img" />
        </div>
        <div class="header-profile-name">로미</div>
        <a href="">
            <img class="header-profile-img" src="./css/11zon_cropped__2_-removebg-preview.png" />
        </a>
    </header>
    <main class="main-box">
        <div class="box">
            <div class="menu-content">
                <div class="menu">
                    <a href="">내정보</a>
                </div>
                <div class="menu">
                    <a href="">할일</a>
                </div>
                <div class="menu">
                    <a href="">캘린더</a>
                </div>
            </div>
            <div class="content">
                <div class="content-list">
                    <form action="./todolist_list.php" method="POST">
                        <div class="list-box">
                            <label for="todo_date">
                                <input type="date" id="todo_date" name="todo_date"/>
                            </label>
                            <label for="content" class="text-list">
                                <input type="text" id="content" name="content" placeholder="할일을 추가하세요!"/>
                            </label>
                            <button type="submit">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAACXBIWXMAAAsTAAALEwEAmpwYAAADEElEQVR4nO2aS08UQRDH++DjLolI0JuvT2MgJho8eTBg1MUDxK1eSdYPIMJeJFO90XD1bCCccLdqF/CBHjyQqJ9AIQEx8UEWUzNLNKtopul5ZeefdLKZ3enp31ZPdXVVK5UrV65cubpU448XT2hDVzRSRSMtaKR32vCGNvQ9aLzhXwu+qwDWh+QelSXB7EoPmMYoIL3QhnftGj3XyAXpS6VVd3D5JBieBkNf7EE7GvK2NjxVmmn2q7Ro2Ht5WHt0Gwx/dgba0YI/ke4VKnNHE4WdqPI5jfwmKtC/gL8GrJ9NBLbo8cUorbr/NKetItJgrLAl5Ktg+EfssL/e7R1Avh4LrDY0khjon+CFyKexRt5JHPQ3S+tqfSASWDDN04C0mThkR/P9iNc47xS2/OTtkTi9cXhL06osj86AtSFIHOp/lq7SuBPY0kyzvx3xuBkY0lPpsx2Zzbmc2iWv1ndgYDA87dISArrX991HtVNuLU2TDjYC5C42Nrzb+QynwMjbB9pwgGmMurVAxMA+NN20B8aDbPGSAQakFSvYklfr00itrAEHY17qDQ2sJVPhejBxAAce+3J4YKRKVoElaWADvJBVYFnfLYD5Q1aBJTEYGhiQ1m0iKOVYVhEZ0icb4G+2EZRrWURkX0M/BLoQeD2so4gC2odFng8J/DE8sKH3UTiUzuekxmnp7luWqLsCD8D6UGaBkS6Ft7BZ6u2qzYPIr+JlDBgMLylbaeRC5oC9+g1rYJhd6XGZwEt9ikckHi8rSTxAvq9Sl6Y1QURmGUH9w7q05ey4BCAVnU69SBqNKadVfqTV5KH2nTWvnJZa0l5Mk5MIKgoVkQbTVi4Fjy+oKAWGhtMBS62i4WuqW448aEMjKk7pan1AloLY31mkzcin8X6amHl2Jk7vLd5YnKdKUuXy4iE5mBaltfcOpslJBJUWlbxaHyA9cBp7+33RZKoPnEJQU76lDS9b7aeRWrLFk7Jn2TSPqSxp7GHjuBS2/A0I8jwgrUk2VFLA7Saf19ox9ZT8Vu5Jety5cuXKlUslpJ/fX9oCxt+DDAAAAABJRU5ErkJggg==" />
                            </button>
                        </div>
                    </form>
                        <div class="scroll">
                            <form method="post">   
                    <?php foreach($result as $item){
                        ?>
                            <div class="chk-list">
                                <input type="checkbox" id="check1" />
                                <label for="check1"></label>

                                <input type="text" name="content" value="<?php echo $item["content"]?>"/>
                                <button type="submit" formaction="./todolist_list_update.php">
                                    <img
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAACXBIWXMAAAsTAAALEwEAmpwYAAABa0lEQVR4nO3XvUoDQRDA8VX8KAQ730QQfRcLC9NZ2cxoYEF7wTI7Z2M6BUtRgwQzk2BhaamFL6Hi18ndJYZILmfhcbOwf9j+x3CzxxoTCoVKaevwfN6e3M8ZH7LN20V00kaSGJ18IEkLGp0VoxvMvRQ8cvgNos668QcsQ7i2iQPJfj44O+Dkymiq1ribRZKziWiSd3XLWSuCO/5Shy6EO2mbqto+vlwA4oudSJb/DHfcS5a1MjA6vuljnvCos1QIrxI8/lrja2vbM7lwJ3taJhyPXmV8YLQ1CYw/11l3w/gExuzH8ZK3mCrBODy5i6kVHGu61gL43wsTLrsw4bILEy477yachMQAxJ/egAcBcc0rcFocT6GTR3/A/ZJ3nlfgJHBiEzgQP3gBHvOpbKbLqQmMjut5D9BBQIxqwLuN7lr2juPTIriawHFz+AD1AG5tPI3Ez78WrW7ULxlJC0lek4lDJKtVk0KhkNHXN22uzocDxBx4AAAAAElFTkSuQmCC" /></button>
                                <button type="submit" formaction="./todolist_list_delete.php">
                                    <img
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC1UlEQVR4nO2ZO2wTQRCGFwJCEAkEJA00UEGgBUTDo6cEt0CFCIiCRNi7x+MoaVAIr+hmHKEECmJRICIhpSAWnrETJBc8QkVDAyL0kSISxXB2nJh4D4ll1wfoPmka+/zP/rdzvt9nIRISEhIS/gcy2eJhifRAQfGE+CepVFZJJKWA5xVypVb0KBOUNzUe5vv5NV62eOxKlneJvw3/4eRGhfRk2cBySaCPCvhQfbcU0tvq68hzCvnGmaC8NpZF9w6Ptcv7tLleXpb3KaAPOhNLZpDnJNC4AlrQvE/h7jRq9g6PtTsz4GHpgAR+E7EYuwW0oIBfy6C436oJPyhvkMifnRvAJkOfLt4srbdmxAM62HITWKuwtzUjl+HlzpaMFDaPWGZgYoewiUS+FcOO9AkXKOTRlc0k0owEyvxRIc1oTIw6MbFo5F7z9vO871dWm2qGn128p6w4QXxXuEIhXdeNgBx6tdVUsyfId2g1gX3hCgV0XjvLQXG3qWYaJ7q0RoLCOeEKmaWU/tulFkFMqMUWnWbRXejMIB3VNc0EfNxUM1ywfpcLR4Qr0kB7I+b5rKmmQu7WGhks7LG7+samg4VOrRGkq8aayNd0mj1BvkO4IpXLtf38e2PJyG1TTQV8R/eVnsrl2oRLJPBXTZR4bKyHPNK8IzRtd9W6xsDvNY1fmOvRuGa0poRrFHC++WKnd8Z6yFMavXG7q9Y1Bs5pGn8x1kOa1sSTEburdpy3YslZLvJWLDmrTpiBbOWtqJylkLuFa6LyVpiZflcrjCEtz1ku8lYsOatOmIFs5a2onJXGiS7hmui8xUM+lrY0Pmj7VVWPRR6KCIydzo1U8xbybMRFaqNmneesOgp50pkRoJJoFR7waVdGPKCTLTNSvSMDP7NvhJ6Gf0+IVnKh//k6BdSvkL5ZMBBq9IWaIi68gdJ2iXTK9OFcOEqXsrwtNgMJCQkJCQk/zsB35gy2CL4XJHAAAAAASUVORK5CYII=" />
                                </button>
                            </div>
                            <?php }?>
                        </form>
                    </div>
                    <form action="./todolist_list_cal.php" method="get">
                        <input type="date" name="list_start_date" style="display: none;">
                        <div class="todo-get-calendar">
                            <div class="nav">
                                <button class="material-icons"> <img
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAxklEQVR4nO2VTQrCMBCFcwh/NnoZF66ajF3UwygYZkKPouIMBS+ip1DQegglhUIrLie40AezfV94eZMY85e2bOAZkFwB+QxhP1U1L0oeOuI7kDzjOOK1KsAhH1rzOIsgczVziwJd8whTM8/8dgAotw7gYcvdWA0AJNw/vRTJogHiY7rWoNS5r0ZqAEfse60hWaqZNwDkTVJAExFKnSyiqKSX3Oq9pupRZakX7XtRBcXH7lOrAGVlknw4KBcgPuW+mqgDzM/pBWGTysH2H670AAAAAElFTkSuQmCC"></button>
                                <p>2024년 4월</p>
                                <button class="material-icons"><img
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAuUlEQVR4nO2UQQrCQAxF5xBWV3oYBVftpILjaZSSKT2KSn8QvIieQkH0EEpBoXWdLIo+yDYP/vyJc380SAtMiHGmKFdimTltPGNNUZ7NeJZ7qJCoCvJS5h/Be+C08Yy6LfEsQVWQFtsBsdxakkdW7UaqkoyFulHh6LShKDCNKlRImiaZtsqzhK9WbfojCNYRkeUj51FW3eWoTT/aojgM7aKJWPbn2Hnrc03lfkwRJ2K5ZCWmqsvdz/MCS6HK05bgwhsAAAAASUVORK5CYII=">
                                    </span>
                            </div>
                            </header>
                            <div class="calendar">
                                <div class="calendar-detail">
                                    <ul class="weeks">
                                        <li>일</li>
                                        <li>월</li>
                                        <li>화</li>
                                        <li>수</li>
                                        <li>목</li>
                                        <li>금</li>
                                        <li>토</li>
                                    </ul>
                                    <ul class="days">
                                        <li class="inactive">27</li>
                                        <li class="inactive">28</li>
                                        <li class="inactive">29</li>
                                        <li class="inactive">30</li>
                                        <li>1</li>
                                        <li>2</li>
                                        <li>3</li>
                                        <li>4</li>
                                        <li>5</li>
                                        <li>6</li>
                                        <li>7</li>
                                        <li>8</li>
                                        <li>9</li>
                                        <li>10</li>
                                        <li>11</li>
                                        <li>12</li>
                                        <li>13</li>
                                        <li>14</li>
                                        <li>15</li>
                                        <li>16</li>
                                        <li>17</li>
                                        <li>18</li>
                                        <li>19</li>
                                        <li>20</li>
                                        <li>21</li>
                                        <li>22</li>
                                        <li>23</li>
                                        <li>24</li>
                                        <li>25</li>
                                        <li>26</li>
                                        <li>27</li>
                                        <li>28</li>
                                        <li>29</li>
                                        <li>30</li>
                                        <li>31</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form action="./todolist.html" method="post">
                        <div class="shopping-list">
                            <div class="shopping-list-title">쇼핑리스트</div>
                            <input type="text" name="" id=""
                                style="width: 170px; height: 3px; border:none; border-bottom: 3px solid #799bc4; border-radius: 0; background-color: transparent;  margin: 5px 0;">
                            <input type="text" name="" id=""
                                style="width: 170px; height: 3px; border:none; border-bottom: 3px solid #799bc4; border-radius: 0; background-color: transparent; margin: 5px 0;">
                            <input type="text" name="" id=""
                                style="width: 170px; height: 3px; border:none; border-bottom: 3px solid #799bc4; border-radius: 0; background-color: transparent; margin: 5px 0;">
                            <input type="text" name="" id=""
                                style="width: 170px; height: 3px; border:none; border-bottom: 3px solid #799bc4; border-radius: 0; background-color: transparent; margin: 5px 0;">
                    </form>
                </div>
            </div>

        </div>
    </main>
</body>

</html>