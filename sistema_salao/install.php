<?php
/**
 * Instalador do Sistema Salão Nil Sisters
 * Versão 2.0.0
 */

session_start();
require_once 'config.php';

// Verificar se já está instalado
$installed = false;
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($result->rowCount() > 0) {
        $pdo->exec("USE " . DB_NAME);
        $result = $pdo->query("SHOW TABLES");
        if ($result->rowCount() > 0) {
            $installed = true;
        }
    }
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['install'])) {
        try {
            // Criar banco de dados se não existir
            $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Selecionar banco
            $pdo->exec("USE " . DB_NAME);

            // Executar schema completo
            $schema = file_get_contents('schema_completo.sql');
            $pdo->exec($schema);

            $success = "Sistema instalado com sucesso! Você pode acessar o sistema agora.";
            $installed = true;

        } catch (PDOException $e) {
            $error = "Erro na instalação: " . $e->getMessage();
        }
    } elseif (isset($_POST['test_connection'])) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("SELECT 1");

            $connection_success = "Conexão com o banco de dados estabelecida com sucesso!";
        } catch (PDOException $e) {
            $connection_error = "Erro na conexão: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Sistema Salão Nil Sisters</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .installer-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .installer-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .installer-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .installer-header p {
            color: #7f8c8d;
            font-size: 16px;
        }
        .status-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .status-success {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .status-error {
            border-color: #dc3545;
            background-color: #f8d7da;
        }
        .btn-install {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-test {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
        .requirements {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .requirements h3 {
            margin-top: 0;
            color: #495057;
        }
        .requirements ul {
            list-style: none;
            padding: 0;
        }
        .requirements li {
            padding: 5px 0;
        }
        .requirements li:before {
            content: "✓";
            color: #28a745;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1>🚀 Instalador do Sistema</h1>
            <p>Sistema de Gestão para Salão Nil Sisters v<?php echo APP_VERSION; ?></p>
        </div>

        <?php if ($installed): ?>
            <div class="status-card status-success">
                <h3>✅ Sistema Já Instalado</h3>
                <p>O sistema já está instalado e configurado. Você pode acessar o sistema principal agora.</p>
                <a href="index.php" class="btn-install" style="text-decoration: none; display: inline-block; margin-top: 15px;">Acessar Sistema</a>
            </div>
        <?php else: ?>
            <div class="requirements">
                <h3>📋 Requisitos do Sistema</h3>
                <ul>
                    <li>PHP 7.4 ou superior</li>
                    <li>MySQL 5.7 ou MariaDB 10.0 ou superior</li>
                    <li>Apache/Nginx com mod_rewrite</li>
                    <li>Extensão PDO MySQL habilitada</li>
                    <li>Permissões de escrita nos diretórios logs/, backups/, uploads/</li>
                </ul>
            </div>

            <div class="status-card">
                <h3>🔧 Configurações Atuais</h3>
                <p><strong>Servidor:</strong> <?php echo DB_HOST; ?></p>
                <p><strong>Banco:</strong> <?php echo DB_NAME; ?></p>
                <p><strong>Charset:</strong> <?php echo DB_CHARSET; ?></p>
                <p><strong>Usuário:</strong> <?php echo DB_USER; ?></p>

                <form method="post" style="margin-top: 15px;">
                    <button type="submit" name="test_connection" class="btn-test">Testar Conexão</button>
                </form>

                <?php if (isset($connection_success)): ?>
                    <div style="color: #28a745; margin-top: 10px;">✅ <?php echo $connection_success; ?></div>
                <?php endif; ?>

                <?php if (isset($connection_error)): ?>
                    <div style="color: #dc3545; margin-top: 10px;">❌ <?php echo $connection_error; ?></div>
                <?php endif; ?>

                <?php if (isset($db_error)): ?>
                    <div style="color: #856404; margin-top: 10px; background: #fff3cd; padding: 10px; border-radius: 4px;">
                        ⚠️ <strong>Aviso:</strong> <?php echo $db_error; ?><br>
                        Verifique se o MySQL está rodando e as credenciais estão corretas.
                    </div>
                <?php endif; ?>
            </div>

            <div class="status-card">
                <h3>📦 Instalação do Banco de Dados</h3>
                <p>Esta instalação irá:</p>
                <ul>
                    <li>Criar o banco de dados '<?php echo DB_NAME; ?>' (se não existir)</li>
                    <li>Criar todas as tabelas necessárias</li>
                    <li>Adicionar índices e chaves estrangeiras</li>
                    <li>Inserir dados de exemplo para teste</li>
                    <li>Criar views, procedures e triggers</li>
                </ul>

                <form method="post" onsubmit="return confirm('Tem certeza que deseja instalar o sistema? Isso irá criar/modificar o banco de dados.');">
                    <button type="submit" name="install" class="btn-install">Instalar Sistema</button>
                </form>

                <?php if (isset($success)): ?>
                    <div style="color: #28a745; margin-top: 15px; font-weight: bold;">✅ <?php echo $success; ?></div>
                    <a href="index.php" class="btn-install" style="text-decoration: none; display: inline-block; margin-top: 15px;">Acessar Sistema</a>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div style="color: #dc3545; margin-top: 15px; font-weight: bold;">❌ <?php echo $error; ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-refresh após instalação bem-sucedida
        <?php if (isset($success)): ?>
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>