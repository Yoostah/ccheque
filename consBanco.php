<?php
		if (isset($_GET['op']) && $_GET['op'] == 'DEL') {
		    $cod = addslashes($_GET['cod']);
		    $pdo = _conectaBD();
			    $sql = $pdo->prepare("DELETE FROM bancos WHERE codigo = '$cod'");
			    $sql->execute();
			    unlink('_arquivos/_bancos/_logo_'.$cod.'.png');
			    _JS_Alerta("Banco Deletado!");    
		    
		}
?>

<h2 id="chequeTitle" class="bg-primary">:: BANCOS ::</h2>
<p class="bg-info">Bancos cadastrados no sistema</p>

<div class="table-responsive">
	<table class="table table-condensed">
		<?php
		$pdo = _conectaBD();
		$sql = $pdo->prepare("SELECT * FROM bancos");
		$sql->execute();

		if ($sql->rowCount() > 0){ ?>
		<thead>
			<th></th>
			<th></th>
			<th>Código</th>
			<th>Nome</th>
			<th></th>
		</thead>
		<tbody>
			
			<?php

				$bancos = $sql->fetchAll();

				foreach ($bancos as $banco) {
				?>
					<tr>
						<td></td>
						<?php /* retirar cache da imagem "$banco['logo'].'?'.time()" */ ?>
						<td><img src="<?php echo $banco['logo'].'.png?'.time() ?>"></td>
						<td><?php echo $banco['codigo'] ?></td>
						<td><?php echo $banco['nome'] ?></td>
						<td>
							<a href="main.php?func=cadBanco&op=UPD&cod=<?php echo $banco['codigo']; ?>"><img src="_imagens\editar.png"></a>
							<a href="main.php?func=consBanco&op=DEL&cod=<?php echo $banco['codigo']; ?>" onclick="return confirm('Deseja excluir o banco [ <?php echo $banco['nome']; ?> ] ?')"><img src="_imagens\excluir.png"></a>
                        </td>
					<?php
				}

				?>	
			
				
			</tr>
		</tbody>
		<?php  
		}else{?>
			<tr>
					<td colspan="3" align="center" style="font-weight: bold; color: red">Nenhum banco cadastrado</td>
			</tr> 
		<?php
		} ?>
	</table>

</div>