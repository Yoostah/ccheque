<?php
include 'const.php';


/*FUNÇÕES DE BANCO*/
/* Função para estabelecer conexão com o banco. Retorna um objeto pdo*/
function _conectaBD (){

    //TRY/CATCH DE CONEXÃO
    try {
        $pdo = new PDO(_DSN, _DBUSER, _DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;

    } catch(PDOException $e) {
        echo 'Erro de conexão ('.$e->getMessage().')';
    }
}

/*Função para executar uma query sql passada. Retorna um array com o resultado obtido da query*/
function _executaSQL ($sql){
    $pdo = _conectaBD();
    return $sql = $pdo->query($sql);
}



/*FUNÇÕES DE JAVASCRIPT*/
/* Recebe uma mensagem como parâmetro e abre um alert com essa mensagem */
function _JS_Alerta($js){
echo '<script language="JavaScript" type="text/javascript">alert("'.$js.'"); </script>';

}

/* Recebe o id de um campo de text e um valor. Insere o valor no campo com o id informado */
function _PreencherCampos($id_campo, $valor){
echo '<script language="JavaScript" type="text/javascript">document.getElementById("'.$id_campo.'").value = "'.$valor.'";</script>';

}

/*Recebe um texto para aexibir no alert e redireciona para uma pagina. exemplo ai final de um update, exibe a msg de concluido e redireciona para a consulta*/
function _JS_Alerta_Redireciona($msg,$pagina){
echo '<script language="JavaScript" type="text/javascript">
		alert("'.$msg.'"); 
		location.href ="'.$pagina.'";
	</script>';
}

function ehFds($data, &$msg) {
    if(date('N', strtotime($data)) == 6){
    	$msg .= '+1 dia porque dia ('.date('d-m-Y', strtotime($data)).') é Sábado.<br>';
    	return true;
    }else if(date('N', strtotime($data)) > 6){
    	$msg .= '+1 dia porque dia ('.date('d-m-Y', strtotime($data)).') é Domingo.<br>';
    	return true;
    }else{
    	return false;
    }
}

function ehFeriado($data, &$msg) {
    $pdo = _conectaBD();
    $sql = $pdo->prepare("SELECT * FROM feriados where data=:data");
    $sql->bindParam(':data', $data);
    $sql->execute();

    if ($sql->rowCount() > 0){
    	$msg .= '+1 dia porque dia ('.date('d-m-Y', strtotime($data)).') é Feriado.<br>';
    	return true;
    }else{
    	return false;
    }
}

function dataCompensacao($data){
	$msg = '';
	while(ehFeriado($data, $msg) || ehFds($data, $msg) ){
		$data = date('Y-m-d', strtotime($data . ' +1 day'));
	}

    // Adiciona dias informado no código de compensação
    
	return $nova_data = array('data' => $data , 'log' => $msg);
}

function dataCompFinal($data, $cod_comp){
    $data_comp = dataCompensacao($data);

    if ($cod_comp > 0){
        if ($cod_comp == 1){
            $data_comp['data'] = date('Y-m-d', strtotime($data_comp['data'] . ' +'.$cod_comp.' day'));
            $data_comp['log'] .= '+ '.$cod_comp.' dia pelo Código de Compensação.<br>';   
        }
        else{
            $data_comp['data'] = date('Y-m-d', strtotime($data_comp['data'] . ' +'.$cod_comp.' day'));
            $data_comp['log'] .= '+ '.$cod_comp.' dias pelo Código de Compensação.<br>';
        }
        
    }
    //Verifica se após a adição dos dias de compensação não caiu em fim de semana ou feriado novmaente
    $data_final = dataCompensacao($data_comp['data']);
    
    //Concatena o log da primeira verificaçAo com a segunda
    $data_comp['log'] .= $data_final['log'];
    
    //Sobrepoe a data final na variável $data_comp após a verificação acima 
    $data_comp['data'] = $data_final['data'];
    return $data_comp;
}
?>