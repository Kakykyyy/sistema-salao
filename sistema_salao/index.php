<?php
/**
 * Dashboard — Salão Nil Sisters
 */
require_once 'conexao.php';
$pageTitle = 'Painel — ' . APP_NAME;

// Estatísticas gerais
$stats = $db->fetchOne("
    SELECT
        (SELECT COUNT(*) FROM clientes     WHERE ativo = 1) AS clientes,
        (SELECT COUNT(*) FROM servicos     WHERE ativo = 1) AS servicos,
        (SELECT COUNT(*) FROM funcionarios WHERE ativo = 1) AS funcionarios,
        (SELECT COUNT(*) FROM agendamentos WHERE status = 'agendado') AS agendamentos_abertos,
        (SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='entrada') AS entradas,
        (SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='saida')   AS saidas
") ?? [];

$entradas = (float)($stats['entradas'] ?? 0);
$saidas   = (float)($stats['saidas']   ?? 0);
$saldo    = $entradas - $saidas;

// Últimos agendamentos
$ultimosAgendamentos = $db->fetchAll("
    SELECT a.data_agendamento, a.hora_agendamento, a.status,
           c.nome AS cliente, s.nome AS servico
    FROM agendamentos a
    JOIN clientes    c ON a.cliente_id    = c.id
    JOIN servicos    s ON a.servico_id    = s.id
    ORDER BY a.data_cadastro DESC
    LIMIT 5
");

// Últimos lançamentos
$ultimosLancamentos = $db->fetchAll("
    SELECT descricao, valor, tipo, data
    FROM financeiro
    ORDER BY data_cadastro DESC
    LIMIT 5
");

include 'header.php';
?>

<section class="panel">
    <h2>📊 Visão geral</h2>
    <div class="dashboard-grid">
        <div class="card">
            <strong><?= number_format((int)($stats['clientes'] ?? 0)) ?></strong>
            <span>👥 Clientes ativos</span>
        </div>
        <div class="card">
            <strong><?= number_format((int)($stats['servicos'] ?? 0)) ?></strong>
            <span>💅 Serviços</span>
        </div>
        <div class="card">
            <strong><?= number_format((int)($stats['funcionarios'] ?? 0)) ?></strong>
            <span>👩‍💼 Funcionários</span>
        </div>
        <div class="card">
            <strong><?= number_format((int)($stats['agendamentos_abertos'] ?? 0)) ?></strong>
            <span>📅 Agendamentos abertos</span>
        </div>
        <div class="card success">
            <strong><?= formatCurrency($entradas) ?></strong>
            <span>💰 Entradas</span>
        </div>
        <div class="card danger">
            <strong><?= formatCurrency($saidas) ?></strong>
            <span>💸 Saídas</span>
        </div>
        <div class="card <?= $saldo >= 0 ? 'info' : 'warning' ?>">
            <strong><?= formatCurrency($saldo) ?></strong>
            <span>📈 Saldo atual</span>
        </div>
    </div>
</section>

<section class="panel">
    <h2>🚀 Acesso rápido</h2>
    <div class="dashboard-grid">
        <?php
        $links = [
            'clientes.php'    => ['👥', 'Clientes'],
            'servicos.php'    => ['💅', 'Serviços'],
            'funcionarios.php'=> ['👩‍💼', 'Funcionários'],
            'agendamento.php' => ['📅', 'Agendamentos'],
            'financeiro.php'  => ['💰', 'Financeiro'],
            'relatorios.php'  => ['📊', 'Relatórios'],
        ];
        foreach ($links as $url => [$icon, $label]):
        ?>
        <a href="<?= $url ?>" class="card quick-card">
            <strong><?= $icon ?></strong>
            <span><?= $label ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="panel-grid">
    <?php if (!empty($ultimosAgendamentos)): ?>
    <section class="panel">
        <h2>📋 Últimos agendamentos</h2>
        <div class="activity-list">
            <?php foreach ($ultimosAgendamentos as $ag): ?>
            <div class="activity-item">
                <strong><?= h($ag['cliente']) ?></strong> — <?= h($ag['servico']) ?>
                <small>
                    <?= formatDate($ag['data_agendamento']) ?> às <?= substr($ag['hora_agendamento'], 0, 5) ?>
                    &nbsp;<span class="status status-<?= h($ag['status']) ?>"><?= ucfirst(h($ag['status'])) ?></span>
                </small>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($ultimosLancamentos)): ?>
    <section class="panel">
        <h2>💵 Últimos lançamentos</h2>
        <div class="activity-list">
            <?php foreach ($ultimosLancamentos as $f): ?>
            <div class="activity-item">
                <strong><?= h($f['descricao']) ?></strong>
                <small>
                    <?= formatCurrency((float)$f['valor']) ?> em <?= formatDate($f['data']) ?>
                    &nbsp;<span class="status status-<?= h($f['tipo']) ?>"><?= ucfirst(h($f['tipo'])) ?></span>
                </small>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
