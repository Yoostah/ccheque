<?php

$pdo = _conectaBD();

$sql = $pdo->prepare("update cheque set compensado = 1 where data_compensacao < NOW() and compensado is null");
try {
	$sql->execute();
	echo "Atualizado!";
} catch (Exception $e) {
	echo $e->getMessage();
}


?>