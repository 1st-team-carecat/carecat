<?php

define("MARIADB_HOST", "112.222.157.156");
define("MARIADB_USER", "team1");
define("MARIADB_PASSWORD", "team1");
define("MARIADB_NAME", "001_create_table");
define("MARIADB_CHARSET", "utf8mb4");
define("MARIADB_DSN", "mysql:host=".MARIADB_HOST.";dbname=".MARIADB_NAME.";charset=".MARIADB_CHARSET);

define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/");
define("FILE_MYPAGE", ROOT."");
define("FILE_JOIN", ROOT."");
define("FILE_TODOLIST", ROOT."");
define("FILE_CALENDAR", ROOT."");
define("FILE_LIB_DB", ROOT."");


define("REQUEST_METHOD", strtoupper($_SERVER["REQUEST_METHOD"]));

?>