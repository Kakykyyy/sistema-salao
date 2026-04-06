<?php include "conexao.php"; ?>

<h2>Agendar Horário</h2>

<form method="POST">
    Cliente:
    <select name="cliente">
        <?php
        $clientes = $conn->query("SELECT * FROM clientes");
        while($c = $clientes->fetch_assoc()){
            echo "<option value='{$c['id']}'>{$c['nome']}</option>";
        }
        ?>
        <link rel="stylesheet" href="style.css">
    </select><br>

    Serviço:
    <select name="servico">
        <?php
        $servicos = $conn->query("SELECT * FROM servicos");
        while($s = $servicos->fetch_assoc()){
            echo "<option value='{$s['id']}'>{$s['nome']}</option>";
        }
        ?>
    </select><br>

    Data: <input type="date" name="data"><br>
    Hora: <input type="time" name="hora"><br>
    Observação: <input type="text" name="obs"><br>

    <button type="submit" name="agendar">Agendar</button>
</form>

<?php
if(isset($_POST['agendar'])){
    $cliente = $_POST['cliente'];
    $servico = $_POST['servico'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $obs = $_POST['obs'];

    $sql = "INSERT INTO agendamentos (cliente_id, servico_id, data, hora, observacao)
            VALUES ('$cliente','$servico','$data','$hora','$obs')";
    $conn->query($sql);
    echo "Agendado com sucesso!";
}
?>