
<?php
		//JS Validação formulário
	include '_scripts/s_cheque.php';
		
	//print_r($_POST);


	if (isset($_POST['cadastro']) && $_POST['cadastro'] == 2) {
		$pdo = _conectaBD();

		$sql = $pdo->prepare("INSERT INTO cheque (`banco`, `agencia`, `conta_corrente`, `num_cheque`, `valor`, `taxa`, `recebido_em`, `bom_para`, `data_compensacao`, `dias_correcao`, `valor_corrigido`, `cliente`, `titular_cheque`, `compensou`, `compensado`, `cod_compensacao`, `arredondado`, `receita`, `usu_id`) SELECT `banco`, `agencia`, `conta_corrente`, `num_cheque`, `valor`, `taxa`, `recebido_em`, `bom_para`, `data_compensacao`, `dias_correcao`, `valor_corrigido`, `cliente`, `titular_cheque`, `compensou`, `compensado`, `cod_compensacao`, `arredondado`, `receita`, `usu_id` FROM cheque_temp");
	    $sql->execute();
	    if ($sql->rowCount() > 0) {						            
		    echo '<script>document.getElementById("cadastro").value = 0;</script>';
		    _JS_Alerta('Grupo de Cheques cadastrado no sistema.');
	    }

	    $sql = $pdo->prepare("TRUNCATE cheque_temp");
	    $sql->execute();    
	}



	if (isset($_POST['salvar']) && $_POST['salvar'] == 1) {
		$cliente = ($_POST['cliente']);
		$sel_banco = ($_POST['sel_banco']);
		$agencia = ($_POST['agencia']);
		$conta = ($_POST['conta']);
		$cheque_num = ($_POST['cheque_num']);
		$recebido_em = ($_POST['recebido_em']);
		$bom_para = ($_POST['bom_para']);
		$cod_comp = ($_POST['cod_comp']);
		$valor = ($_POST['valor']);
		$taxa = ($_POST['taxa']);
		$titular = ($_POST['titular']);

		$pdo = _conectaBD();

		$sql = $pdo->prepare("INSERT INTO cheque_temp (banco, agencia, conta_corrente, num_cheque, valor, taxa, recebido_em, bom_para, data_compensacao, dias_correcao, valor_corrigido, cliente, titular_cheque, compensou, compensado, cod_compensacao, arredondado, receita, usu_id) VALUES (:banco, :agencia, :conta_corrente, :num_cheque, :valor, :taxa, :recebido_em, :bom_para, :data_compensacao, :dias_correcao, :valor_corrigido, :cliente, :titular_cheque, '0', :compensado, :cod_compensacao, :arredondado, :receita, :usu_id)");

		foreach ($sel_banco as $a => $b) {
			$nova_data = dataCompFinal($bom_para[$a], $cod_comp[$a]);

			/*$log[] = 'Cheque BOM PARA: '.date('d/m/Y', strtotime($bom_para[$a])).'<br>Será COMPENSADO EM: '.date('d/m/Y', strtotime($nova_data['data'])).'<br>-----------------------------------<br><br>';
			if (strtotime($nova_data['data']) != strtotime($bom_para[$a])){
				$log[] = 'Motivo: <br>'.$nova_data['log'];
			

			}
			*/
			// Calcula a diferença em segundos entre as datas + os dias do código de compensação
			$diferenca = (strtotime($nova_data['data']) - strtotime($recebido_em[$a]) + $cod_comp[$a]);
			//Calcula a diferença em dias
			$dias_correcao = floor($diferenca / (60 * 60 * 24));

			//Calculo do valor que será passado ao cliente
			$receita = number_format((($valor[$a] * ($taxa[$a]/100) ) / 30) * $dias_correcao,2,'.','');
			
			$valor_corrigido = $valor[$a] - $receita;

			(strtotime($nova_data['data']) < strtotime(date('Y-m-d')))? $compensado = 1: $compensado = NULL;	
			
			$sql->bindValue(":banco", $sel_banco[$a]);
			$sql->bindValue(":agencia", $agencia[$a]);
			$sql->bindValue(":conta_corrente", $conta[$a]);
			$sql->bindValue(":num_cheque", $cheque_num[$a]);
			$sql->bindValue(":valor", $valor[$a]);
			$sql->bindValue(":taxa", $taxa[$a]);
			$sql->bindValue(":recebido_em", $recebido_em[$a]);
			$sql->bindValue(":bom_para", $bom_para[$a]);
			$sql->bindValue(":cliente", $cliente);
			$sql->bindValue(":cod_compensacao", $cod_comp[$a]);
			$sql->bindValue(":usu_id", $_SESSION['id']);
			
			$sql->bindValue(":data_compensacao", $nova_data['data']);
			$sql->bindValue(":dias_correcao", $dias_correcao);
			$sql->bindValue(":valor_corrigido", $valor_corrigido);
			$sql->bindValue(":titular_cheque", $titular[$a]);
			$sql->bindValue(":compensado", $compensado);
			$sql->bindValue(":arredondado", 0);
			$sql->bindValue(":receita", $receita);

					$result = '';
					try {
						$sql->execute();
					} catch (Exception $e) {
						$result = explode(':', $e->getMessage());
						_JS_Alerta("Erro ao cadastrar Cheque! Verifique os dados e tente novamente.\\n[ ".$result[1].$result[2]." ]");
					}					
		}
	

		/*Mostra relatório de cheques */
		?>
		<!-- Modal -->
		<div class="modal fade" id="relatorioModal" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h3 class="modal-title" id="relModalLabel">Relatório de Cheques Pré-Cadastrados</h3>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      <?php 

		      $sql = $pdo->prepare("SELECT * FROM cheque_temp ORDER BY id");
		      $sql->execute();
			  if ($sql->rowCount() > 0) {						            
				  $cheques = $sql->fetchAll();
			  }else{
			  	break;
			  }

			  $valor_corrigido = 0;	
			  foreach ($cheques as $cheque) {
			  	echo "<div class='panel panel-default'>";
			  	echo "<div class='panel-body'>";
			  	echo "<strong>Valor: </strong>R$".number_format($cheque['valor'],2, ',', ' ').'<br>';
			  	echo "<strong>Recebido em: </strong>".date('d/m/Y', strtotime($cheque['recebido_em'])).'<br>';
			  	echo "<strong>Bom para: </strong>".date('d/m/Y', strtotime($cheque['bom_para'])).'<br>';
			  	echo "<strong>Dias de Correção: </strong>".$cheque['dias_correcao'].'<br>';
			  	echo "<strong>Valor Corrigido: </strong>R$".number_format($cheque['valor_corrigido'],2, ',', ' ');
			  	echo "</div>";
			  	echo "</div>";
			  	$valor_corrigido += $cheque['valor_corrigido'];
			  }

			  echo "<h4 class='bg-info cadGrupoTotal'>Valor Total Corrigido: <strong>R$".number_format($valor_corrigido,2, ',', ' ').'</strong></h4>';
		      ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" onclick="corrigirCadastro()">Corrigir Cadastro</button>
		        <button type="button" class="btn btn-primary" onclick="aprovarCadastro()">Aprovar Cadastro</button>
		      </div>
		    </div>
		  </div>
		</div>

		<script type="text/javascript">
			$('#relatorioModal').modal('show')
		</script>
		<?php
	}



	//Armazena o fetch dos bancos.
	$pdo = _conectaBD();
	$sql = $pdo->prepare("SELECT codigo, nome FROM bancos");
	$sql->execute();
	if ($sql->rowCount() > 0) {						            
		$bancos = $sql->fetchAll();
	}	
