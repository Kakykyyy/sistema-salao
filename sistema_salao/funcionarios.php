<?php
/**
 * Gerenciamento de Funcionários — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Funcionários — ' . APP_NAME;
$feedback  = '';

// ── Salvar ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $id       = (int)($_POST['id'] ?? 0);
    $nome     = sanitizeInput($_POST['nome']     ?? '');
    $telefone = sanitizeInput($_POST['telefone'] ?? '');
    $email    = sanitizeInput($_POST['email']    ?? '');
    $comissao = normalizePercent($_POST['comissao_padrao'] ?? '0');

    $errors = [];
    if (strlen($nome) < 2)             $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
    if ($email    && !validateEmail($email))   $errors[] = 'E-mail inválido.';
    if ($telefone && !validatePhone($telefone)) $errors[] = 'Telefone inválido.';

    if (empty($errors)) {
        if ($id > 0) {
            $ok = $db->execute(
                'UPDATE funcionarios SET nome=?, telefone=?, email=?, comissao_padrao=? WHERE id=?',
                [$nome, $telefone, $email, $comissao, $id]
            );
            $feedback = $ok !== false ? 'Funcionário atualizado!' : 'Erro ao atualizar funcionário.';
        } else {
            $ok = $db->execute(
                'INSERT INTO funcionarios (nome, telefone, email, comissao_padrao, ativo) VALUES (?,?,?,?,1)',
                [$nome, $telefone, $email, $comissao]
            );
            $feedback = $ok !== false ? 'Funcionário cadastrado com sucesso!' : 'Erro ao cadastrar funcionário.';
        }
    } else {
        $feedback = implode('<br>', $errors);
    }
}

// ── Desativar ───────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $has = $db->fetchOne('SELECT COUNT(*) AS n FROM agendamentos WHERE funcionario_id=? AND status="agendado"', [$id]);
    if (($has['n'] ?? 0) > 0) {
        $feedback = 'Erro: funcionário possui agendamentos ativos.';
    } else {
        $ok = $db->execute('UPDATE funcionarios SET ativo=0 WHERE id=?', [$id]);
        $feedback = $ok !== false ? 'Funcionário removido.' : 'Erro ao remover funcionário.';
    }
}

// ── Carregar para edição ────────────────────────────────────
$editing     = false;
$funcionario = ['id'=>0,'nome'=>'','telefone'=>'','email'=>'','comissao_padrao'=>0];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $row = $db->fetchOne('SELECT * FROM funcionarios WHERE id=? AND ativo=1', [(int)$_GET['edit']]);
    if ($row) { $funcionario = $row; $editing = true; }
}

// ── Listagem ────────────────────────────────────────────────
$search = sanitizeInput($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * ITEMS_PER_PAGE;
$params = [];
$where  = 'WHERE ativo=1';

if ($search !== '') {
    $where .= ' AND (nome LIKE ? OR telefone LIKE ? OR email LIKE ?)';
    $s      = "%$search%";
    $params = [$s, $s, $s];
}

$total        = (int)($db->fetchOne("SELECT COUNT(*) AS n FROM funcionarios $where", $params)['n'] ?? 0);
$totalPages   = max(1, (int)ceil($total / ITEMS_PER_PAGE));
$funcionarios = $db->fetchAll(
    "SELECT * FROM funcionarios $where ORDER BY nome LIMIT ? OFFSET ?",
    array_merge($params, [ITEMS_PER_PAGE, $offset])
);

include 'header.php';
?>

<section class="panel">
    <h2><?= $editing ? '✏️ Editar funcionário' : '👩‍💼 Cadastrar funcionário' ?></h2>
    <form method="POST" class="form-grid" novalidate>
        <input type="hidden" name="id" value="<?= (int)$funcionario['id'] ?>">
        <label>Nome completo *
            <input type="text" name="nome" required maxlength="255" value="<?= h($funcionario['nome']) ?>">
        </label>
        <label>Telefone
            <input type="tel" name="telefone" maxlength="20" value="<?= h($funcionario['telefone'] ?? '') ?>">
        </label>
        <label>E-mail
            <input type="email" name="email" maxlength="255" value="<?= h($funcionario['email'] ?? '') ?>">
        </label>
        <label>Comissão padrão (%)
            <input type="number" name="comissao_padrao" min="0" max="100" step="0.01"
                   value="<?= h($funcionario['comissao_padrao']) ?>">
        </label>
        <div class="form-actions">
            <button type="submit" name="salvar" class="btn">
                <?= $editing ? 'Salvar alterações' : 'Cadastrar funcionário' ?>
            </button>
            <?php if ($editing): ?>
                <a href="funcionarios.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>📋 Funcionários cadastrados</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por nome, telefone ou e-mail…" value="<?= h($search) ?>">
            <button type="submit" class="btn">Buscar</button>
            <?php if ($search): ?><a href="funcionarios.php" class="btn btn-secondary">Limpar</a><?php endif; ?>
        </form>
    </div>

    <?php if (empty($funcionarios)): ?>
        <p class="empty-state">Nenhum funcionário encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nome</th><th>Telefone</th><th>E-mail</th><th>Comissão</th><th>Cadastro</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $f): ?>
                    <tr>
                        <td><?= h($f['nome']) ?></td>
                        <td><?= h($f['telefone'] ?? '—') ?></td>
                        <td><?= h($f['email'] ?? '—') ?></td>
                        <td><?= number_format((float)$f['comissao_padrao'],2,',','.')?>%</td>
                        <td><?= formatDate($f['data_cadastro']) ?></td>
                        <td class="actions">
                            <a href="funcionarios.php?edit=<?= $f['id'] ?>" class="btn-small">Editar</a>
                            <a href="funcionarios.php?delete=<?= $f['id'] ?>"
                               onclick="return confirm('Remover este funcionário?')"
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
            <span>Página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)</span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="btn-small">Próxima →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
