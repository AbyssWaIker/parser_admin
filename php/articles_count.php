<?php

require('session_variables.php');
require('sql.php');

use db_connection as connect;

$db = new connect\db($server,$user,$pass);
$articles = $db->select_articles_count();
$db->dissconnect();

echo  $articles;
?>
