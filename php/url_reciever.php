<?php

require('session_variables.php');
require_once('parser.php');
require_once('sql.php');


use db_connection as connect;
use parser as parse;


// //чисто для дебага
// function console_log( $data )
// {
//   echo '<script>';
//   echo 'console.log('. json_encode( $data ) .')';
//   echo '</script>';
// }



$parser = new parse\Parser();

$domain_name = 'https://dailytargum.com';

$links = $parser->get_links($domain_name.'/section/news');

$db = new connect\db($server,$user,$pass);
$existing_links = $db->select_url();
$db->dissconnect();

$new_links=array_diff($links,$existing_links);

echo json_encode($new_links);
// console_log($existing_links);
// console_log($links);
// console_log($new_links);
?>
