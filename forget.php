<?php

require 'config.php';

?>


<form method="POST">
	<label for="login">Login:</label>
	<input type="text" name="login">
	<button name="forget">Valider</button>
</form>

<?php
if(isset($_POST['forget'])){ 
	$login = $_POST['login'];
	$query = $db->prepare("SELECT * FROM user WHERE login = :login");
	$query->bindValue(":login", $login, PDO::PARAM_STR);
	$query->execute();
	if($query->rowCount()){
		$user = $query->fetch();
		$forget= sha1(md5(uniqid().$_SERVER['REMOTE_ADDR']));
		$db->query("UPDATE user SET forget = '$forget' WHERE id = ".$user['id']);
		echo "Bonjour ".$login.", vous pouvez redéfinir votre mot de passe sur 
		<a href='".$url."?forget=".$forget."'>".$url."?forget=".$forget."</a>";
	} else {
		echo "L'utilisateur n'existe pas";
	}
}