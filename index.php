<?php session_start();
include 'funcoes.php';

if (isset($_SESSION['login'])) {
	header('Location:main.php?func=bemVindo');
}else{
	if (isset($_POST['login']) && (!empty($_POST['login']))) {
	$login = addslashes($_POST['login']);
	$senha = addslashes($_POST['senha']);


	$pdo = _conectaBD();


	$sql = $pdo->prepare('SELECT * FROM usuarios where login = :login and senha = :senha');
	$sql->bindValue(':login', $login);
	$sql->bindValue(':senha', $senha);
	try {
			$sql->execute();
	} catch (Exception $e) {
			$result = explode(':', $e->getMessage());
	}
	if($sql->rowCount() > 0){
			$usuario = $sql->fetch();

			$_SESSION['login'] = $usuario['login'];
			$_SESSION['id'] = $usuario['id'];
			$_SESSION['nome'] = $usuario['nome'];
			header('Location:main.php?func=bemVindo');
		}else{
			_JS_Alerta("Usuário não cadastrado ou senha inválida");
		}					
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>CCHEQUE</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="_assets/bootstrap.min.css"> 
	<link rel="stylesheet" type="text/css" href="_css/_css.css">
	<script type="text/javascript" src="_assets/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="_assets/bootstrap.min.js"></script>
</head>
<body class="body_login">
	<div class="container">
		<div class="login">
			<img class="img_login" src="_imagens/logo_login.png">
			<div class="box">
				<form method="POST">
					<div class="form-group ">
						<label for="login">Login:</label>
						<input type="text" name="login" class="form-input" required>
					</div>
					<div class="form-group ">
						<label for="senha">Senha:</label>
						<input type="password" name="senha" class="form-input" required>
					</div>	
					<button type="submit" id="btnLogar">Entrar</button>
				</form>
			</div><!--box-->	
		</div><!--login-->
	</div><!--container-->
</body>
</html>