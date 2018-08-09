<?php
		//JS Validação formulário
	include '_scripts/s_cheque.php';
		
	if (isset($_POST['taxa']) && !empty($_POST['taxa'])){
		$bom_para = addslashes($_POST['bom_para']);
		$recebido_em = addslashes($_POST['recebido_em']);
		$taxa = addslashes($_POST['taxa']);
		$valor = addslashes($_POST['valor']);

		(isset($_POST['arredondar'])) ? $arredondar = 1 : $arredondar = 0;
		(isset($_POST['cod_comp'])) ? $cod_comp = addslashes($_POST['cod_comp']) : $cod_comp = 0;
		(isset($_POST['sel_banco'])) ? $sel_banco = addslashes($_POST['sel_banco']) : $sel_banco = '';
		(isset($_POST['agencia'])) ? $agencia = addslashes($_POST['agencia']) : $agencia = '';
		(isset($_POST['conta'])) ? $conta = addslashes($_POST['conta']) : $conta = '';
		(isset($_POST['cheque_num'])) ? $cheque_num = addslashes($_POST['cheque_num']) : $cheque_num = '';
		(isset($_POST['cliente'])) ? $cliente = addslashes($_POST['cliente']) : $cliente = '';
		(isset($_POST['titular'])) ? $titular = addslashes($_POST['titular']) : $titular = '';
		if (isset($_POST['op']) && $_POST['op'] == 1){

			$nova_data = dataCompFinal($bom_para, $cod_comp);

			$log[] = 'Cheque BOM PARA: '.date('d/m/Y', strtotime($bom_para)).'<br>Será COMPENSADO EM: '.date('d/m/Y', strtotime($nova_data['data'])).'<br>-----------------------------------<br><br>';
			if (strtotime($nova_data['data']) != strtotime($bom_para)){
				$log[] = 'Motivo: <br>'.$nova_data['log'];
			

			}
			
			// Calcula a diferença em segundos entre as datas + os dias do código de compensação
			$diferenca = (strtotime($nova_data['data']) - strtotime($recebido_em) + $cod_comp);
			//Calcula a diferença em dias
			$dias_correcao = floor($diferenca / (60 * 60 * 24));

			//Calculo do valor que será passado ao cliente
			$receita = number_format((($valor * ($taxa/100) ) / 30) * $dias_correcao,2,'.','');
			
			$valor_corrigido = $valor - $receita;	

			$_POST['simular'] = 1;
		}else if ($_POST['salvar'] == 1){
			$nova_data = dataCompFinal($bom_para, $cod_comp);

			$log[] = 'Cheque BOM PARA: '.date('d/m/Y', strtotime($bom_para)).'<br>Será COMPENSADO EM: '.date('d/m/Y', strtotime($nova_data['data'])).'<br>-----------------------------------<br><br>';
			if (strtotime($nova_data['data']) != strtotime($bom_para)){
				$log[] = 'Motivo: <br>'.$nova_data['log'];
			

			}
			
			// Calcula a diferença em segundos entre as datas + os dias do código de compensação
			$diferenca = (strtotime($nova_data['data']) - strtotime($recebido_em) + $cod_comp);

			//Calcula a diferença em dias
			$dias_correcao = floor($diferenca / (60 * 60 * 24));


			//Calculo do valor que será passado ao cliente
			if($arredondar == 1){
				$receita = ceil( (($valor*($taxa/100)) / 30) * $dias_correcao);
				$valor_corrigido = $valor - $receita;

			}else{
				$receita = number_format((($valor * ($taxa/100) ) / 30) * $dias_correcao,2,'.','');

				$valor_corrigido = $valor - $receita;	

			}	
			
			(strtotime($nova_data['data']) < strtotime(date('Y-m-d')))? $compensado = 1: $compensado = NULL;
			//echo $compensado;
			$pdo = _conectaBD();

			$sql = $pdo->prepare("INSERT INTO cheque (banco, agencia, conta_corrente, num_cheque, valor, taxa, recebido_em, bom_para, data_compensacao, dias_correcao, valor_corrigido, cliente, titular_cheque, compensou, compensado, cod_compensacao, arredondado, receita, usu_id) VALUES (:banco, :agencia, :conta_corrente, :num_cheque, :valor, :taxa, :recebido_em, :bom_para, :data_compensacao, :dias_correcao, :valor_corrigido, :cliente, :titular_cheque, '0', :compensado, :cod_compensacao, :arredondado, :receita, :usu_id)");
					$sql->bindValue(":banco", $sel_banco);
					$sql->bindValue(":agencia", $agencia);
					$sql->bindValue(":conta_corrente", $conta);
					$sql->bindValue(":num_cheque", $cheque_num);
					$sql->bindValue(":valor", $valor);
					$sql->bindValue(":taxa", $taxa);
					$sql->bindValue(":recebido_em", $recebido_em);
					$sql->bindValue(":bom_para", $bom_para);
					$sql->bindValue(":data_compensacao", $nova_data['data']);
					$sql->bindValue(":dias_correcao", $dias_correcao);
					$sql->bindValue(":valor_corrigido", $valor_corrigido);
					$sql->bindValue(":cliente", $cliente);
					$sql->bindValue(":titular_cheque", $titular);
					$sql->bindValue(":compensado", $compensado);
					$sql->bindValue(":cod_compensacao", $cod_comp);
					$sql->bindValue(":arredondado", $arredondar);
					$sql->bindValue(":receita", $receita);
					$sql->bindValue(":usu_id", $_SESSION['id']);

					$result = '';
					try {
						$sql->execute();
					} catch (Exception $e) {
						$result = explode(':', $e->getMessage());
					}
					if ($sql->rowCount() > 0){
						
						_JS_Alerta("Cheque cadastrado com sucesso!");
					}else{
						_JS_Alerta("Erro ao cadastrar Cheque! Verifique os dados e tente novamente.\\n[ ".$result[2]." ]");
					}			
		}
	}
