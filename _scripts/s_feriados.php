<script>
function campo_vazio() {
    var nome,data;
    
    
    nome = document.getElementById('nome').value;
    data = document.getElementById('data').value;

    if (nome == "") {
        alert("O campo 'Nome' não pode ser vazio!");
        document.getElementById("nome").focus();
        return false;
    }else if (data == ""){
    	alert("O campo 'Data' não pode ser vazio!");
        document.getElementById("data").focus();
        return false;
    }else{
    	return true;
    }
}

function atualizar(){
    document.getElementById('upd').value = 1;
}	
</script>