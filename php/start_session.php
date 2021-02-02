<?php
session_start();
if(!isset($_POST['server_name'])||!isset($_POST['username'])||!isset($_POST['password']))
{
    setcookie('login_error',"Не удалось войти. \n Данные не получены",['SameSite' => 'Strict']);
    header("Location: ../login.php");;
    exit();
}
$_SESSION['server_name'] = $_POST['server_name'];
$_SESSION['encr_username'] = $_POST['username'] ^ session_id();
$_SESSION['encr_password'] = $_POST['password'] ^ session_id();

if(isset($_POST['e-mail']) )
{
	$_SESSION['e-mail'] = $_POST['e-mail'];
}
header("Location: ../index.php");
exit();

?>
