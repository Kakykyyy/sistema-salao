<?php
/**
 * Conexão com banco e funções utilitárias — Salão Nil Sisters
 */

require_once __DIR__ . '/config.php';

// ══════════════════════════════════════════════════════════════
// CLASSE DATABASE (Singleton PDO)
// ══════════════════════════════════════════════════════════════
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            die('Erro de conexão com o banco de dados. Verifique as configurações em config.php.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Executa query e retorna Statement ou false */
    public function query(string $sql, array $params = []): PDOStatement|false
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return false;
        }
    }

    /** Retorna uma linha ou null */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row  = $stmt ? $stmt->fetch() : false;
        return $row ?: null;
    }

    /** Retorna todas as linhas */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /** Executa DML e retorna rowCount ou false */
    public function execute(string $sql, array $params = []): int|false
    {
        $stmt = $this->query($sql, $params);
        return $stmt !== false ? $stmt->rowCount() : false;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}

// Instância global
$db = Database::getInstance();

// ══════════════════════════════════════════════════════════════
// FUNÇÕES UTILITÁRIAS
// ══════════════════════════════════════════════════════════════

/** Trim + cast para string */
function sanitizeInput(mixed $value): string
{
    return trim((string) $value);
}

/** Escape HTML seguro */
function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Normaliza valor monetário: "R$ 1.200,50" → 1200.50 */
function normalizeCurrency(string $value): float
{
    $clean = str_replace(['R$', '.', ' '], '', $value);
    $clean = str_replace(',', '.', $clean);
    return (float) $clean;
}

/** Clamp percentual 0-100 */
function normalizePercent(mixed $value): float
{
    return max(0.0, min(100.0, (float) str_replace(',', '.', (string) $value)));
}

/** Formata valor em moeda BR */
function formatCurrency(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/** Formata data para exibição */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

/** Valida data no formato Y-m-d */
function validateDate(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/** Valida hora HH:MM */
function validateTime(string $time): bool
{
    return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time);
}

/** Valida e-mail */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/** Valida telefone (10 ou 11 dígitos) */
function validatePhone(string $phone): bool
{
    $digits = preg_replace('/\D/', '', $phone);
    return strlen($digits) >= 10 && strlen($digits) <= 11;
}

/** Formata telefone: (00) 00000-0000 */
function formatPhone(string $phone): string
{
    $d = preg_replace('/\D/', '', $phone);
    if (strlen($d) === 11) return '('.substr($d,0,2).') '.substr($d,2,5).'-'.substr($d,7);
    if (strlen($d) === 10) return '('.substr($d,0,2).') '.substr($d,2,4).'-'.substr($d,6);
    return $phone;
}

/** Retorna classe CSS de feedback baseada no conteúdo da mensagem */
function feedbackClass(string $msg): string
{
    $lower = mb_strtolower($msg);
    return (str_contains($lower, 'erro') || str_contains($lower, 'não é possível'))
        ? 'error' : 'success';
}
