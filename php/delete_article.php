<?php

require('session_variables.php');
require("sql.php");
use db_connection as connect;

echo '<b>'.date("M d: h:i:s",time())."</b>\t ";
if(!isset($_POST['id'])||!isset($_POST['src']))
{
    print_r($_POST);
    die("Ошибкаю Отсутствуют данные для удаления");
}

$id = $_POST['id'];

$db = new connect\db($server,$user,$pass);
$db->delete_article($id);
$db->dissconnect();

$src = $_POST['src'];
unlink('..'.$src);

$title=$_POST['title'];
echo 'Удалена статья <b>«'.$title.'»</b>';

?>