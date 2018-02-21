<?php

require_once (ABSPATH . "/scripts/ez_sql_core.php");
require_once (ABSPATH . "/scripts/ez_sql_mysqli.php");
$db = new ezSQL_mysqli(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

require_once (ABSPATH . "/scripts/functions.req.php");
require_once (ABSPATH . "/scripts/classes.req.php");
require_once (ABSPATH . "/scripts/display.req.php");

$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
$hentai_toggle = ($_COOKIE["mangadex_h_toggle"]) ?? 0;
?>