<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Baza danych
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'electro_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Aplikacja
define('APP_NAME', 'Electro Shop');
define('APP_URL', 'http://localhost');
define('APP_DEBUG', true);

// Ścieżki
if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__);
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('VIEWS_PATH')) define('VIEWS_PATH', ROOT_PATH . '/views');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', ROOT_PATH . '/storage');

if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}


if (!defined('SMTP_HOST')) define('SMTP_HOST', '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', ''); /
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl' or ''
if (!defined('MAIL_FROM')) define('MAIL_FROM', 'noreply@yourdomain.com');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', APP_NAME);


if (!defined('MAILTRAP_HOST')) define('MAILTRAP_HOST', 'sandbox.smtp.mailtrap.io');
if (!defined('MAILTRAP_PORT')) define('MAILTRAP_PORT', 2525);
if (!defined('MAILTRAP_USER')) define('MAILTRAP_USER', 'f36f07369eaa89');
if (!defined('MAILTRAP_PASS')) define('MAILTRAP_PASS', '6f8bf48db90b2a');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


spl_autoload_register(function ($class) {

    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    }
});


function env($key, $default = null) {
    if (defined($key)) {
        return constant($key);
    }
    return $default;
}

function asset($path) {
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base_path = dirname($script_name);
    
    if ($base_path === '/' || $base_path === '\\' || $base_path === '.') {
        $base_path = '';
    } else {
        if (substr($base_path, 0, 1) !== '/') {
            $base_path = '/' . $base_path;
        }
    }
    
    $path = ltrim($path, '/');
    return $base_path . '/' . $path;
}

function url($path = '') {
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base_path = dirname($script_name);
    
    if ($base_path === '/' || $base_path === '\\' || $base_path === '.') {
        $base_path = '';
    } else {
        if (substr($base_path, 0, 1) !== '/') {
            $base_path = '/' . $base_path;
        }
    }
    
    $path = ltrim($path, '/');
    return $base_path . ($path ? '/' . $path : '');
}

