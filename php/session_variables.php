<?php
session_start();
$server = $_SESSION['server_name'];
$user = ($_SESSION['encr_username']^session_id());
$pass = ($_SESSION['encr_password']^session_id());

?>