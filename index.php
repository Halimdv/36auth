<?php
require 'config.php';


$valid = true;

if(!isset($_SESSION['id']) && !isset($_SESSION['login'])){
		if(isset($_COOKIE['remember'])){
			$token = $_COOKIE['remember'];
			$query = $db->query("SELECT * FROM user WHERE token = '$token'"); 
			$user = $query->fetch();
			$_SESSION['id'] = $user['id'];
			$_SESSION['login'] = $user['login'];
		}
}

if(!isset($_SESSION['id'])){
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
	} elseif(isset($_POST['password']) && isset($_POST['cf-password']) && $_POST['password'] != $_POST['cf-password']) {
		$valid = false;
		echo "La confirmation est différente du mot de passe";
	}?>
	<br>
	<button name="submit">S'inscrire</button>
</form>

<?php
	if(isset($_POST['submit']) && $valid){
		$options = array('cost' => 10);
		$login = trim($_POST['login']);
		$password = password_hash( trim($_POST['password']), PASSWORD_DEFAULT, $options);
		$email = trim($_POST['email']);
		$date = time();
		$query = $db->prepare("SELECT * FROM user WHERE login = :login");
		$query->bindValue(":login", $login, PDO::PARAM_STR);
		$query->execute();
		if(!$query->rowCount()){
			$query = $db->prepare("INSERT INTO user(login, password,  email, date)
			 VALUES(:login, :password, :email, :date)");
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			$query->bindValue(':password', $password, PDO::PARAM_STR);
			$query->bindValue(':email', $email, PDO::PARAM_STR);
			$query->bindValue(':date', $date, PDO::PARAM_STR);
			if($query->execute()){
				$_SESSION['id'] = $db->lastInsertId();
				$_SESSION['login'] = $login;
				header('location: '.$url);
			}
		} else {
			echo "L'utilisateur existe déjà";
		}
	}

?>

<form method="POST">
	<label for="">Login</label>
	<input type="text" name="login"><br>
	<label for="">Mot de passe</label>
	<input type="text" name="password"><br>
	<label for="">Se rappeler de moi</label>
	<input type="checkbox" name="remember"><br>
	<button name="loginValid">Se connecter</button>
</form>

<?php
	if(isset($_POST['loginValid'])){

		$login = $_POST['login'];
		$password = $_POST['password'];
		$options = array('cost' => 10);
		if(!empty($login) && !empty($password)){
			$query = $db->prepare("SELECT * FROM user WHERE login = :login");
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			if($query->rowCount()){
				$user = $query->fetch();
				$valid = password_verify($password, $user['password']);
				if($valid){
					if(isset($_POST['remember'])){
						$token = sha1(md5(uniqid().$_SERVER['REMOTE_ADDR']));
						setcookie('remember', $token , time()+60*60*24);
						$db->query("UPDATE user SET token = '$token' WHERE id = ".$user['id']);
					}
					$_SESSION['id'] = $user['id'];
					$_SESSION['login'] = $user['login'];
					header('location: '.$url);
				} else{
					echo "Le mot de passe n'est pas bon";
				}
			} else {
				echo "L'utilisateur n'existe pas.";
			}
		}
	}
} else {
	var_dump($_SESSION);
	echo "Bonjour ".$_SESSION['login']; ?>
	<a href="logout.php">Se déconnecter</a>
<?php }
?>