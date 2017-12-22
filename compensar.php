<?php
require "funcoes.php";

if (isset($_GET['id']) && empty($_GET['id']) == false){
	$pdo = _conectaBD();
	$sql = $pdo->prepare("UPDATE cheque SET compensou = 1 WHERE id = :id");
	$sql->bindValue(":id" , $_GET['id']);
	$sql->execute();

	header("Location:main.php?func=consCheque");

}


?>