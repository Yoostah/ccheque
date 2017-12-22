<?php
		if (isset($_GET['op']) && $_GET['op'] == 'DEL') {
		    $id = addslashes($_GET['cod']);
		    $pdo = _conectaBD();
			    $sql = $pdo->prepare("DELETE FROM feriados WHERE id = '$id' and usu_id = $session_usu_id");
			    $sql->execute();
			    _JS_Alerta("Feriado Deletado!");    
		    
		}
?>

<h2 id="chequeTitle" class="bg-primary">:: FERIADOS ::</h2>
<p class="bg-info">Feriados cadastrados no sistema</p>

<div class="table-responsive">
	<table class="table table-condensed">
		<?php
		$pdo = _conectaBD();
		$sql = $pdo->prepare("SELECT * FROM feriados where usu_id = $session_usu_id order by data");
		$sql->execute();

		if ($sql->rowCount() > 0){ ?>
		<thead>
			<th></th>
			<th>Nome</th>
			<th>Data</th>
			<th>Dia Fixo?</th>
			<th></th>
		</thead>
		<tbody>
			
			<?php

				$feriados = $sql->fetchAll();
				foreach ($feriados as $feriado) {
				?>
					<tr>
						<td></td>
						<td><?php echo $feriado['nome'] ?></td>
						<?php
							$data = date('d/m/Y', strtotime($feriado['data']));
						?>
						<td><?php echo $data ?></td>
						<?php
							$data_fixa = '';
							if($feriado['data_fixa'] == 1){
								$data_fixa = '<img src="_imagens/check.png">';
							}
						?>
						<td><?php echo $data_fixa ?></td>
						<td>
							<a href="main.php?func=cadFeriado&op=UPD&cod=<?php echo $feriado['id']; ?>"><img src="_imagens\editar.png"></a>
							<a href="main.php?func=consFeriado&op=DEL&cod=<?php echo $feriado['id']; ?>" onclick="return confirm('Deseja excluir o feriado [ <?php echo $feriado['nome']; ?> ] ?')"><img src="_imagens\excluir.png"></a>
                        </td>
					<?php
				}

				?>	
			
				
			</tr>
		</tbody>
		<?php  
		}else{?>
			<tr>
					<td colspan="3" align="center" style="font-weight: bold; color: red">Nenhum feriado cadastrado</td>
			</tr> 
		<?php
		} ?>
	</table>

</div>