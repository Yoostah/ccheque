<?php
	if (isset($_GET['op']) && $_GET['op'] == 'UPD') {
		$codigo_anterior = addslashes($_GET['cod']);

	    if (isset($_POST['codigo']) && empty($_POST['codigo']) == false && $_GET['op'] == 'UPD') {
	    	if(empty($_FILES['logo']['name'])){
	    		$codigo = addslashes($_POST['codigo']);
				$nome = addslashes($_POST['nome']);
				//Usado para o javascript focar no elemento que deu erro
				$erro = '';
				
					
				$result = '';
				try {
				    $pdo = _conectaBD();
					$sql = $pdo->prepare("UPDATE bancos SET codigo='".$codigo."', nome='".$nome."', logo='_arquivos/_bancos/_logo_".$codigo."' WHERE codigo='".$codigo_anterior."'");
					$sql->execute();
					if ($codigo_anterior != $codigo) {
						rename('_arquivos/_bancos/_logo_'.$codigo_anterior.'.png', '_arquivos/_bancos/_logo_'.$codigo.'.png');
					}
					_JS_Alerta_Redireciona('Banco atualizado!','main.php?func=consBanco&upd=1');
				}catch(PDOException $e){
				    $result = explode(':', $e->getMessage());
				    $_POST['erro'] = true;
					$erro = 'codigo';
					_JS_Alerta("Erro ao atualizar Banco! Verifique os dados e tente novamente.\\n[ ".$result[2]." ]");
			    }
				
	    	}else{	    		
	    		$logo = $_FILES['logo'];
				$tamanho = getimagesize($logo['tmp_name']);
				$codigo = addslashes($_POST['codigo']);
				$nome = addslashes($_POST['nome']);
				$erro = '';
		        if($logo['type'] == 'image/png'){
					if ($tamanho[0] > 30 || $tamanho[1] > 30){
						$_POST['erro'] = true;
						$erro = 'file';
						_JS_Alerta("Logo maior que o tamanho permitido!");
					}else{
						try {
						    $pdo = _conectaBD();
							$sql = $pdo->prepare("UPDATE bancos SET codigo='".$codigo."', nome='".$nome."', logo='_arquivos/_bancos/_logo_".$codigo."' WHERE codigo='".$codigo_anterior."'");
							$sql->execute();
							unlink('_arquivos/_bancos/_logo_'.$codigo_anterior.'.png');
							move_uploaded_file($logo['tmp_name'], '_arquivos/_bancos/_logo_'.$codigo.'.png' );
							_JS_Alerta_Redireciona('Banco atualizado!','main.php?func=consBanco');
						}catch(PDOException $e){
						    $result = explode(':', $e->getMessage());
							$_POST['erro'] = true;
							$erro = 'codigo';
							_JS_Alerta("Erro ao atualizar Banco! Verifique os dados e tente novamente.\\n[ ".$result[2]." ]");
					    }					
									
					}
				}else{
					_JS_Alerta('ELSE $logo["type"] == "image/png"');
					$codigo = addslashes($_POST['codigo']);
					$nome = addslashes($_POST['nome']);
					$_POST['erro'] = true;
					$erro = 'file';
					_JS_Alerta("Logo em formato não suportado!");
				}		
	    	}
	        
		} else {
	        $pdo = _conectaBD();
	        $sql = $pdo->prepare("SELECT * FROM bancos WHERE codigo = '$codigo_anterior'");
	        $sql->execute();
	        if ($sql->rowCount() > 0) {
	            
	            $banco = $sql->fetch();

	            $cod = ($banco['codigo']);
	            $nome = ($banco['nome']);

	        }
	    }
	}
	
	if (isset($_POST['codigo']) && empty($_POST['codigo'] && !isset($_GET['op'])) == false){
			$logo = $_FILES['logo'];
			$tamanho = getimagesize($logo['tmp_name']);
			$codigo = addslashes($_POST['codigo']);
			$nome = addslashes($_POST['nome']);
			$erro = '';

			if($logo['type'] == 'image/png'){
				if ($tamanho[0] > 30 || $tamanho[1] > 30){
					$_POST['erro'] = true;
					$erro = 'file';
					_JS_Alerta("Logo maior que o tamanho permitido!");
				}else{
					$pdo = _conectaBD();
					$sql = $pdo->prepare("INSERT INTO bancos (codigo,nome,logo) VALUES ('".$codigo."','".$nome."', '_arquivos/_bancos/_logo_".$codigo."')");
					$result = '';
					try {
						$sql->execute();
					} catch (Exception $e) {
						$result = explode(':', $e->getMessage());
					}
					if ($sql->rowCount() > 0){
						move_uploaded_file($logo['tmp_name'], '_arquivos/_bancos/_logo_'.$codigo.'.png' );
						_JS_Alerta("Banco cadastrado com sucesso!");
					}else{
						$_POST['erro'] = true;
						$erro = 'codigo';
						_JS_Alerta("Erro ao cadastrar Banco! Verifique os dados e tente novamente.\\n[ ".$result[2]." ]");
					}
				}
			}else{
				$codigo = addslashes($_POST['codigo']);
				$nome = addslashes($_POST['nome']);
				$_POST['erro'] = true;
				$erro = 'file';
				_JS_Alerta("Logo em formato não suportado!");
			}	
	}

	//JS Validação formulário
	include '_scripts/s_banco.php';		