?>


<div class="container">
	<h1>Cadastro de Grupo de Cheques</h1>
	<div class="panel-footer dica">
			<h5>Aperte <span class="glyphicon glyphicon-plus gs"></span> para cadastrar um novo cheque.</h5><br> 
			<h5>Pressione <span class="glyphicon glyphicon-minus gs"></span> para apagar um cheque.</h5>
	</div>
	<div class="formulario">
		<form name='precadastro' method="POST">
			<div class="panel panel-default">
			  <div class="panel-body">
			  	<div class="col-md-12">	
				  <div class="form-row">
			     	<h2>Dados Cliente</h2>
			      </div>
		    	  <div class="row">		    	  	
					 <div class="col-sm-12">
						<div class="form-group col-md-12">
							<label for="cliente">Nome:</label>
							<input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nome do Cliente" required>
						</div>							
					</div>
				  </div>
				</div>
			  </div>  	  
			</div>
			<div class="panel panel-default">
			  <div class="panel-body">		  		
			    <div class="col-sm-12 nopadding">			    	 
				    <div class="form-row">
				    			
						<h2>Dados do Cheque</h2>
						<div class="form-group col-md-12">
							<div>
								<label for="titular">Titular do Cheque:</label>
								<input type="text" class="form-control" name="titular[]" id="titular" placeholder="Titular do Cheque" required>
							</div>							
						</div>
						<div class="form-group col-md-3">
							<label for="agencia">Banco:</label>	
							<div class="input-group">
								
								<span style="padding: 0px;" class="input-group-addon">
		                        	<img  id="logo" >
		                        </span>
								<select name="sel_banco[]" id="sel_banco" class="form-control" onchange="" required>
									<option value="0" disabled selected>Escolha</option>									
									<?php 
										foreach ($bancos as $banco) {
										$nome = $banco['nome'];
										$cod = $banco['codigo'];?>
										<option value="<?php echo $cod ?>"><?php echo $nome ?></option><?php
									}
										
									?>
								</select>
								
							</div>
						</div>
						
						<div class="form-group col-md-3">
							<div>
								<label for="agencia">Agência:</label>
								<input type="number" class="form-control" name="agencia[]" id="agencia" placeholder="Agência" required>
							</div>
						</div>
						<div class="form-group col-md-3">
							<div>
								<label for="conta">Conta:</label>
								<input type="number" class="form-control" name="conta[]" id="conta" placeholder="Conta" required>
							</div>
						</div>
						<div class="form-group col-md-3">
							<div>
								<label for="cheque_num">Número do Cheque:</label>
								<input type="number" class="form-control" id="cheque_num" name="cheque_num[]" placeholder="Número do Cheque" required>	
							</div>	
						</div>				
					</div> <!-- <div form-row> -->
					<div class="form-row">
						<div class="form-group col-md-4">    
						    <div>
						     	<label for="recebido_em">Recebido em:</label>
						     	<input type="date" class="form-control" name="recebido_em[]" id="recebido_em" required>   
						    </div>
						</div>
						<div class="form-group col-md-4">    
						    <div>
						     	<label for="bom_para">Bom para:</label>
						     	<input type="date" class="form-control" name="bom_para[]" id="bom_para" required>   
						    </div>
						</div>
						<div class="form-group col-md-4">
							<div>
								<label for="cod_comp">Cod. Compensação:</label>
								<select name="cod_comp[]" id="cod_comp" class="form-control">
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
						     	<input type="number" class="form-control" name="valor[]" id="valor" placeholder="Valor do Cheque" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$" required>
						    </div>
						</div>			
						<div class="form-group col-md-6">    
						        <label for="taxa">Taxa:</label>
						    <div class="input-group">				     	
						     	<span class="input-group-addon">%</span>  	
						      	<input type="number" class="form-control" name="taxa[]" id="taxa" placeholder="Taxa de Juros" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$" required>  
						    </div>
						</div>
					</div> <!--<div for-row> -->
					<div class="form-row pull-right" style="margin-bottom: 10px;margin-right: 15px;">
						<label for="cliente">Adicionar Novo Cheque</label>
				    	<button class="btn btn-success btn_add_rem_chq" type="button" onclick="adicionar_cheque();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
				    </div>	
				</div>
				<div id="cheques"></div>
			    <div style="clear: both"></div>					
			</div>
			<input type="hidden" name="salvar" id="salvar" value="0">	
			<input type="text" name="cadastro" id="cadastro" value="0">	
			<div class="btn-toolbar pull-right col-md-3">
				<button  style="width: 100%; margin-bottom: 15px; margin-top: 15px" id= "btnCad" class="btn btn-warning pull-right" type="submit" onclick="return cadastrarGrupo()">Cadastrar Grupo</button>
			</div>	
		</form>
	</div> <!-- <div formulario> -->
			
