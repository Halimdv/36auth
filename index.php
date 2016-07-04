<?php
require 'config.php';

$valid = true;
?>

<form method="POST">
	<label for="login">Login: </label>
	<input type="text" name="login" placeholder="Pseudo">
	<?php if(isset($_POST['submit']) && empty($_POST['login'])){
    	$valid = false;
		echo "Le champ login est vide";
	} ?>
	<br>
	<label for="email">Email: </label>
	<input type="text" name="email" placeholder="Email">
	<?php if(isset($_POST['submit']) && empty($_POST['email'])){ 
    	$valid = false;
		echo "Le champ email est vide";
	} elseif(isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		echo "Un email valide doit être écrit";
	}
	?>
	<br>
	<label for="password">Mot de passe: </label>
	<input type="text" name="password" placeholder="Mot de passe">
	<?php if(isset($_POST['submit']) && empty($_POST['password'])){ 
    	$valid = false;
		echo "Le champ mot de passe est vide";
	} ?>
	<br>
	<label for="cf-password">Confirmer le mot de passe: </label>
	<input type="text" name="cf-password" placeholder="Mot de passe">
	<?php if(isset($_POST['submit']) && empty($_POST['cf-password'])){ 
    	$valid = false;
		echo "Veuillez confirmer le mot de passe";
	} elseif(isset($_POST['password']) && $_POST['password'] != $_POST['cf-password']) {
		$valid = false;
		echo "La confirmation est différente du mot de passe";
	}?>
	<br>
	<button name="submit">S'inscrire</button>
</form>

<?php
if(isset($_POST['submit']) && $valid){
	$options = array('cost' => 10);
	$password = password_hash( $_POST['password'], PASSWORD_DEFAULT, $options);
	echo "ok";
	$query = $db->prepare("INSERT INTO user(login, password,  email, date)
	 VALUES(:login, :password, :email, :date)");
	$query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
	$query->bindValue(':password', $password, PDO::PARAM_STR);
	$query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
	$query->bindValue(':date', time(), PDO::PARAM_STR);
	$query->execute();
}

?>