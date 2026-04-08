<?php
/**
 * Gerenciamento de Serviços — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Serviços — ' . APP_NAME;
$feedback  = '';

// ── Salvar ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $id       = (int)($_POST['id'] ?? 0);
    $nome     = sanitizeInput($_POST['nome']    ?? '');
    $duracao  = sanitizeInput($_POST['duracao'] ?? '');
    $preco    = normalizeCurrency($_POST['preco']    ?? '0');
    $comissao = normalizePercent($_POST['comissao']  ?? '0');
    $descricao= sanitizeInput($_POST['descricao'] ?? '');

    $errors = [];
    if (strlen($nome) < 2) $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
    if (!$duracao)          $errors[] = 'Duração é obrigatória.';
    if ($preco <= 0)        $errors[] = 'Preço deve ser maior que zero.';

    if (empty($errors)) {
        if ($id > 0) {
            $ok = $db->execute(
                'UPDATE servicos SET nome=?, descricao=?, duracao=?, preco=?, comissao=? WHERE id=?',
                [$nome, $descricao, $duracao, $preco, $comissao, $id]
            );
            $feedback = $ok !== false ? 'Serviço atualizado!' : 'Erro ao atualizar serviço.';
        } else {
            // Verifica duplicidade
            $dup = $db->fetchOne('SELECT id FROM servicos WHERE nome=? AND ativo=1', [$nome]);
            if ($dup) {
                $feedback = 'Erro: já existe um serviço com este nome.';
            } else {
                $ok = $db->execute(
                    'INSERT INTO servicos (nome, descricao, duracao, preco, comissao, ativo) VALUES (?,?,?,?,?,1)',
                    [$nome, $descricao, $duracao, $preco, $comissao]
                );
                $feedback = $ok !== false ? 'Serviço cadastrado com sucesso!' : 'Erro ao cadastrar serviço.';
            }
        }
    } else {
        $feedback = implode('<br>', $errors);
    }
}

// ── Desativar ───────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $has = $db->fetchOne('SELECT COUNT(*) AS n FROM agendamentos WHERE servico_id=? AND status="agendado"', [$id]);
    if (($has['n'] ?? 0) > 0) {
        $feedback = 'Erro: serviço possui agendamentos ativos.';
    } else {
        $ok = $db->execute('UPDATE servicos SET ativo=0 WHERE id=?', [$id]);
        $feedback = $ok !== false ? 'Serviço removido.' : 'Erro ao remover serviço.';
    }
}

// ── Carregar para edição ────────────────────────────────────
$editing = false;
$servico = ['id'=>0,'nome'=>'','descricao'=>'','duracao'=>'','preco'=>0,'comissao'=>0];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $row = $db->fetchOne('SELECT * FROM servicos WHERE id=? AND ativo=1', [(int)$_GET['edit']]);
    if ($row) { $servico = $row; $editing = true; }
}

// ── Listagem ────────────────────────────────────────────────
$search = sanitizeInput($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * ITEMS_PER_PAGE;
$params = [];
$where  = 'WHERE ativo=1';

if ($search !== '') {
    $where  .= ' AND (nome LIKE ? OR duracao LIKE ?)';
    $s       = "%$search%";
    $params  = [$s, $s];
}

$total      = (int)($db->fetchOne("SELECT COUNT(*) AS n FROM servicos $where", $params)['n'] ?? 0);
$totalPages = max(1, (int)ceil($total / ITEMS_PER_PAGE));
$servicos   = $db->fetchAll(
    "SELECT * FROM servicos $where ORDER BY nome LIMIT ? OFFSET ?",
    array_merge($params, [ITEMS_PER_PAGE, $offset])
);

include 'header.php';
?>

<section class="panel">
    <h2><?= $editing ? '✏️ Editar serviço' : '💅 Cadastrar serviço' ?></h2>
    <form method="POST" class="form-grid" novalidate>
        <input type="hidden" name="id" value="<?= (int)$servico['id'] ?>">
        <label>Nome do serviço *
            <input type="text" name="nome" required maxlength="255" value="<?= h($servico['nome']) ?>">
        </label>
        <label>Duração *
            <input type="text" name="duracao" required maxlength="50" placeholder="Ex: 1h30min"
                   value="<?= h($servico['duracao']) ?>">
        </label>
        <label>Preço (R$) *
            <input type="text" name="preco" required placeholder="0,00"
                   value="<?= $servico['preco'] > 0 ? number_format((float)$servico['preco'],2,',','.') : '' ?>">
        </label>
        <label>Comissão (%)
            <input type="number" name="comissao" min="0" max="100" step="0.01"
                   value="<?= h($servico['comissao']) ?>">
        </label>
        <label style="grid-column: 1 / -1">Descrição
            <textarea name="descricao" rows="2" maxlength="500"><?= h($servico['descricao'] ?? '') ?></textarea>
        </label>
        <div class="form-actions">
            <button type="submit" name="salvar" class="btn">
                <?= $editing ? 'Salvar alterações' : 'Cadastrar serviço' ?>
            </button>
            <?php if ($editing): ?>
                <a href="servicos.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>📋 Serviços cadastrados</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por nome ou duração…" value="<?= h($search) ?>">
            <button type="submit" class="btn">Buscar</button>
            <?php if ($search): ?><a href="servicos.php" class="btn btn-secondary">Limpar</a><?php endif; ?>
        </form>
    </div>

    <?php if (empty($servicos)): ?>
        <p class="empty-state">Nenhum serviço encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nome</th><th>Duração</th><th>Preço</th><th>Comissão</th><th>Cadastro</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $s): ?>
                    <tr>
                        <td><?= h($s['nome']) ?></td>
                        <td><?= h($s['duracao']) ?></td>
                        <td><?= formatCurrency((float)$s['preco']) ?></td>
                        <td><?= number_format((float)$s['comissao'],2,',','.')?>%</td>
                        <td><?= formatDate($s['data_cadastro']) ?></td>
                        <td class="actions">
                            <a href="servicos.php?edit=<?= $s['id'] ?>" class="btn-small">Editar</a>
                            <a href="servicos.php?delete=<?= $s['id'] ?>"
                               onclick="return confirm('Remover este serviço?')"
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
