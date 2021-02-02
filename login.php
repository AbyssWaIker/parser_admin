<!DOCTYPE html>
<html>
<head>
	<title> Login into the database</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" crossorigin="anonymous"/>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" crossorigin="anonymous"/>

	<link rel="stylesheet" a href="css\login-style.css">

</head>
<body>
	<div class="container">
		<img src="svg/database.svg"/>
		<form action="php/start_session.php" method="post">
			<h1 class='text-dark'>Войти в базу данных</h1>


			<div class="form-input">
				<input type="text" name="e-mail" placeholder="e-mail(необязательно)"/>	
			</div>


			<div class="form-input">
				<input type="text" required name="server_name" value="localhost"/>	
			</div>

			<div class="form-input user">
				<input type="text" required name="username" placeholder="логин"/>	
			</div>

			<div class="form-input user">
				<input type="password" required name="password" placeholder="пароль"/>
			</div>
			
			<input type="submit" value="Войти" class="btn-login"/>
		</form>
	</div>
	
    <?php

    if(isset($_COOKIE['login_error']))
    {
    	//мне не хочеться импортировать весь файл script.js, когда в нем нужна только одна функция
        echo "<script>
				function notify(message) 
				{
				  if (!('Notification' in window)) 
				  {
				    alert(message);
				    return;
				  }

				  if (Notification.permission === 'default') 
				  {
				    Notification.requestPermission();
				  }

				  if (Notification.permission === 'granted') 
				  {
				    var notification = new Notification(message);
				  }
				}
			   </script>";
        $message = trim(str_replace(PHP_EOL, '', $_COOKIE['login_error']));//чертовы \n не дают просто сделать $message = $_COOKIE['login_error']
        echo "<script>notify('$message')</script>\n";
        setcookie ('login_error', '', time() - 3600);
    }
    ?>
</body>

</html>
