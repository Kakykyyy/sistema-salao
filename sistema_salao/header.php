<?php
/**
 * Cabeçalho — Salão Nil Sisters
 * $pageTitle deve ser definido antes do include
 */
$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestão para o Salão Nil Sisters">
    <title><?= h($pageTitle ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-brand">
            <a href="index.php" class="logo-link" aria-label="Ir para o início">
                <img src="assets/logo.png" alt="Logo <?= h(APP_NAME) ?>" class="logo-image"
                     onerror="this.style.display='none'">
            </a>
            <div>
                <div class="brand-name"><?= h(APP_NAME) ?></div>
                <p class="brand-sub">Sistema de Gestão</p>
            </div>
        </div>

        <nav class="header-nav" aria-label="Navegação principal">
            <?php
            $navItems = [
                'index.php'       => ['🏠', 'Início'],
                'clientes.php'    => ['👥', 'Clientes'],
                'servicos.php'    => ['💅', 'Serviços'],
                'funcionarios.php'=> ['👩‍💼', 'Funcionários'],
                'agendamento.php' => ['📅', 'Agendamentos'],
                'financeiro.php'  => ['💰', 'Financeiro'],
                'relatorios.php'  => ['📊', 'Relatórios'],
                'backup.php'      => ['💾', 'Backup'],
            ];
            foreach ($navItems as $file => [$icon, $label]):
            ?>
                <a href="<?= $file ?>"
                   class="<?= $activePage === $file ? 'active' : '' ?>"
                   aria-current="<?= $activePage === $file ? 'page' : 'false' ?>">
                    <span aria-hidden="true"><?= $icon ?></span> <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>

    <main class="main-content">
<?php
// Exibe feedback se definido antes do include
if (!empty($feedback)):
?>
        <div class="feedback <?= feedbackClass($feedback) ?>" role="alert">
            <?= $feedback ?>
        </div>
<?php endif; ?>
