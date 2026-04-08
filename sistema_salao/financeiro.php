<?php
/**
 * Controle Financeiro — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Financeiro — ' . APP_NAME;
$feedback  = '';

// ── Salvar lançamento ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $descricao = sanitizeInput($_POST['descricao'] ?? '');
    $categoria = sanitizeInput($_POST['categoria'] ?? '');
    $tipo      = sanitizeInput($_POST['tipo']      ?? '');
    $valor     = normalizeCurrency($_POST['valor'] ?? '0');
    $data      = sanitizeInput($_POST['data']      ?? '');

    $errors = [];
    if (strlen($descricao) < 3)                   $errors[] = 'Descrição deve ter pelo menos 3 caracteres.';
    if (!in_array($tipo, ['entrada','saida']))     $errors[] = 'Tipo inválido.';
    if ($valor <= 0)                               $errors[] = 'Valor deve ser maior que zero.';
    if (!validateDate($data))                      $errors[] = 'Data inválida.';

    if (empty($errors)) {
        $ok = $db->execute(
            'INSERT INTO financeiro (descricao, categoria, valor, tipo, data) VALUES (?,?,?,?,?)',
            [$descricao, $categoria, $valor, $tipo, $data]
        );
        $feedback = $ok !== false ? 'Lançamento salvo com sucesso!' : 'Erro ao salvar lançamento.';
    } else {
        $feedback = implode('<br>', $errors);
    }
}

// ── Excluir ─────────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $ok = $db->execute('DELETE FROM financeiro WHERE id=?', [(int)$_GET['delete']]);
    $feedback = $ok !== false ? 'Lançamento removido.' : 'Erro ao remover lançamento.';
}

// ── Totais ──────────────────────────────────────────────────
$totais   = $db->fetchOne("SELECT
    COALESCE(SUM(CASE WHEN tipo='entrada' THEN valor END),0) AS entradas,
    COALESCE(SUM(CASE WHEN tipo='saida'   THEN valor END),0) AS saidas
    FROM financeiro");
$entradas = (float)($totais['entradas'] ?? 0);
$saidas   = (float)($totais['saidas']   ?? 0);
$saldo    = $entradas - $saidas;

// ── Totais de Comissões ──────────────────────────────────────
$comissoesTotais = $db->fetchOne("SELECT
    COALESCE(SUM(CASE WHEN tipo='saida' AND categoria='Comissões' THEN valor END),0) AS comissoes_pagas
    FROM financeiro");
$comissoesPagas = (float)($comissoesTotais['comissoes_pagas'] ?? 0);

// ── Resumo de Comissões ─────────────────────────────────────
$comissoesRecentes = $db->fetchAll(
    "SELECT descricao, valor, data FROM financeiro 
     WHERE categoria='Comissões' AND tipo='saida' 
     ORDER BY data DESC LIMIT 10"
);
$search      = sanitizeInput($_GET['search'] ?? '');
$tipoFiltro  = sanitizeInput($_GET['tipo']   ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($page - 1) * ITEMS_PER_PAGE;
$params      = [];
$where       = 'WHERE 1=1';

if ($search !== '') {
    $where  .= ' AND (descricao LIKE ? OR categoria LIKE ?)';
    $s       = "%$search%";
    $params  = [$s, $s];
}
if (in_array($tipoFiltro, ['entrada','saida'])) {
    $where  .= ' AND tipo=?';
    $params[] = $tipoFiltro;
}

$total      = (int)($db->fetchOne("SELECT COUNT(*) AS n FROM financeiro $where", $params)['n'] ?? 0);
$totalPages = max(1, (int)ceil($total / ITEMS_PER_PAGE));
$lancamentos= $db->fetchAll(
    "SELECT * FROM financeiro $where ORDER BY data DESC, data_cadastro DESC LIMIT ? OFFSET ?",
    array_merge($params, [ITEMS_PER_PAGE, $offset])
);

include 'header.php';
?>

<section class="panel">
    <h2>💰 Novo lançamento</h2>
    <form method="POST" class="form-grid" novalidate>
        <label>Descrição *
            <input type="text" name="descricao" required maxlength="255"
                   placeholder="Ex: Venda de produto, Conta de luz…">
        </label>
        <label>Categoria
            <input type="text" name="categoria" maxlength="100"
                   placeholder="Ex: Produtos, Contas, Serviços…">
        </label>
        <label>Tipo *
            <select name="tipo" required>
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
            </select>
        </label>
        <label>Valor (R$) *
            <input type="number" name="valor" required min="0.01" step="0.01" placeholder="0,00">
        </label>
        <label>Data *
            <input type="date" name="data" required value="<?= date('Y-m-d') ?>">
        </label>
        <div class="form-actions">
            <button type="submit" name="salvar" class="btn">Salvar lançamento</button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>📊 Resumo financeiro</h2>
    <div class="dashboard-grid">
        <div class="card success">
            <strong><?= formatCurrency($entradas) ?></strong>
            <span>Entradas</span>
        </div>
        <div class="card danger">
            <strong><?= formatCurrency($saidas) ?></strong>
            <span>Saídas</span>
        </div>
        <div class="card <?= $saldo >= 0 ? 'info' : 'warning' ?>">
            <strong><?= formatCurrency($saldo) ?></strong>
            <span>Saldo</span>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>📋 Lançamentos</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por descrição ou categoria…"
                   value="<?= h($search) ?>">
            <select name="tipo">
                <option value="">Todos os tipos</option>
                <option value="entrada" <?= $tipoFiltro==='entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="saida"   <?= $tipoFiltro==='saida'   ? 'selected' : '' ?>>Saída</option>
            </select>
            <button type="submit" class="btn">Filtrar</button>
            <?php if ($search || $tipoFiltro): ?>
                <a href="financeiro.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($lancamentos)): ?>
        <p class="empty-state">Nenhum lançamento encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Data</th><th>Descrição</th><th>Categoria</th><th>Tipo</th><th>Valor</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($lancamentos as $l): ?>
                    <tr>
                        <td><?= formatDate($l['data']) ?></td>
                        <td><?= h($l['descricao']) ?></td>
                        <td><?= h($l['categoria'] ?? '—') ?></td>
                        <td><span class="status status-<?= h($l['tipo']) ?>"><?= ucfirst(h($l['tipo'])) ?></span></td>
                        <td><?= formatCurrency((float)$l['valor']) ?></td>
                        <td class="actions">
                            <a href="?delete=<?= $l['id'] ?>&search=<?= urlencode($search) ?>&tipo=<?= urlencode($tipoFiltro) ?>&page=<?= $page ?>"
                               onclick="return confirm('Remover este lançamento?')"
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
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&tipo=<?= urlencode($tipoFiltro) ?>" class="btn-small">← Anterior</a>
            <?php endif; ?>
            <span>Página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)</span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&tipo=<?= urlencode($tipoFiltro) ?>" class="btn-small">Próxima →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php if (!empty($comissoesRecentes)): ?>
<section class="panel">
    <h2>💰 Resumo de Comissões</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comissoesRecentes as $comissao): ?>
                <tr>
                    <td><?= h($comissao['descricao']) ?></td>
                    <td class="amount negative">R$ <?= number_format($comissao['valor'], 2, ',', '.') ?></td>
                    <td><?= formatDate($comissao['data']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
