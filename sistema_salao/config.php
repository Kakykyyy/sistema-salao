<?php
/**
 * Configurações do Sistema — Salão Nil Sisters
 */

// ── Banco de dados ──────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_NAME',    'salao');
define('DB_CHARSET', 'utf8mb4');

// ── Aplicação ───────────────────────────────────────────────
define('APP_NAME',    'Salão Nil Sisters');
define('APP_VERSION', '2.0.0');
define('APP_TIMEZONE','America/Sao_Paulo');

// ── Paginação / sessão ──────────────────────────────────────
define('ITEMS_PER_PAGE',   10);
define('SESSION_TIMEOUT',  3600);
define('BACKUP_PATH',      __DIR__ . '/backups/');
define('BACKUP_RETENTION', 30);

// ── Timezone e locale ───────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);
setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'portuguese');

// ── Ambiente ────────────────────────────────────────────────
$_host = $_SERVER['SERVER_NAME'] ?? 'cli';
$isDev = in_array($_host, ['localhost', '127.0.0.1', '::1'])
      || str_ends_with($_host, '.local');

if ($isDev) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// ── Diretórios necessários ──────────────────────────────────
foreach (['logs', 'backups'] as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) mkdir($path, 0755, true);
}
