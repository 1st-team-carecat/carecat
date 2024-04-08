<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/todolist_config.php"); // 설정 파일 호출
require_once(FILE_LIB_DB); // DB관련 라이브러리
?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./css/todolist.css">
</head>

<body>
  <header>
    <div class="main-title">
      <img src="./css/content-title.png" class="title-img">
    </div>
    <div class="header-profile-name">로미</div>
    <a href="./join.html">
      <img class="header-profile-img" src="./img/11zon_cropped__2_-removebg-preview.png" />
    </a>
  </header>
  <main class="main-box">
    <div class="box">
      <div class="menu-content">
        <div class="menu">
          <a href="./todolist_mypage.php">내정보</a>
        </div>
        <div class="menu">
          <a href="./todolist_list.php">할일</a>
        </div>
        <div class="menu page-point">
          <a href="#">캘린더</a>
        </div>
      </div>
      <div class="calendar-dox">
        <div class="calendar">
          <ul class="weeks">
            <li>Sun</li>
            <li>Mon</li>
            <li>Tue</li>
            <li>Wed</li>
            <li>Thu</li>
            <li>Fri</li>
            <li>Sat</li>
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
  </main>
</body>

</html>