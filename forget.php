<?php

require 'config.php';

if(isset($_GET['forgetToken'])){ 
	$forget = $_GET['forgetToken'];
	$checkUser = $db->prepare("SELECT * FROM user WHERE forget = :forget");
	$checkUser->bindValue(":forget", $forget, PDO::PARAM_STR);
	$checkUser->execute();
	if($checkUser->rowCount()){ ?> 
	<form method="POST">
		<div>
			<label for="password">Nouveau mot de passe</label>
			<input type="text" name="password">
		</div>
		<div>
			<label for="cf-password">Confirmer le nouveau mot de passe</label>
			<input type="text" name="cf-password">
		</div>
		<button name="changePassword">Valider</button>
	</form>
	<?php 
		if(isset($_POST['changePassword'])){
			$user = $checkUser->fetch();
			$query = $db->prepare("UPDATE user SET password = :password WHERE id= :id");
			$password = password_hash( trim($_POST['password']), PASSWORD_BCRYPT);
			$query->bindValue(':password', $password, PDO::PARAM_STR);
			$query->bindValue(':id', $user['id'], PDO::PARAM_INT);
			if($query->execute()){

				echo "Votre mot de passe a été mis à jour";
				

			}

		}

	} else {
?>


<form method="POST">
	<label for="login">Login:</label>
	<input type="text" name="login">
	<button name="forget">Valider</button>
</form>

<?php }
} else { ?>


<form method="POST">
	<label for="login">Login:</label>
	<input type="text" name="login">
	<button name="forget">Valider</button>
</form>

<?php }
if(isset($_POST['forget'])){ 
	$login = $_POST['login'];
	$checkUser = $db->prepare("SELECT * FROM user WHERE login = :login");
	$checkUser->bindValue(":login", $login, PDO::PARAM_STR);
	$checkUser->execute();
	if($checkUser->rowCount()){
		$user = $checkUser->fetch();
		$forget= sha1(md5(uniqid().$_SERVER['REMOTE_ADDR']));
		$db->query("UPDATE user SET forget = '$forget' WHERE id = ".$user['id']);
		echo "Bonjour ".$login.", vous pouvez redéfinir votre mot de passe sur 
		<a href='".$url."?forgetToken=".$forget."'>".$url."?forgetToken=".$forget."</a>";

		$mail = new PHPMailer();
		$mail->setFrom('no-reply@localhost.com' , 'Admin Localhost');
		$mail->addAdress($user['email']);
		$mail->Subject = 'Oubli de mot de passe';
		$mail->Body = "Bonjour ".$login.", vous pouvez redéfinir votre mot de passe sur 
		<a href='".$url."?forgetToken=".$forget."'>".$url."?forgetToken=".$forget."</a>";
		var_dump($mail);
		if($mail->send()){
			echo "Vous allez recevoir un lien pour redéfinir votre mot de passe.";
		} else {
			echo "Problème";
		}
	} else {
		echo "L'utilisateur n'existe pas";
	}
}