?>



<h1>Cadastro de Cheque</h1>
<h3>Cadastre os Cheques</h3>

<div class="container">

	<div class="formulario">
		<!--<div style="height: 30px">
			<div>
				<label for="logo">&nbsp;</label>
				<img style="padding-left: 4px;" id="logo" src="">
			</div>
		</div>-->
		<form method="POST">
			<div class="form-row">
				<h2>Dados Cliente</h2>
				<div class="form-group col-md-6">
					<div>
						<label for="cliente">Cliente:</label>
						<input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nome do Cliente">
					</div>
				</div>
				<div class="form-group col-md-6">
					<div>
						<label for="titular">Titular do Cheque (OPCIONAL)</label>
						<input type="text" class="form-control" name="titular" id="titular" placeholder="Titular do Cheque">
					</div>
				</div>	
			</div>
			<div class="form-row">
				<h2>Dados do Cheque</h2>
				<div class="form-group col-md-3">
					<label for="agencia">Banco:</label>	
					<div class="input-group">
						
						<span style="padding: 0px;" class="input-group-addon">
                        	<img  id="logo" >
                        </span>
						<select name="sel_banco" id="sel_banco" class="form-control" onchange="mostrarLogo()">
							<option value="0" disabled selected>Escolha</option>
							<?php
								$pdo = _conectaBD();
								$sql = $pdo->prepare("SELECT codigo, nome FROM bancos");
								$sql->execute();
								if ($sql->rowCount() > 0) {						            
									$bancos = $sql->fetchAll();
									foreach ($bancos as $banco) {
										$nome = $banco['nome'];
										$cod = $banco['codigo'];?>
										<option value="<?php echo $cod ?>"><?php echo $nome ?></option><?php
									}
								}	
							?>
						</select>
					</div>
				</div>
				
				<div class="form-group col-md-3">
					<div>
						<label for="agencia">Agência:</label>
						<input type="text" class="form-control" name="agencia" id="agencia" placeholder="Agência">
					</div>
				</div>
				<div class="form-group col-md-3">
					<div>
						<label for="conta">Conta:</label>
						<input type="text" class="form-control" name="conta" id="conta" placeholder="Conta">
					</div>
				</div>
				<div class="form-group col-md-3">
					<div>
						<label for="cheque_num">Número do Cheque:</label>
						<input type="text" class="form-control" id="cheque_num" name="cheque_num" placeholder="Número do Cheque">	
					</div>	
				</div>
				
			</div> <!-- <div form-row> -->
			<div class="form-row">
				<div class="form-group col-md-4">    
				    <div>
				     	<label for="recebido_em">Recebido em:</label>
				     	<input type="date" class="form-control" name="recebido_em" id="recebido_em">   
				    </div>
				</div>
				<div class="form-group col-md-4">    
				    <div>
				     	<label for="bom_para">Bom para:</label>
				     	<input type="date" class="form-control" name="bom_para" id="bom_para">   
				    </div>
				</div>
				<div class="form-group col-md-4">
					<div>
						<label for="cod_comp">Cod. Compensação:</label>
						<select name="cod_comp" id="cod_comp" class="form-control">
							<option id="0" value="0" selected>+0 Dia</option>
							<option id="1" value="1">+1 Dia</option>
							<option id="2" value="2">+2 Dias</option>
							<option id="3" value="3">+3 Dias</option>
							<option id="4" value="4">+4 Dias</option>
						</select>
					</div>
				</div>				
			</div>  <!-- <div form-row> -->	
			<div class="form-row" style="text-align: left">
				<div class="form-group col-md-6">
						<label for="valor">Valor:</label>
					<div class="input-group">				     	
				     	<span class="input-group-addon">R$</span>
				     	<input type="number" class="form-control" name="valor" id="valor" placeholder="Valor do Cheque" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$">
				    </div>
				</div>			
				<div class="form-group col-md-6">    
				        <label for="taxa">Taxa:</label>
				    <div class="input-group">				     	
				     	<span class="input-group-addon">%</span>  	
				      	<input type="number" class="form-control" name="taxa" id="taxa" placeholder="Taxa de Juros" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$">  
				    </div>
				</div>
			</div> <!--<div for-row> -->
				
			<div id="form_sim" class="form-row" style="display: none">
				<h2>Simulação</h2>	
				<div class="form-group col-md-3">    
				    <div>
				     	<label for="dias_correcao">
				     		Dias de Correção:<a class="info" onclick="mostrarDetalhes()">
          										<span class="glyphicon glyphicon-info-sign"></span>
        									 </a>
				     	</label>
				     	<input type="text" class="form-control" name="dias_correcao" id="dias_correcao">
				     	
				     	<div id="info-comp" class="form-group" style="display: none">
						    <label for="comment">Detalhes:</label>
						    <textarea class="form-control" rows="5" id="detalhes" style="font-size: .7em;"><?php foreach ($log as $registro) {
						    		echo str_replace('<br>', PHP_EOL, $registro);
						    	}
						    	?>
						    </textarea>
					    </div>   
				    </div>
				</div>
				<div class="form-group col-md-3">
					<div>
				     	<label for="data_comp">Data Compensação:</label>
				     	<input type="date" class="form-control" name="data_comp" id="data_comp">   
				    </div>
				</div>
				<div class="form-group col-md-3">    
						<label for="valor_corrigido">Valor Corrigido:</label>
				    <div class="input-group">				     	
				     	<span class="input-group-addon">R$</span> 	
				     	<input type="number" class="form-control" name="valor_corrigido" id="valor_corrigido" step=".01" pattern="^\d+(?:\.\d{1,2})?$">   
				    </div>				    	
				</div>
				<div class="form-group col-md-3">    
				    
				      	<label for="receita">Receita:</label>
				    <div class="input-group">				     	
				     	<span class="input-group-addon">R$</span>  	
				      	<input type="number" class="form-control" name="receita" id="receita" step=".01" pattern="^\d+(?:\.\d{1,2})?$">  
				    </div>
				    <div class="form-check">
					    <label class="form-check-label" style="font-weight: normal; color: rgba(1,1,1,.3);">
					    	<input id="arredondar" name="arredondar" type="checkbox" class="form-check-input" style="margin-right: 5px;">Arredondar
					    </label>
					</div>
				</div>

			</div> <!-- <div form-row> -->	
			<div class="btn-toolbar pull-right col-md-3">
				<button  style="width: 100%" id="btnSim" class="btn btn-danger pull-right" type="submit" onclick="return simular()">Simular Cheque</button><br><br>
				<button  style="width: 100%" id= "btnCad" class="btn btn-warning pull-right" type="submit" onclick="return cadastrar()">Cadastrar Cheque</button><br><br>
				<!--<button  style="width: 100%" id= "btnCad" class="btn btn-info pull-right" formaction="main.php?func=cadGCheque">Cadastrar Grupo de Cheque</button>-->
			</div>	
			
		<input type="hidden" name="op" id="opcao" value="0">
		<input type="hidden" name="salvar" id="salvar" value="0">	
		</form>
	</div> <!-- <div formulario> -->		