function full_url($path = '') {
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

function redirect($url) {
    if (strpos($url, 'http') === 0) {
        header('Location: ' . $url);
        exit;
    }

    $path = ltrim($url, '/');
    $target = url($path);
    header('Location: ' . $target);
    exit;
}

function old($key, $default = '') {
    return $_SESSION['old_' . $key] ?? $default;
}

function flash($key, $value = null) {
    if ($value === null) {
        $val = $_SESSION['flash_' . $key] ?? null;
        unset($_SESSION['flash_' . $key]);
        return $val;
    }
    $_SESSION['flash_' . $key] = $value;
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    $token = $_POST['_token'] ?? $_GET['_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function dd($value) {
    var_dump($value);
    die();
}

// Funkcje autentykacji
function auth() {
    if (isset($_SESSION['user_id'])) {
        $userData = \DB::selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if ($userData) {
            return new \App\Models\User($userData);
        }
    }
    return null;
}

function auth_check() {
    return isset($_SESSION['user_id']) && auth() !== null;
}

// Połączenie z bazą danych
class DB {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die("Błąd połączenia z bazą danych: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    public static function query($sql, $params = []) {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function select($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function selectOne($sql, $params = []) {
        return self::query($sql, $params)->fetch();
    }
    
    public static function insert($sql, $params = []) {
        self::query($sql, $params);
        return self::getConnection()->lastInsertId();
    }
    
    public static function update($sql, $params = []) {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function delete($sql, $params = []) {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function beginTransaction() {
        return self::getConnection()->beginTransaction();
    }
    
    public static function commit() {
        return self::getConnection()->commit();
    }
    
    public static function rollBack() {
        return self::getConnection()->rollBack();
    }
}

function send_mail($to, $subject, $body, $headers = []) {
    if (!is_dir(STORAGE_PATH)) {
        @mkdir(STORAGE_PATH, 0755, true);
    }

    $defaultHeaders = [
        'From: ' . (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' : 'noreply@localhost'),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8'
    ];

    $allHeaders = array_merge($defaultHeaders, $headers);
    $headersString = implode("\r\n", $allHeaders);

    $sent = false;
    $errorMessage = '';

    if (!empty(MAILTRAP_USER) && !empty(MAILTRAP_PASS) && !empty(MAILTRAP_HOST)) {
        $boundary = 'boundary-' . bin2hex(random_bytes(8));
        $fromHeader = (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' : MAIL_FROM);

        $raw  = "From: " . $fromHeader . "\r\n";
        $raw .= "To: " . $to . "\r\n";
        $raw .= "Subject: " . $subject . "\r\n";
        $raw .= "MIME-Version: 1.0\r\n";
        $raw .= "Content-Type: multipart/alternative; boundary=\"" . $boundary . "\"\r\n\r\n";

        $raw .= "--" . $boundary . "\r\n";
        $raw .= "Content-Type: text/plain; charset=\"utf-8\"\r\n\r\n";
        $raw .= strip_tags($body) . "\r\n\r\n";

        $raw .= "--" . $boundary . "\r\n";
        $raw .= "Content-Type: text/html; charset=\"utf-8\"\r\n\r\n";
        $raw .= $body . "\r\n\r\n";

        $raw .= "--" . $boundary . "--\r\n";

        $tmpFile = STORAGE_PATH . '/mail_' . uniqid() . '.eml';
        file_put_contents($tmpFile, $raw);

        $curl = [];
        $curl[] = 'curl --silent --show-error --ssl-reqd';
        $curl[] = "--url 'smtp://" . MAILTRAP_HOST . ":" . MAILTRAP_PORT . "'";
        $curl[] = "--user '" . MAILTRAP_USER . ":" . MAILTRAP_PASS . "'";
        $curl[] = "--mail-from '" . MAIL_FROM . "'";
        $curl[] = "--mail-rcpt '" . $to . "'";
        $curl[] = "--upload-file '" . $tmpFile . "'";

        $cmd = implode(' ', $curl) . ' 2>&1';

        exec($cmd, $output, $exitCode);
        $outputStr = implode("\n", $output);

        if ($exitCode === 0) {
            $sent = true;
        } else {
            $sent = false;
            $errorMessage = 'Mailtrap curl error: ' . $outputStr;
        }

        @unlink($tmpFile);
    }

    if (!$sent) {
        if (!empty(SMTP_HOST) && !empty(SMTP_USER)) {
            try {
                $smtpResult = smtp_send($to, $subject, $body, [
                    'host' => SMTP_HOST,
                    'port' => SMTP_PORT,
                    'user' => SMTP_USER,
                    'pass' => SMTP_PASS,
                    'secure' => SMTP_SECURE,
                    'from' => MAIL_FROM,
                    'from_name' => MAIL_FROM_NAME,
                ], $headersString);

                $sent = $smtpResult['sent'] ?? false;
                $errorMessage = $smtpResult['error'] ?? '';
            } catch (\Throwable $e) {
                $sent = false;
                $errorMessage = $e->getMessage();
            }
        } else {
            try {
                $sent = @mail($to, $subject, $body, $headersString);
            } catch (\Throwable $e) {
                $sent = false;
                $errorMessage = $e->getMessage();
            }
        }
    }

    $logEntry = "=== " . date('Y-m-d H:i:s') . " ===\n";
    $logEntry .= "To: " . $to . "\n";
    $logEntry .= "Subject: " . $subject . "\n";
    $logEntry .= "Headers: " . $headersString . "\n";
    $logEntry .= "Body:\n" . $body . "\n\n";
    if (!empty($errorMessage)) {
        $logEntry .= "SMTP/Error: " . $errorMessage . "\n";
    }
    $logEntry .= "Sent: " . ($sent ? 'yes' : 'no') . "\n\n";

    @file_put_contents(STORAGE_PATH . '/emails.log', $logEntry, FILE_APPEND | LOCK_EX);

    return $sent;
}

function smtp_send($to, $subject, $body, $smtpConfig = [], $headersString = '') {
    $host = $smtpConfig['host'] ?? '';
    $port = $smtpConfig['port'] ?? 587;
    $user = $smtpConfig['user'] ?? '';
    $pass = $smtpConfig['pass'] ?? '';
    $secure = strtolower($smtpConfig['secure'] ?? '');
    $from = $smtpConfig['from'] ?? 'noreply@localhost';
    $from_name = $smtpConfig['from_name'] ?? '';

    $result = ['sent' => false, 'error' => ''];

    if (empty($host) || empty($user)) {
        $result['error'] = 'SMTP host or user not configured.';
        return $result;
    }

    $remote = $host . ':' . $port;
    $contextOptions = [];
    $errno = 0; $errstr = '';

    $flags = STREAM_CLIENT_CONNECT;
    $stream = @stream_socket_client($remote, $errno, $errstr, 10, $flags);
    if (!$stream) {
        $result['error'] = "Unable to connect to SMTP server: $errstr ($errno)";
        return $result;
    }
    stream_set_timeout($stream, 10);

    $res = smtp_get_response($stream);
    if (strpos($res, '220') !== 0) {
        $result['error'] = 'SMTP server did not respond with 220: ' . trim($res);
        fclose($stream);
        return $result;
    }

    $hostname = $_SERVER['SERVER_NAME'] ?? 'localhost';
    smtp_put($stream, "EHLO " . $hostname);
    $ehlo = smtp_get_multiline($stream);

    if ($secure === 'tls' || ($port == 587 && $secure !== 'ssl')) {
        smtp_put($stream, "STARTTLS");
        $startTlsResp = smtp_get_response($stream);
        if (strpos($startTlsResp, '220') === 0) {
            if (!stream_socket_enable_crypto($stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $result['error'] = 'Failed to enable TLS on SMTP connection.';
                fclose($stream);
                return $result;
            }
            smtp_put($stream, "EHLO " . $hostname);
            $ehlo = smtp_get_multiline($stream);
        } else {
        }
    }

    smtp_put($stream, "AUTH LOGIN");
    $authResp = smtp_get_response($stream);
    if (strpos($authResp, '334') !== 0) {
        $result['error'] = 'SMTP server did not accept AUTH LOGIN: ' . trim($authResp);
        fclose($stream);
        return $result;
    }
    smtp_put($stream, base64_encode($user));
    $userResp = smtp_get_response($stream);
    smtp_put($stream, base64_encode($pass));
    $passResp = smtp_get_response($stream);
    if (strpos($passResp, '235') !== 0) {
        $result['error'] = 'SMTP authentication failed: ' . trim($passResp);
        fclose($stream);
        return $result;
    }

    smtp_put($stream, "MAIL FROM:<" . $from . ">");
    $mailFromResp = smtp_get_response($stream);
    if (strpos($mailFromResp, '250') !== 0) {
        $result['error'] = 'MAIL FROM rejected: ' . trim($mailFromResp);
        fclose($stream);
        return $result;
    }

    smtp_put($stream, "RCPT TO:<" . $to . ">");
    $rcptResp = smtp_get_response($stream);
    if (strpos($rcptResp, '250') !== 0 && strpos($rcptResp, '251') !== 0) {
        $result['error'] = 'RCPT TO rejected: ' . trim($rcptResp);
        fclose($stream);
        return $result;
    }

    smtp_put($stream, "DATA");
    $dataResp = smtp_get_response($stream);
    if (strpos($dataResp, '354') !== 0) {
        $result['error'] = 'DATA command rejected: ' . trim($dataResp);
        fclose($stream);
        return $result;
    }

    $headers = "From: " . ($from_name ? ($from_name . ' <' . $from . '>') : $from) . "\r\n";
    $headers .= "To: " . $to . "\r\n";
    $headers .= "Subject: " . $subject . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $message = $headers . "\r\n" . $body . "\r\n.\r\n";
    smtp_put($stream, $message);
    $messageResp = smtp_get_response($stream);
    if (strpos($messageResp, '250') !== 0) {
        $result['error'] = 'Message not accepted: ' . trim($messageResp);
        fclose($stream);
        return $result;
    }

    smtp_put($stream, "QUIT");
    fclose($stream);

    $result['sent'] = true;
    return $result;
}

function smtp_put($stream, $data) {
    fwrite($stream, $data . "\r\n");
}

function smtp_get_response($stream) {
    $line = fgets($stream, 515);
    return $line;
}

function smtp_get_multiline($stream) {
    $lines = '';
    while (($line = fgets($stream, 515)) !== false) {
        $lines .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return $lines;
}
