<script>
function mostrarLogo() {
    var banco;
    
    banco = document.getElementById('sel_banco').value;
    document.getElementById('logo').src = '_arquivos/_bancos/_logo_'+banco+'.png';
}

function simular(){

	var valor, rec_em, bom_para, taxa;

	valor = document.getElementById('valor').value;
	taxa = document.getElementById('taxa').value;
	rec_em = document.getElementById('recebido_em').value;
	bom_para = document.getElementById('bom_para').value;

	if (rec_em != '' && bom_para != ''){
		rec_em = new Date(rec_em.split('/').reverse().join('/'));
		bom_para = new Date(bom_para.split('/').reverse().join('/'));
	}else{
		rec_em = '';
		bom_para = '';
	}

	

	if (valor == '' || rec_em == '' || bom_para == '' || taxa == ''){
		alert('Os campos VALOR, RECEBIDO EM, BOM PARA e TAXA são obrigatórios!');
		return false;
	}

    if (rec_em > bom_para) {
        alert("A data RECEBIDO EM não pode ser maior que a data BOM PARA!");
        return false;    
    } 
    if (document.getElementById('salvar').value != 1){
		document.getElementById('opcao').value = 1;
    }
	return true;
}
function corrigirCadastro(){
	document.precadastro.submit();
	document.getElementById('cadastro').value = 1;

}

function aprovarCadastro(){
	document.getElementById('cadastro').value = 2;
	document.precadastro.submit();


}

function cadastrar(){

	var banco, agencia, num_cheque, conta, cliente;

	banco = document.getElementById('sel_banco').value;
	agencia = document.getElementById('agencia').value;
	num_cheque = document.getElementById('cheque_num').value;
	conta = document.getElementById('conta').value;
	cliente = document.getElementById('cliente').value;

	document.getElementsByName('data_comp')[0].disabled = false;
    document.getElementsByName('dias_correcao')[0].disabled = false;
    document.getElementsByName('valor_corrigido')[0].disabled = false;
    document.getElementsByName('receita')[0].disabled = false; 

	if (cliente == '' || banco == 0 || agencia == '' || num_cheque == '' || conta == ''){
		alert('Os campos CLIENTE, BANCO, AGÊNCIA, CONTA e NUMERO DO CHEQUE são obrigatórios!');
		return false;
	}else{
		document.getElementById('salvar').value = 1;
		return simular();
	}
	document.getElementById('salvar').value = 1;
	return true;
}

function cadastrarGrupo(){
	document.getElementById('salvar').value = 1;
	return true;
}

function roundTo(n, digits) {
        if (digits === undefined) {
            digits = 0;
        }

        var multiplicator = Math.pow(10, digits);
        n = parseFloat((n * multiplicator).toFixed(11));
        return Math.round(n) / multiplicator;
}

function arredondamento(){
	var valor, novo;
	valor = document.getElementById('valor_corrigido').value;
	
	novo = roundTo(valor,1);
	alert(novo);
	
}

function mostrarDetalhes(){
	if (document.getElementById('info-comp').style.display == 'none'){
		document.getElementById('info-comp').style.display = 'block';
	}else{
		document.getElementById('info-comp').style.display = 'none';
	}
}
</script>