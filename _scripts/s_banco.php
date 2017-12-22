<script>
function campo_vazio() {
    var codigo,nome,arquivo;
    
    codigo = document.getElementById('codigo').value;
    nome = document.getElementById('nome').value;
    arquivo = document.getElementById('file').value;
    ok = document.getElementById('logoOK').value;

    if (codigo == "") {
        alert("O campo 'Código' não pode ser vazio!");
        document.getElementById("codigo").focus();
        return false;
    }else if (nome == ""){
    	alert("O campo 'Nome' não pode ser vazio!");
        document.getElementById("nome").focus();
        return false;
    }else if (arquivo == "" && ok == "1"){
    	alert("É necessário enviar uma logomarca!");
        document.getElementById("file").focus();
        return false;
    }else{
    	return true;
    }
}	
</script>