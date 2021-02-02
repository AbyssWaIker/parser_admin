<?php


require('session_variables.php');
require_once('sql.php');
require_once('article.php');

use db_connection as connect;
use article as article;

$is_orderby_desc = $_POST['is_orderby_desc']; 
$current_page = $_POST['current_page']; 
$page_LIMIT = $_POST['page_LIMIT'];

$search='*';
if(isset($_POST['search']))
	$search=$_POST['search'];

$db = new connect\db($server,$user,$pass);

$articles = $db->select_articles($current_page, $page_LIMIT, $is_orderby_desc,$search);

for ($i=0; $i < count($articles); $i++) 
{
	$article = new article\Article(
									$articles[$i]['article_id'],
									$articles[$i]["title"],
									$articles[$i]["Publication_Date"],
									$articles[$i]["Full_Text"],
									$articles[$i]["img_path"]
								);
    $article->display();
}

$db->dissconnect();

?>