?>
<h1>Cadastro de Bancos</h1>
<h4>Cadastre os Bancos</h4>
<div class="container">
	<div class="formulario">
		<form method="POST" enctype="multipart/form-data">
			<div class="form-group">
					<label for="codigo">Código:</label>
			        <input id ="codigo" type="text" class="form-control" name="codigo" placeholder="Código Banco">
			</div>
			<div class="form-group">
					<label for="nome">Nome:</label>
			        <input id ="nome" type="text" class="form-control" name="nome" placeholder="Nome Banco">
			</div>
			<div class="form-group">        	
			 <label class="custom-file">
			 	<label for="nome">Logomarca:</label>
				  <input type="file" name="logo" id="file" class="custom-file-input" accept=".png">
				  <span class="custom-file-control"></span>
				  <small id="fileHelp" class="form-text text-muted" style="color: red"><input type='hidden' id='logoOK' value='1'>A logomarca deverá ser um arquivo no formato [.png] e com tamanho 30 x 30px</small>
				</label>
			</div>	
			<div class="btn-toolbar pull-right">
				<button type="submit" style="width: 100%;" id="btnCad" class="btn btn-warning pull-right" onClick="return campo_vazio()" >Cadastrar Novo Banco</button>
			</div>	
		</form>
	</div>	
</div>

<?php
if (isset($_POST['erro']) && $_POST['erro'] == true){
	?>
	<script>
        document.getElementsByName('codigo')[0].value = "<?php echo $codigo ?>";
        document.getElementsByName('nome')[0].value = "<?php echo $nome ?>";
        document.getElementById("<?php echo $erro ?>").focus();
    </script>
    <?php
    unset($_POST['erro']);
}

if (isset($_GET['op']) && $_GET['op'] == 'UPD') {

    ?>
    <script>
    	function trocarLogo(){
    		document.getElementById("file").disabled = false;
    		document.getElementById("fileHelp").innerHTML = "A logomarca deverá ser um arquivo no formato [.png] e com tamanho 30 x 30px";
    		document.getElementById("logoOK").value = 0;
    	}

        document.getElementsByName('codigo')[0].value = "<?php echo $cod ?>";
        document.getElementsByName('nome')[0].value = "<?php echo $nome ?>";
        document.getElementById("file").disabled = true;
        document.getElementById("fileHelp").innerHTML = "Caso queira alterar a logomarca já cadastrada clique <a href='#' onclick='return trocarLogo();'>[Aqui]</a>";
        document.getElementById("btnCad").innerHTML = "Atualizar dados Cadastrais";

    </script>

    <?php

}
?>