<?php

	
	if (isset($_POST['upd']) && $_POST['upd'] == 1){
		
		$pdo = _conectaBD();
		$sql = $pdo->query("SELECT * FROM feriados WHERE YEAR(data) = YEAR(CURDATE()+ INTERVAL 1 YEAR) and usu_id = $session_usu_id");
		if ($sql->rowCount() > 0 ) {
			_JS_Alerta("Os feriados já foram atualizados para o ano atual !");	
		}else{
		$sql = $pdo->query("SELECT * FROM feriados where YEAR(data) = YEAR(CURDATE() - 1) and usu_id = $session_usu_id ORDER BY data");
		$fer_fixo = array();
		$fer = array();
		if ($sql->rowCount() > 0 ) {
			$feriados = $sql->fetchAll();

			foreach ($feriados as $feriado) {
				if ($feriado['data_fixa'] == 1){
					$fer_fixo[] = array('id' => $feriado['id'], 
										'data' => $feriado['data'],
										'nome' => $feriado['nome']);
				}else{
					$fer[] = array('id' => $feriado['id'], 
								  'data' => $feriado['data'],
								  'nome' => $feriado['nome']);
				}
			}
		}


		include 'updFeriados.php';
		exit;			
		}	


	}


	if (isset($_GET['op']) && $_GET['op'] == 'UPD') {
		$id = addslashes($_GET['cod']);
	    if (isset($_POST['nome']) && empty($_POST['nome']) == false && $_GET['op'] == 'UPD') {
	    	
	    		$nome = addslashes($_POST['nome']);
				$data = addslashes($_POST['data']);
				$data_fixa = 0;
				if (isset($_POST['data_fixa'])){
					$data_fixa = 1;
				}
				//Usado para o javascript focar no elemento que deu erro
				$erro = '';
				$result = '';
				try {
				    $pdo = _conectaBD();
					$sql = $pdo->prepare("UPDATE feriados SET nome='".$nome."', data='".$data."', data_fixa='".$data_fixa."' WHERE id='".$id."' and usu_id = $session_usu_id" );
					$sql->execute();
					_JS_Alerta_Redireciona('Feriado atualizado!','main.php?func=consFeriado');
				}catch(PDOException $e){
				    $result = explode(':', $e->getMessage());
				    $_POST['erro'] = true;
					$erro = 'data';
					_JS_Alerta("Erro! Já existe feriado cadastrado para a data informada!\\n[ ".$result[2]." ]");
				}	
		} else {
	        $pdo = _conectaBD();
	        $sql = $pdo->prepare("SELECT * FROM feriados WHERE id = '$id' and usu_id = $session_usu_id");
	        $sql->execute();
	        if ($sql->rowCount() > 0) {
	            
	            $feriado = $sql->fetch();

	            $nome = ($feriado['nome']);
	            $data = ($feriado['data']);
	            $data_fixa = ($feriado['data_fixa']);

	            //$_GET['ok'] = 'OK';

	        }
	    }
	}
	
	if (isset($_POST['nome']) && empty($_POST['nome'] && !isset($_GET['op'])) == false){
			$nome = addslashes($_POST['nome']);
			$data = addslashes($_POST['data']);
			$data_fixa = 0;
			if (isset($_POST['data_fixa'])){
				$data_fixa = 1;
			}
			
			$pdo = _conectaBD();
			$sql = $pdo->prepare("INSERT INTO feriados (nome,data,data_fixa) VALUES ('".$nome."','".$data."', '".$data_fixa."')");
			$result = '';
			try {
				$sql->execute();
			} catch (Exception $e) {
				$result = explode(':', $e->getMessage());
			}
			if ($sql->rowCount() > 0){
				_JS_Alerta("Feriado cadastrado com sucesso!");
			}else{
				$_POST['erro'] = true;
				$erro = 'data';
				_JS_Alerta("Erro! Já existe feriado cadastrado para a data informada!\\n[ ".$result[2]." ]");
			}
	}
		


	//JS Validação formulário
	include '_scripts/s_feriados.php';		
?>
<h1>Cadastro de Feriados</h1>
<h4>Cadastre os Feriados</h4>
<div class="container">
	<div class="formulario">
		<form method="POST" enctype="multipart/form-data">
			<div class="form-group">
				<label for="nome">Nome:</label>
		        <input id ="nome" type="text" class="form-control" name="nome" placeholder="Feriado">
			</div>
			<div class="form-group">
				<label for="data">Data:</label>
		        <input id ="data" type="date" class="form-control" name="data" placeholder="Data">
			</div>
			<div class="form-check">
			    <label class="form-check-label">
			      	<input type="checkbox" id="data_fixa" name="data_fixa" class="form-check-input">
			      	Data Fixa
			    </label>
		    </div>

	    	<div class="btn-toolbar pull-right">
				<button type="submit" id="btnFer" style="width: 100%" class="btn btn-warning pull-right" onClick="return campo_vazio()" >Cadastrar Novo Feriado</button><br><br>
				<button type="submit" id="updFer" style="width: 100%" class="btn btn-info pull-right" onClick="atualizar()" >Atualizar Feriados</button>		

			</div>
			<div style="clear: both"></div>
			<div class="pull-right" style="max-width: 196px">
				<small style="color: red">Não pode existir nenhum feriado cadastrado no ano seguinte!</small>	
			</div>
			<input type="hidden" name="upd" id="upd" value="0">
		</form>
	</div>	
</div>

<?php
if (isset($_POST['erro']) && $_POST['erro'] == true){
	?>
	<script>
		var data_fixa;

    	data_fixa = "<?php echo $data_fixa ?>"
        document.getElementsByName('nome')[0].value = "<?php echo $nome ?>";
        document.getElementsByName('data')[0].value = "<?php echo $data ?>";
        if(data_fixa == 1){
        	document.getElementById("data_fixa").checked = true;
        }
        document.getElementById("<?php echo $erro ?>").focus();
    </script>
    <?php
    unset($_POST['erro']);
}

if (isset($_GET['op']) && $_GET['op'] == 'UPD') {

    ?>
    <script>
    	var data_fixa;

    	data_fixa = "<?php echo $data_fixa ?>"
        document.getElementsByName('nome')[0].value = "<?php echo $nome ?>";
    	document.getElementsByName('data')[0].value = "<?php echo $data ?>";
        if(data_fixa == 1){
        	document.getElementById("data_fixa").checked = true;
        }
        document.getElementById("btnFer").innerHTML = "Atualizar dados Cadastrais";
        document.getElementById("updFer").style.visibility = 'hidden';


    </script>

    <?php

}
 ?>


