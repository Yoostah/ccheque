<?php 
//print_r($_POST);


if (isset($_POST['updFer'])){
	foreach ($_POST['id'] as $key => $id) {
    	$nome   = $_POST['nome'][$key];
    	$data = $_POST['data'][$key];
    	if ($_POST['data_fixa'][$key] == 1){
			$data_fixa = 1;
    	}else{
			$data_fixa = 0;
    	}
    
    	//echo('ID: '.$id.' NOME: '.$nome.' DATA: '.$data.'<BR>' );
    	$pdo = _conectaBD();
		$sql = $pdo->prepare("INSERT INTO feriados (nome,data,data_fixa,usu_id) VALUES ('".$nome."','".$data."', '".$data_fixa."', '".$session_usu_id."')");
		$result = '';
		try {
			$sql->execute();
		} catch (Exception $e) {
			$result = explode(':', $e->getMessage());
		}
			
	}
	_JS_Alerta("Feriados atualizados!");
	header('Location:index.php');
}


?>

<div class="container formulario" style="margin-bottom: 30px;">
	<form method="POST" action="main.php?func=updFeriados">
		<h2>Feriados com datas Variáveis:</h2>
		<?php foreach ($fer as $feri): ?>
			<div class="form-group col-md-6">
				<input type="hidden" name="id[]" value="<?php echo $feri['id'] ?>">
				<label for="nome">Nome:</label>
		        <input type="text" class="form-control" name="nome[]" value="<?php echo $feri['nome'] ?>">
			</div>
			<div class="form-group col-md-6">
				<label for="data">Data:</label>
		        <input id ="data" type="date" class="form-control" name="data[]" placeholder="Informe data Atualizada" required>
			</div>
			<input type="hidden" id="data_fixa" name="data_fixa[]">
		<?php endforeach; ?>
		<h2>Feriados Fixos (ATUALIZADOS):</h2>
		<?php foreach ($fer_fixo as $feri): ?>
			<div class="form-group col-md-6">
				<label for="nome">Nome:</label>
				<input type="hidden" name="id[]" value="<?php echo $feri['id'] ?>">

		        <input type="text" class="form-control" name="nome[]" value="<?php echo $feri['nome'] ?>">
			</div>
			<div class="form-group col-md-6">
				<label for="data">Data:</label>
		        <input id ="data" type="date" class="form-control" name="data[]" value="<?php echo date('Y-m-d', strtotime($feri['data'] . ' +1 year')); ?>">
			</div>
			<input type="hidden" id="data_fixa" name="data_fixa[]" value="1">
		<?php endforeach; ?>
		<div class="btn-toolbar pull-right">
			<button type="submit" name="updFer" style="width: 100%;" class="btn btn-info pull-right">Atualizar Feriados para <?php echo date('Y', strtotime($feri['data'] . ' +1 year')); ?></button>
		</div>		
	</form>
	
</div>	