</div> <!-- <div container> -->

<script type="text/javascript">
var room = 1;
function adicionar_cheque() { 
    room++;
    var objTo = document.getElementById('cheques')
    var divtest = document.createElement("div");
	divtest.setAttribute("class", "form-group removeclass"+room);
	var rdiv = 'removeclass'+room;
    divtest.innerHTML = '<div class="col-sm-12 nopadding">'+			    	 
						    '<div class="form-row">'+
						    			
								'<h2>Dados do Cheque</h2>'+
								'<div class="form-group col-md-12">'+
									'<div>'+
										'<label for="cliente">Titular do Cheque:</label>'+
										'<input type="text" class="form-control" name="titular[]" id="titular" placeholder="Titular do Cheque" required>'+
									'</div>'+					
								'</div>'+
								'<div class="form-group col-md-3">'+
									'<label for="agencia">Banco:</label>'+
									'<div class="input-group">'+										
										'<span style="padding: 0px;" class="input-group-addon">'+
				                        	'<img  id="logo" >'+
				                        '</span>'+
										'<select name="sel_banco[]" id="sel_banco" class="form-control" onchange="">'+
											'<option value="0" disabled selected>Escolha</option>'+								
											<?php 
											foreach ($bancos as $banco) {
											$nome = $banco['nome'];
											$cod = $banco['codigo'];?>
											
											'<option value="<?php echo $cod ?>"><?php echo $nome ?></option>'+
											
											<?php
											}?>
										'</select>'+
									'</div>'+
								'</div>'+
								
								'<div class="form-group col-md-3">'+
									'<div>'+
										'<label for="agencia">Agência:</label>'+
										'<input type="number" class="form-control" name="agencia[]" id="agencia" placeholder="Agência" required>'+
									'</div>'+
								'</div>'+
								'<div class="form-group col-md-3">'+
									'<div>'+
										'<label for="conta">Conta:</label>'+
										'<input type="number" class="form-control" name="conta[]" id="conta" placeholder="Conta" required>'+
									'</div>'+
								'</div>'+
								'<div class="form-group col-md-3">'+
									'<div>'+
										'<label for="cheque_num">Número do Cheque:</label>'+
										'<input type="number" class="form-control" id="cheque_num" name="cheque_num[]" placeholder="Número do Cheque" required>'+	
									'</div>'+	
								'</div>'+				
							'</div>'+
							'<div class="form-row">'+
								'<div class="form-group col-md-4">'+   
								    '<div>'+
								     	'<label for="recebido_em">Recebido em:</label>'+
								     	'<input type="date" class="form-control" name="recebido_em[]" id="recebido_em" required>'+   
								    '</div>'+
								'</div>'+
								'<div class="form-group col-md-4">'+    
								   	'<div>'+
								     	'<label for="bom_para">Bom para:</label>'+
								     	'<input type="date" class="form-control" name="bom_para[]" id="bom_para" required>'+   
								    '</div>'+
								'</div>'+
								'<div class="form-group col-md-4">'+
									'<div>'+
										'<label for="cod_comp">Cod. Compensação:</label>'+
										'<select name="cod_comp[]" id="cod_comp" class="form-control">'+
											'<option id="0" value="0" selected>+0 Dia</option>'+
											'<option id="1" value="1">+1 Dia</option>'+
											'<option id="2" value="2">+2 Dias</option>'+
											'<option id="3" value="3">+3 Dias</option>'+
											'<option id="4" value="4">+4 Dias</option>'+
										'</select>'+
									'</div>'+
								'</div>'+				
							'</div>  <!-- <div form-row> -->'+	
							'<div class="form-row" style="text-align: left">'+
								'<div class="form-group col-md-6">'+
										'<label for="valor">Valor:</label>'+
									'<div class="input-group">'+				     	
								     	'<span class="input-group-addon">R$</span>'+
								     	'<input type="number" class="form-control" name="valor[]" id="valor" placeholder="Valor do Cheque" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$" required>'+
								    '</div>'+
								'</div>'+			
								'<div class="form-group col-md-6">'+    
								        '<label for="taxa">Taxa:</label>'+
								    '<div class="input-group">'+				     	
								     	'<span class="input-group-addon">%</span>'+  	
								      	'<input type="number" class="form-control" name="taxa[]" id="taxa" placeholder="Taxa de Juros" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$" required>'+  
								    '</div>'+
								'</div>'+
							'</div> <!--<div for-row> -->'+
							'<div class="form-row pull-right" style="margin-bottom: 10px;margin-right: 15px;">'+
								'<label for="cliente" style="margin-right: 5px">Remover Cheque</label>'+
						    	'<button class="btn btn-danger btn_add_rem_chq" type="button"  onclick="remove_cheque('+room+');"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button>'+
						    	'<label for="cliente" style="margin-right: 5px; margin-left: 5px">Adicionar Novo Cheque</label>'+
				    			'<button class="btn btn-success btn_add_rem_chq" type="button"  onclick="adicionar_cheque();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>'+
						    '</div>'+	
						'</div>';
    
    objTo.appendChild(divtest)
	}
   
   	function remove_cheque(id) {
	   $('.removeclass'+id).remove();
   	}
</script>

