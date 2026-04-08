<?php
/**
 * Agendamentos — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Agendamentos — ' . APP_NAME;
$feedback  = '';

// ── Novo agendamento ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
    $cliente_id    = (int)($_POST['cliente_id']    ?? 0);
    $servico_id    = (int)($_POST['servico_id']    ?? 0);
    $funcionario_id= (int)($_POST['funcionario_id'] ?? 0);
    $data          = sanitizeInput($_POST['data']  ?? '');
    $hora          = sanitizeInput($_POST['hora']  ?? '');
    $obs           = sanitizeInput($_POST['observacoes'] ?? '');

    $errors = [];
    if ($cliente_id    <= 0) $errors[] = 'Cliente é obrigatório.';
    if ($servico_id    <= 0) $errors[] = 'Serviço é obrigatório.';
    if ($funcionario_id<= 0) $errors[] = 'Funcionário é obrigatório.';
    if (!validateDate($data))  $errors[] = 'Data inválida.';
    elseif ($data < date('Y-m-d')) $errors[] = 'Data não pode ser no passado.';
    if (!validateTime($hora))  $errors[] = 'Horário inválido.';

    if (empty($errors)) {
        $ocupado = $db->fetchOne(
            'SELECT id FROM agendamentos WHERE funcionario_id=? AND data_agendamento=? AND hora_agendamento=? AND status!="cancelado"',
            [$funcionario_id, $data, $hora]
        );
        if ($ocupado) $errors[] = 'Horário já ocupado para este funcionário.';
    }

    if (empty($errors)) {
        $serv = $db->fetchOne('SELECT preco, comissao FROM servicos WHERE id=?', [$servico_id]);
        $func = $db->fetchOne('SELECT comissao_padrao FROM funcionarios WHERE id=?', [$funcionario_id]);
        $valor     = (float)($serv['preco']    ?? 0);
        $pctComis  = (float)($serv['comissao'] > 0 ? $serv['comissao'] : ($func['comissao_padrao'] ?? 0));
        $comissao  = round($valor * $pctComis / 100, 2);

        $ok = $db->execute(
            'INSERT INTO agendamentos (cliente_id, servico_id, funcionario_id, data_agendamento, hora_agendamento, valor, comissao, observacoes, status)
             VALUES (?,?,?,?,?,?,?,?,"agendado")',
            [$cliente_id, $servico_id, $funcionario_id, $data, $hora, $valor, $comissao, $obs]
        );
        $feedback = $ok !== false ? 'Agendamento realizado com sucesso!' : 'Erro ao realizar agendamento.';
    } else {
        $feedback = implode('<br>', $errors);
    }
}

// ── Ações de status ─────────────────────────────────────────
foreach (['concluir' => 'concluido', 'cancelar' => 'cancelado'] as $action => $status) {
    if (isset($_GET[$action]) && is_numeric($_GET[$action])) {
        $id = (int)$_GET[$action];
        $ok = $db->execute('UPDATE agendamentos SET status=? WHERE id=?', [$status, $id]);
        if ($ok !== false && $action === 'concluir') {
            // Inserir no financeiro quando concluir
            $ag = $db->fetchOne('SELECT valor, comissao, data_agendamento FROM agendamentos WHERE id=?', [$id]);
            if ($ag) {
                $data = $ag['data_agendamento'];
                $valor = $ag['valor'];
                $comissao = $ag['comissao'];
                // Entrada para o serviço
                $db->execute(
                    'INSERT INTO financeiro (descricao, categoria, valor, tipo, data) VALUES (?, "Serviços", ?, "entrada", ?)',
                    ["Serviço realizado - ID $id", $valor, $data]
                );
                // Saída para a comissão
                if ($comissao > 0) {
                    $db->execute(
                        'INSERT INTO financeiro (descricao, categoria, valor, tipo, data) VALUES (?, "Comissões", ?, "saida", ?)',
                        ["Comissão - ID $id", $comissao, $data]
                    );
                }
            }
        }
        $feedback = $ok !== false ? 'Status atualizado.' : 'Erro ao atualizar status.';
    }
}

// ── Listagem ────────────────────────────────────────────────
$search  = sanitizeInput($_GET['search'] ?? '');
$status  = sanitizeInput($_GET['status'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * ITEMS_PER_PAGE;
$params  = [];
$where   = 'WHERE 1=1';

if ($search !== '') {
    $where  .= ' AND (c.nome LIKE ? OR s.nome LIKE ? OR f.nome LIKE ?)';
    $s       = "%$search%";
    $params  = array_merge($params, [$s, $s, $s]);
}
if (in_array($status, ['agendado','concluido','cancelado'])) {
    $where  .= ' AND a.status = ?';
    $params[] = $status;
}

$countSql   = "SELECT COUNT(*) AS n FROM agendamentos a
               JOIN clientes c ON a.cliente_id=c.id
               JOIN servicos  s ON a.servico_id=s.id
               JOIN funcionarios f ON a.funcionario_id=f.id $where";
$total      = (int)($db->fetchOne($countSql, $params)['n'] ?? 0);
$totalPages = max(1, (int)ceil($total / ITEMS_PER_PAGE));

$agendamentos = $db->fetchAll(
    "SELECT a.*, c.nome AS cliente_nome, s.nome AS servico_nome, f.nome AS funcionario_nome
     FROM agendamentos a
     JOIN clientes c ON a.cliente_id=c.id
     JOIN servicos  s ON a.servico_id=s.id
     JOIN funcionarios f ON a.funcionario_id=f.id
     $where ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
     LIMIT ? OFFSET ?",
    array_merge($params, [ITEMS_PER_PAGE, $offset])
);

// Listas para o formulário
$clientes     = $db->fetchAll('SELECT id, nome FROM clientes     WHERE ativo=1 ORDER BY nome');
$servicos     = $db->fetchAll('SELECT id, nome, preco FROM servicos    WHERE ativo=1 ORDER BY nome');
$funcionarios = $db->fetchAll('SELECT id, nome FROM funcionarios WHERE ativo=1 ORDER BY nome');

include 'header.php';
?>

<section class="panel">
    <h2>📅 Novo agendamento</h2>
    <form method="POST" class="form-grid" novalidate>
        <label>Cliente *
            <select name="cliente_id" required>
                <option value="">Selecione um cliente</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= h($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Serviço *
            <select name="servico_id" required>
                <option value="">Selecione um serviço</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= h($s['nome']) ?> — <?= formatCurrency((float)$s['preco']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Funcionário *
            <select name="funcionario_id" required>
                <option value="">Selecione um funcionário</option>
                <?php foreach ($funcionarios as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= h($f['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Data *
            <input type="date" name="data" required min="<?= date('Y-m-d') ?>">
        </label>
        <label>Horário *
            <input type="time" name="hora" required>
        </label>
        <label style="grid-column: 1 / -1">Observações
            <textarea name="observacoes" rows="2" maxlength="500"></textarea>
        </label>
        <div class="form-actions">
            <button type="submit" name="agendar" class="btn">Confirmar agendamento</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>📋 Lista de agendamentos</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por cliente, serviço ou funcionário…"
                   value="<?= h($search) ?>">
            <select name="status">
                <option value="">Todos os status</option>
                <?php foreach (['agendado','concluido','cancelado'] as $st): ?>
                    <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>>
                        <?= ucfirst($st) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Filtrar</button>
            <?php if ($search || $status): ?>
                <a href="agendamento.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($agendamentos)): ?>
        <p class="empty-state">Nenhum agendamento encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Data / Hora</th><th>Cliente</th><th>Serviço</th>
                        <th>Funcionário</th><th>Valor</th><th>Status</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $ag): ?>
                    <tr>
                        <td>
                            <?= formatDate($ag['data_agendamento']) ?><br>
                            <small><?= substr($ag['hora_agendamento'],0,5) ?></small>
                        </td>
                        <td><?= h($ag['cliente_nome']) ?></td>
                        <td><?= h($ag['servico_nome']) ?></td>
                        <td><?= h($ag['funcionario_nome']) ?></td>
                        <td><?= formatCurrency((float)$ag['valor']) ?></td>
                        <td><span class="status status-<?= h($ag['status']) ?>"><?= ucfirst(h($ag['status'])) ?></span></td>
                        <td class="actions">
                            <?php if ($ag['status'] === 'agendado'): ?>
                                <a href="?concluir=<?= $ag['id'] ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page=<?= $page ?>"
                                   onclick="return confirm('Marcar como concluído?')" class="btn-small">Concluir</a>
                                <a href="?cancelar=<?= $ag['id'] ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page=<?= $page ?>"
                                   onclick="return confirm('Cancelar este agendamento?')"
                                   class="btn-small btn-danger">Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="btn-small">← Anterior</a>
            <?php endif; ?>
            <span>Página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)</span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="btn-small">Próxima →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
