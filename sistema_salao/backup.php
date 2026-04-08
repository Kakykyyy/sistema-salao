<?php
/**
 * Backup do sistema — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Backup — ' . APP_NAME;
$feedback  = '';
$arquivo   = '';

if (isset($_POST['backup'])) {
    $tables = $db->fetchAll('SHOW TABLES');
    $dados  = [];
    foreach ($tables as $row) {
        $table = array_values($row)[0];
        $dados[$table] = $db->fetchAll("SELECT * FROM `$table`");
    }

    if (!is_dir(BACKUP_PATH)) mkdir(BACKUP_PATH, 0755, true);
    $arquivo = 'backup_' . date('Ymd_His') . '.json';
    $ok = file_put_contents(
        BACKUP_PATH . $arquivo,
        json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
    $feedback = $ok !== false ? 'Backup gerado com sucesso!' : 'Erro ao gerar backup.';
}

// Lista backups existentes
$backups = glob(BACKUP_PATH . 'backup_*.json') ?: [];
rsort($backups);

include 'header.php';
?>

<section class="panel">
    <h2>💾 Backup do sistema</h2>
    <p style="margin-bottom:var(--s-4);color:var(--gray-600)">
        Gera um arquivo JSON completo com todos os dados do banco de dados.
        O arquivo é salvo na pasta <code>backups/</code> do servidor.
    </p>
    <form method="POST">
        <button type="submit" name="backup" class="btn"
                onclick="return confirm('Gerar backup agora?')">
            Gerar backup agora
        </button>
    </form>

    <?php if ($arquivo): ?>
        <p style="margin-top:var(--s-4)">
            ✅ Arquivo gerado:
            <a href="backups/<?= h($arquivo) ?>" download class="btn btn-secondary" style="display:inline-flex;margin-left:8px">
                ⬇ Baixar <?= h($arquivo) ?>
            </a>
        </p>
    <?php endif; ?>
</section>

<?php if (!empty($backups)): ?>
<section class="panel">
    <h2>📁 Backups disponíveis</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Arquivo</th><th>Data</th><th>Tamanho</th><th>Ação</th></tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $b):
                    $name = basename($b);
                    $size = round(filesize($b) / 1024, 1);
                    $mtime= date('d/m/Y H:i', filemtime($b));
                ?>
                <tr>
                    <td><?= h($name) ?></td>
                    <td><?= $mtime ?></td>
                    <td><?= $size ?> KB</td>
                    <td><a href="backups/<?= h($name) ?>" download class="btn-small">⬇ Baixar</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
