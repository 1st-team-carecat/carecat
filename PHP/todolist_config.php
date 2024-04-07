<?php

define("MARIADB_HOST", "112.222.157.156");
define("MARIADB_USER", "team1");
define("MARIADB_PASSWORD", "team1");
define("MARIADB_NAME", "team1");
define("MARIADB_CHARSET", "utf8mb4");
define("MARIADB_DSN", "mysql:host=".MARIADB_HOST.";port=6412;dbname=;dbname=".MARIADB_NAME.";charset=".MARIADB_CHARSET);

define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/");
define("FILE_HEADER", ROOT."todolist_header.php");
define("FILE_LIB_DB", ROOT."lib/lib_db.php");


define("REQUEST_METHOD", strtoupper($_SERVER["REQUEST_METHOD"]));

?>