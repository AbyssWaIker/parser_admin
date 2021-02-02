<?php

require('session_variables.php');
require("sql.php");
use db_connection as connect;


echo '<b>'.date("M d: h:i:s",time())."</b>\t";
if(!isset($_POST['id']))
{
    print_r($_POST);
    die('Данные для изменения статьи не получены');
}


$column_name = $_POST['column_name'];
$value = trim($_POST['value']);
$id = $_POST['id'];


$db = new connect\db($server,$user,$pass);
$db->update_article($column_name,$value,$id);
$db->dissconnect();


$title=$_POST['title'];
$changes='';
switch ($column_name) {
	case 'title':
		$changes = 'Статья <b>«'.$title.'»</b> была переименована';
		break;

	case 'Full_Text':
		$changes = '</b> Статья <b>«'.$title.'»</b> была изменена';
		break;

	case 'Publication_Date':
		$changes = '</b> Дата статьи <b>«'.$title.'»</b> была изменена';
		break;
	
	default:

		break;
}

echo $changes.' успешно';

?>