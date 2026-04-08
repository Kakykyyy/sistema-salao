<?php
/**
 * Relatórios — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Relatórios — ' . APP_NAME;
$feedback  = '';

$mesAtual = date('Y-m');
$mes      = sanitizeInput($_GET['mes'] ?? $mesAtual);
if (!preg_match('/^\d{4}-\d{2}$/', $mes)) $mes = $mesAtual;
$inicio   = $mes . '-01';
$fim      = date('Y-m-t', strtotime($inicio));

// ── Resumo financeiro do mês ────────────────────────────────
$resumoFin = $db->fetchOne("
    SELECT
        COALESCE(SUM(CASE WHEN tipo='entrada' THEN valor END),0) AS entradas,
        COALESCE(SUM(CASE WHEN tipo='saida'   THEN valor END),0) AS saidas,
        COUNT(*) AS lancamentos
    FROM financeiro WHERE data BETWEEN ? AND ?
", [$inicio, $fim]);

// ── Agendamentos do mês ─────────────────────────────────────
$resumoAg = $db->fetchOne("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='concluido' THEN 1 ELSE 0 END) AS concluidos,
        SUM(CASE WHEN status='cancelado' THEN 1 ELSE 0 END) AS cancelados,
        COALESCE(SUM(CASE WHEN status='concluido' THEN valor END),0) AS faturamento
    FROM agendamentos WHERE data_agendamento BETWEEN ? AND ?
", [$inicio, $fim]);

// ── Comissões por funcionário no mês ────────────────────────
$comissoes = $db->fetchAll("
    SELECT f.nome, COUNT(a.id) AS servicos,
           COALESCE(SUM(a.valor),0) AS valor_total,
           COALESCE(SUM(a.comissao),0) AS comissao_total
    FROM funcionarios f
    LEFT JOIN agendamentos a ON f.id=a.funcionario_id
        AND a.status='concluido'
        AND a.data_agendamento BETWEEN ? AND ?
    WHERE f.ativo=1
    GROUP BY f.id, f.nome
    ORDER BY comissao_total DESC
", [$inicio, $fim]);

// ── Serviços mais realizados no mês ─────────────────────────
$topServicos = $db->fetchAll("
    SELECT s.nome, COUNT(a.id) AS quantidade,
           COALESCE(SUM(a.valor),0) AS faturamento
    FROM servicos s
    LEFT JOIN agendamentos a ON s.id=a.servico_id
        AND a.status='concluido'
        AND a.data_agendamento BETWEEN ? AND ?
    WHERE s.ativo=1
    GROUP BY s.id, s.nome
    ORDER BY quantidade DESC
    LIMIT 10
", [$inicio, $fim]);

include 'header.php';
?>

<section class="panel">
    <h2>📊 Relatórios</h2>
    <form method="GET" class="search-form">
        <label style="font-weight:600;font-size:13px">Mês de referência
            <input type="month" name="mes" value="<?= h($mes) ?>" max="<?= date('Y-m') ?>">
        </label>
        <button type="submit" class="btn">Gerar relatório</button>
    </form>
    <p style="color:var(--gray-500);font-size:13px;margin-top:8px">
        Período: <?= formatDate($inicio) ?> a <?= formatDate($fim) ?>
    </p>
</section>

<div class="dashboard-grid">
    <div class="card success">
        <strong><?= formatCurrency((float)($resumoFin['entradas'] ?? 0)) ?></strong>
        <span>Entradas no mês</span>
    </div>
    <div class="card danger">
        <strong><?= formatCurrency((float)($resumoFin['saidas'] ?? 0)) ?></strong>
        <span>Saídas no mês</span>
    </div>
    <?php $saldo = ($resumoFin['entradas'] ?? 0) - ($resumoFin['saidas'] ?? 0); ?>
    <div class="card <?= $saldo >= 0 ? 'info' : 'warning' ?>">
        <strong><?= formatCurrency($saldo) ?></strong>
        <span>Saldo do mês</span>
    </div>
    <div class="card">
        <strong><?= (int)($resumoAg['total'] ?? 0) ?></strong>
        <span>Agendamentos</span>
    </div>
    <div class="card success">
        <strong><?= (int)($resumoAg['concluidos'] ?? 0) ?></strong>
        <span>Concluídos</span>
    </div>
    <div class="card danger">
        <strong><?= (int)($resumoAg['cancelados'] ?? 0) ?></strong>
        <span>Cancelados</span>
    </div>
    <div class="card info">
        <strong><?= formatCurrency((float)($resumoAg['faturamento'] ?? 0)) ?></strong>
        <span>Faturamento (serviços)</span>
    </div>
</div>

<div class="panel-grid">
    <section class="panel">
        <h2>👩‍💼 Comissões por funcionário</h2>
        <?php if (empty($comissoes)): ?>
            <p class="empty-state">Nenhum dado disponível.</p>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Funcionário</th><th>Serviços</th><th>Valor total</th><th>Comissão</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($comissoes as $c): ?>
                    <tr>
                        <td><?= h($c['nome']) ?></td>
                        <td><?= (int)$c['servicos'] ?></td>
                        <td><?= formatCurrency((float)$c['valor_total']) ?></td>
                        <td><?= formatCurrency((float)$c['comissao_total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>💅 Serviços mais realizados</h2>
        <?php if (empty($topServicos)): ?>
            <p class="empty-state">Nenhum dado disponível.</p>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Serviço</th><th>Qtd</th><th>Faturamento</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($topServicos as $s): ?>
                    <tr>
                        <td><?= h($s['nome']) ?></td>
                        <td><?= (int)$s['quantidade'] ?></td>
                        <td><?= formatCurrency((float)$s['faturamento']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php include 'footer.php'; ?>