</div> <!-- <div container> -->




<?php 
if (isset($_POST['simular'])) {
	if($arredondar == 1){
		$receita = ceil( (($valor*($taxa/100)) / 30) * $dias_correcao);
		$valor_corrigido = $valor - $receita;
	
	?>
		<script>
			document.getElementById('arredondar').checked = true;
		</script>
	<?php	
	}

	?>

    <script>
    	//Mostra formulário de Simulação
    	document.getElementById('form_sim').style.display = "block";
    	
    	//Preenche os campos do cadastro
    	document.getElementById('sel_banco').value = "<?php echo $sel_banco ?>";
    	if (document.getElementById('sel_banco').value != 0){
    		mostrarLogo();
    	}
    	document.getElementById('agencia').value = "<?php echo $agencia ?>";
    	document.getElementById('conta').value = "<?php echo $conta ?>";
    	document.getElementById('cheque_num').value = "<?php echo $cheque_num ?>";
    	document.getElementById('cliente').value = "<?php echo $cliente ?>";
    	document.getElementById('titular').value = "<?php echo $titular ?>";

    	//Preenche os campos da simulação
        document.getElementsByName('data_comp')[0].value = "<?php echo date('Y-m-d', strtotime($nova_data['data'])) ?>";
        document.getElementsByName('dias_correcao')[0].value = "<?php echo $dias_correcao ?>";
        document.getElementsByName('valor_corrigido')[0].value = "<?php echo $valor_corrigido ?>";
        document.getElementsByName('receita')[0].value = "<?php echo $receita ?>";
        document.getElementById("cod_comp").value = "<?php echo $cod_comp ?>";
        

        //Preenche os dados de cadastro novamente
        document.getElementsByName('bom_para')[0].value = "<?php echo date('Y-m-d', strtotime($bom_para)) ?>";
        document.getElementsByName('recebido_em')[0].value = "<?php echo date('Y-m-d', strtotime($recebido_em)) ?>";
        document.getElementsByName('valor')[0].value = "<?php echo $valor ?>";
        document.getElementsByName('taxa')[0].value = "<?php echo $taxa ?>";

        //Bloqueia os campos para que os valores não sejam editados
        document.getElementsByName('data_comp')[0].disabled = true;
        document.getElementsByName('dias_correcao')[0].disabled = true;
        document.getElementsByName('valor_corrigido')[0].disabled = true;
        document.getElementsByName('receita')[0].disabled = true; 
        location.href = "#form_sim";    
       
    </script>

    <?php
    //unset($_POST['simular']);
}

?>

