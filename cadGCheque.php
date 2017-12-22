<?php
		//JS Validação formulário
	include '_scripts/s_cheque.php';
?>

<h1>Cadastro de Grupo de Cheques</h1>
<h3>Informe a quantidade de cheques que deseja cadastrar e insira os dados dos Cheques</h3>

<div class="container">

	<div class="formulario">
		<form method="POST">	
			<div class="panel panel-default">
			  	  
			  <div class="panel-body">
		  		<div class="form-row">
		    		<h2>Dados Cliente</h2>
		    	</div>
		    	<div class="row">
					<div class="col-md-12">
					<div class="form-group col-md-8">
						<div>
							<label for="cliente">Nome:</label>
							<input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nome do Cliente">
						</div>							
					</div>
					<div class="form-group col-md-4">
						
					</div>
					</div>    				
				</div> 
			  	<div id="education_fields"></div>
			    <div class="col-sm-12 nopadding">			    	 
				    <div class="form-row">
				    			
						<h2>Dados do Cheque</h2>

						<div class="form-group col-md-3">
							<label for="agencia">Banco:</label>	
							<div class="input-group">
								
								<span style="padding: 0px;" class="input-group-addon">
		                        	<img  id="logo" src="">
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
								<input type="number" class="form-control" name="agencia" id="agencia" placeholder="Agência">
							</div>
						</div>
						<div class="form-group col-md-3">
							<div>
								<label for="conta">Conta:</label>
								<input type="number" class="form-control" name="conta" id="conta" placeholder="Conta">
							</div>
						</div>
						<div class="form-group col-md-3">
							<div>
								<label for="cheque_num">Número do Cheque:</label>
								<input type="number" class="form-control" id="cheque_num" name="cheque_num" placeholder="Número do Cheque">	
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
						      	<input type="number" class="form-control" name="taxa" id="taxa" placeholder="Taxa de Juros" step=".01" pattern="^\d+(?:\.\d{1,2})?$">  
						    </div>
						</div>
					</div> <!--<div for-row> -->
					<div class="form-row pull-right" style="margin-bottom: 10px;margin-right: 15px;">
						<label for="cliente">Adicionar Novo Cheque</label>
				    	<button class="btn btn-success" type="button"  onclick="adicionar_cheque();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
				    </div>	
				</div>
				
			    <div style="clear: both"></div>	
				<div class="panel-footer">
			  		<small>Aperte <span class="glyphicon glyphicon-plus gs"></span> para cadastrar um novo cheque.</small><br> 
			  		<small>Pressione <span class="glyphicon glyphicon-minus gs"></span> para apagar um cheque</small>
				</div>
			</div>
		</form>
	</div> <!-- <div formulario> -->		
</div> <!-- <div container> -->

<script type="text/javascript">
var room = 1;
function adicionar_cheque() {
 
    room++;
    var objTo = document.getElementById('education_fields')
    var divtest = document.createElement("div");
	divtest.setAttribute("class", "form-group removeclass"+room);
	var rdiv = 'removeclass'+room;
    divtest.innerHTML = '<div class="col-sm-12 nopadding"><div class="form-row"><h2>Dados do Cheque</h2><div class="form-group col-md-3"><label for="agencia">Banco:</label>	<div class="input-group"><span style="padding: 0px;" class="input-group-addon"><img  id="logo" src=""></span><select name="sel_banco" id="sel_banco" class="form-control" onchange="mostrarLogo()"><option value="0" disabled selected>Escolha</option><?php
										$pdo = _conectaBD();
										$sql = $pdo->prepare("SELECT codigo, nome FROM bancos");
										$sql->execute();
										if ($sql->rowCount() > 0) {						            
											$bancos = $sql->fetchAll();
											foreach ($bancos as $banco) {
												$nome = $banco['nome'];
												$cod = $banco['codigo'];?><option value="<?php echo $cod ?>"><?php echo $nome ?></option><?php
											}
										}	
									?></select></div></div><div class="form-group col-md-3"><div><label for="agencia">Agência:</label><input type="number" class="form-control" name="agencia" id="agencia" placeholder="Agência"></div></div><div class="form-group col-md-3"><div><label for="conta">Conta:</label><input type="number" class="form-control" name="conta" id="conta" placeholder="Conta"></div></div><div class="form-group col-md-3"><div><label for="cheque_num">Número do Cheque:</label><input type="number" class="form-control" id="cheque_num" name="cheque_num" placeholder="Número do Cheque"></div></div></div> <!-- <div form-row> --><div class="form-row"><div class="form-group col-md-4"><div><label for="recebido_em">Recebido em:</label><input type="date" class="form-control" name="recebido_em" id="recebido_em"></div></div><div class="form-group col-md-4"><div><label for="bom_para">Bom para:</label><input type="date" class="form-control" name="bom_para" id="bom_para"></div></div><div class="form-group col-md-4"><div><label for="cod_comp">Cod. Compensação:</label><select name="cod_comp" id="cod_comp" class="form-control"><option id="0" value="0" selected>+0 Dia</option><option id="1" value="1">+1 Dia</option><option id="2" value="2">+2 Dias</option><option id="3" value="3">+3 Dias</option><option id="4" value="4">+4 Dias</option></select></div></div>				</div>  <!-- <div form-row> --><div class="form-row" style="text-align: left"><div class="form-group col-md-6"><label for="valor">Valor:</label><div class="input-group"><span class="input-group-addon">R$</span><input type="number" class="form-control" name="valor" id="valor" placeholder="Valor do Cheque" onkeydown="javascript: return event.keyCode == 69 ? false : true" step=".01" pattern="^\d+(?:\.\d{1,2})?$"></div></div><div class="form-group col-md-6"><label for="taxa">Taxa:</label><div class="input-group">				     	<span class="input-group-addon">%</span><input type="number" class="form-control" name="taxa" id="taxa" placeholder="Taxa de Juros" step=".01" pattern="^\d+(?:\.\d{1,2})?$"></div></div></div> <!--<div for-row> --><div class="form-row pull-right" style="margin-bottom: 10px;margin-right: 15px;"><label for="cliente">Adicionar Novo Cheque</label><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button></div></div>';
    
    objTo.appendChild(divtest)
}
   function remove_education_fields(rid) {
	   $('.removeclass'+rid).remove();
   }
</script>