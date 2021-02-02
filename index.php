<?php
session_start();
if(!isset($_SESSION['server_name'])||!isset($_SESSION['encr_username'])||!isset($_SESSION['encr_password'])) 
{
    setcookie ('login_error','Нет данных о базе данных',['SameSite' => 'Strict']);
    header('Location: login.php');
    exit();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Новости</title>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" crossorigin="anonymous"/>

        <!-- Bootstrap CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" crossorigin="anonymous"/>
        <link rel="stylesheet" href="css/style.css"/>
    </head>

    <body class=" text-white" data-spy="scroll" data-target=".navbar" data-offset="50">
    	<?php
        include ('components/header.html');
    	?>
    	
    	<div class="d-flex m-4">
            <abbr title="Загрузить новые статьи" id='sync'>
                <button onclick="get_new_articles();" class='btn btn-success'>
                    <i class="fas fa-download"></i>
                </button>
            </abbr>
            
            <details id="history" class="container panel panel-info bg-purple-dark text-white text-left m-2">
                <summary class="panel-heading text-center">
                    <span class="panel-title">Последние действия</span>
                </summary>
                <div id="history_html">
                    
                </div>
            </details>
    	</div>

    	<div id="articles" class="container">


            <?php 

            include('components/sort.html') ?>

        	<div class="container panel panel-info bg-purple-dark text-white text-center m-2" open>
        		<div class="panel-heading">
                	<span class="panel-title">Последние Статьи</span>
                </div>
                <div id="articles_html">
	               
            	</div>
            	<div id="page_list_html">

            	</div>
        	</details>
        </div>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/scripts.js"></script>
    </body>
</html> 
