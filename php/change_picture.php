<?php
namespace picture;

require('session_variables.php');
require("sql.php");
use db_connection as connect;



$date = '<b>'.date("M d: h-i-s",time())."</b>\t";

if(!isset($_FILES['file']["tmp_name"])||!isset($_POST['img_path']))
{
    // print_r($_POST);
    // print_r($_FILES);
    die($date.'Информация об изображение отсутствует');
}
$title = "\t<b>« ".$_POST['title']."</b>\t»";

$path_of_new_img = $_FILES['file']['tmp_name'];

$file = file_get_contents($path_of_new_img);
if(!$file) 
{
    die($date.'Не удалось записать изображение к статье '.$title);
}
$img_path = '.'.$_POST['img_path'];

if($_POST['need_change']=='true')
{
	$split_img_path = explode('.',$img_path);

	$new_img_path = $split_img_path[0];
	for ($i=1; $i < sizeof($split_img_path)-1; $i++) 
	{ 
		$new_img_path .= '.'.$split_img_path[$i];
	}

	$new_img_path .= $_POST['new_path_ext'];

	rename($img_path, $new_img_path);

	$img_path = $new_img_path;

	$db = new connect\db($server,$user,$pass);
	$db->update_article('img_path',$img_path,$_POST['id']);
	$db->dissconnect();
}



$picture_handle = fopen($img_path, 'w');
fwrite($picture_handle, $file);;
fclose($picture_handle);

echo $date.'Изображение статьи'.$title." успешно обновленно\r\n <br> \r\n";


?>
