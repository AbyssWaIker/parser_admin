<?php

require('session_variables.php');
require_once('sql.php');
require_once('parser.php');
require('mail.php');


use db_connection as connect;
use parser as parse;

$title = ' ';
$img = '';
$text ='';
//Функция сохраняющая информацию.
function save_article($url, $db,$domain_name='https://dailytargum.com')
{

  $data = file_get_contents($domain_name.$url);

  $parser = new parse\Parser();
  GLOBAL $title;
  $title = $parser->get_article_title($data);
  $date =  $parser->get_article_date($data);
  GLOBAL $text;
  $text = $parser->get_article_text($data);
  GLOBAL $img;
  $img = $parser->get_article_img($data);

  if($title==="")  return;

  $success = $db->insert_articles($url, $title, $date, $text, $img);

  $result = $success ? 'Успешно сохранена статья' : 'Не удалось сохранить статью';



  return $result;

}
echo '<b>'.date("M d: h:i:s",time())."</b>\t";
if(!isset($_POST['url'])) die('Ошибка! Нет ссылки');


$db = new \db_connection\db($server,$user,$pass);

$result = save_article($_POST['url'], $db);

$db->dissconnect();

$message = $result."\t<b>«".$title."»</b> \r\n <br> \r\n ";

echo $message;
if(!session_id())
  session_start();

if(isset($_SESSION['e-mail']) && trim($_SESSION['e-mail'])!='')
{
  $mail = new sender($_SESSION['e-mail'],  $result."\t «".$title."»", $text, realpath('.'.$img));
  $mail->send();
}

?>
