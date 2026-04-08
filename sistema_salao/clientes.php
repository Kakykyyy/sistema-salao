<?php
/**
 * Gerenciamento de Clientes — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Clientes — ' . APP_NAME;
$feedback  = '';

// ── Salvar (novo ou edição) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $id       = (int)($_POST['id'] ?? 0);
    $nome     = sanitizeInput($_POST['nome']     ?? '');
    $telefone = sanitizeInput($_POST['telefone'] ?? '');
    $email    = sanitizeInput($_POST['email']    ?? '');

    $errors = [];
    if (strlen($nome) < 2)              $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
    if ($email    && !validateEmail($email))   $errors[] = 'E-mail inválido.';
    if ($telefone && !validatePhone($telefone)) $errors[] = 'Telefone inválido (10 ou 11 dígitos).';

    if (empty($errors)) {
        if ($id > 0) {
            $ok = $db->execute(
                'UPDATE clientes SET nome=?, telefone=?, email=? WHERE id=?',
                [$nome, $telefone, $email, $id]
            );
            $feedback = $ok !== false ? 'Cliente atualizado com sucesso!' : 'Erro ao atualizar cliente.';
        } else {
            $ok = $db->execute(
                'INSERT INTO clientes (nome, telefone, email, ativo) VALUES (?,?,?,1)',
                [$nome, $telefone, $email]
            );
            $feedback = $ok !== false ? 'Cliente cadastrado com sucesso!' : 'Erro ao cadastrar cliente.';
        }
    } else {
        $feedback = implode('<br>', $errors);
    }
}

// ── Desativar ───────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $has = $db->fetchOne('SELECT COUNT(*) AS n FROM agendamentos WHERE cliente_id=?', [$id]);
    if (($has['n'] ?? 0) > 0) {
        $feedback = 'Erro: cliente possui agendamentos e não pode ser removido.';
    } else {
        $ok = $db->execute('UPDATE clientes SET ativo=0 WHERE id=?', [$id]);
        $feedback = $ok !== false ? 'Cliente removido com sucesso.' : 'Erro ao remover cliente.';
    }
}

// ── Carregar para edição ────────────────────────────────────
$editing = false;
$cliente = ['id'=>0,'nome'=>'','telefone'=>'','email'=>''];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $row = $db->fetchOne('SELECT * FROM clientes WHERE id=? AND ativo=1', [(int)$_GET['edit']]);
    if ($row) { $cliente = $row; $editing = true; }
}

// ── Listagem com busca e paginação ──────────────────────────
$search  = sanitizeInput($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * ITEMS_PER_PAGE;
$params  = [];
$where   = 'WHERE ativo=1';

if ($search !== '') {
    $where   .= ' AND (nome LIKE ? OR telefone LIKE ? OR email LIKE ?)';
    $s        = "%$search%";
    $params   = [$s, $s, $s];
}

$total      = (int)($db->fetchOne("SELECT COUNT(*) AS n FROM clientes $where", $params)['n'] ?? 0);
$totalPages = max(1, (int)ceil($total / ITEMS_PER_PAGE));
$clientes   = $db->fetchAll(
    "SELECT * FROM clientes $where ORDER BY nome LIMIT ? OFFSET ?",
    array_merge($params, [ITEMS_PER_PAGE, $offset])
);

include 'header.php';
?>

<section class="panel">
    <h2><?= $editing ? '✏️ Editar cliente' : '👥 Cadastrar cliente' ?></h2>
    <form method="POST" class="form-grid" novalidate>
        <input type="hidden" name="id" value="<?= (int)$cliente['id'] ?>">
        <label>Nome completo *
            <input type="text" name="nome" required maxlength="255"
                   value="<?= h($cliente['nome']) ?>">
        </label>
        <label>Telefone
            <input type="tel" name="telefone" maxlength="20"
                   value="<?= h($cliente['telefone'] ?? '') ?>">
        </label>
        <label>E-mail
            <input type="email" name="email" maxlength="255"
                   value="<?= h($cliente['email'] ?? '') ?>">
        </label>
        <div class="form-actions">
            <button type="submit" name="salvar" class="btn">
                <?= $editing ? 'Salvar alterações' : 'Cadastrar cliente' ?>
            </button>
            <?php if ($editing): ?>
                <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>📋 Clientes cadastrados</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por nome, telefone ou e-mail..."
                   value="<?= h($search) ?>">
            <button type="submit" class="btn">Buscar</button>
            <?php if ($search): ?>
                <a href="clientes.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($clientes)): ?>
        <p class="empty-state">Nenhum cliente encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th><th>Telefone</th><th>E-mail</th><th>Cadastro</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= h($c['nome']) ?></td>
                        <td><?= h($c['telefone'] ?? '—') ?></td>
                        <td><?= h($c['email'] ?? '—') ?></td>
                        <td><?= formatDate($c['data_cadastro']) ?></td>
                        <td class="actions">
                            <a href="clientes.php?edit=<?= $c['id'] ?>" class="btn-small">Editar</a>
                            <a href="clientes.php?delete=<?= $c['id'] ?>"
                               onclick="return confirm('Remover este cliente?')"
                               class="btn-small btn-danger">Remover</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="btn-small">← Anterior</a>
            <?php endif; ?>
            <span>Página <?= $page ?> de <?= $totalPages ?> &nbsp;(<?= $total ?> registros)</span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="btn-small">Próxima →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
