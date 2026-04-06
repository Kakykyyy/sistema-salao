<?php include "conexao.php"; ?>

<h2>Lançamento Financeiro</h2>

<form method="POST">
    Descrição: <input type="text" name="descricao"><br>
    Valor: <input type="text" name="valor"><br>
    Tipo:
    <select name="tipo">
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
    </select><br>
    Data: <input type="date" name="data"><br>

    <button type="submit" name="salvar">Salvar</button>
</form>

<?php
if(isset($_POST['salvar'])){
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo'];
    $data = $_POST['data'];

    $sql = "INSERT INTO financeiro (descricao, valor, tipo, data)
            VALUES ('$descricao','$valor','$tipo','$data')";
    $conn->query($sql);
    echo "Lançamento salvo!";
}
?>