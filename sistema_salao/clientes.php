<?php include "conexao.php"; ?>

<h2>Cadastrar Cliente</h2>

<form method="POST">
    Nome: <input type="text" name="nome"><br>
    Telefone: <input type="text" name="telefone"><br>
    Email: <input type="text" name="email"><br>
    <button type="submit" name="salvar">Salvar</button>
    <link rel="stylesheet" href="style.css">
</form>

<?php
if(isset($_POST['salvar'])){
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO clientes (nome, telefone, email)
            VALUES ('$nome','$telefone','$email')";
    $conn->query($sql);
    echo "Cliente cadastrado!";
}
?>

<h2>Clientes</h2>

<?php
$result = $conn->query("SELECT * FROM clientes");
while($row = $result->fetch_assoc()){
    echo $row['nome'] . " - " . $row['telefone'] . "<br>";
}
?>