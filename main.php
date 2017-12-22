<?php session_start();

if (!isset($_SESSION['login'])) {
	header('Location:index.php');
}


require "funcoes.php";




if (isset($_GET['func']) && empty($_GET['func']) == false) {
	$func = $_GET['func'];
	if (isset($_GET['op']) && empty($_GET['op']) == false) {
		$op = $_GET['op'];
	}
		if (isset($_GET['id']) && empty($_GET['id']) == false) {
			$id = $_GET['id'];
		}
	
	

}else{
	$func = 'bemVindo';
}

?>


<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="_assets/bootstrap.min.css"> 
	<link rel="stylesheet" type="text/css" href="_css/_css.css">
	<script type="text/javascript" src="_assets/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="_assets/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<nav class="navbar navbar-inverse navbar-fixed-top">
		  <div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="main.php?func=bemVindo"><img src="_imagens/logo.png" alt="Dispute Bills"></a>
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <li class="dropdown">
					<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cadastro<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="main.php?func=cadCheque">Cheque</a></li>
						<li><a href="main.php?func=cadGCheque">Grupo de Cheque</a></li>
						<li><a href="#">Usuários</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="main.php?func=cadFeriado">Feriados</a></li>
						<li><a href="main.php?func=cadBanco">Bancos</a></li>
					</ul>	 
				</li>
				<li class="dropdown">
					<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Consulta<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="main.php?func=consCheque">Cheque</a></li>
						<li><a href="#">Usuários</a></li>
						<li><a href="main.php?func=consFeriado">Feriados</a></li>
						<li><a href="main.php?func=consBanco">Bancos</a></li>
					</ul>	 
				</li>
		      	<li><a href="#">Sobre</a></li>
		      </ul>
		      <ul class="nav navbar-nav navbar-right">
		        <li><p class="msgBemVindo">Bem vindo <?php echo $_SESSION['nome']; ?></p></li>
		        <li><a href="sair.php">Sair</a></li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
		<div class="conteudo">
			<?php
				$session_usu_id = addslashes($_SESSION['id']);
				include $func.".php";
			?>
		</div>
	</div>
</body>
</html>