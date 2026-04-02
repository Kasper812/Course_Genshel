<?php

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/config.php';
}

function send_base_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function utf8_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function utf8_substr(string $value, int $start, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }

    return substr($value, $start, $length);
}

function utf8_lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function sanitize_text(string $value, int $maxLength = 255): string
{
    $clean = trim(strip_tags($value));
    $clean = preg_replace('/\s+/u', ' ', $clean) ?? '';

    if (utf8_length($clean) > $maxLength) {
        $clean = utf8_substr($clean, 0, $maxLength);
    }

    return $clean;
}

function sanitize_multiline_text(string $value, int $maxLength = 2000): string
{
    $clean = trim(strip_tags($value));
    $clean = preg_replace('/[^\P{C}\n\r\t]+/u', '', $clean) ?? '';

    if (utf8_length($clean) > $maxLength) {
        $clean = utf8_substr($clean, 0, $maxLength);
    }

    return $clean;
}

function normalize_phone(string $value): string
{
    $clean = preg_replace('/[^\d\+\(\)\-\s]/', '', $value) ?? '';
    $clean = preg_replace('/\s+/', ' ', trim($clean)) ?? '';

    return $clean;
}

function validate_phone(string $phone): bool
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    $digitsLength = strlen($digits);

    return $digitsLength >= 10 && $digitsLength <= 15;
}

function validate_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function get_csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): bool
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function json_response(array $payload, int $statusCode = 200): void
{
    send_base_headers();
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function require_method(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== strtoupper($method)) {
        json_response([
            'success' => false,
            'message' => 'Некорректный HTTP-метод запроса.',
        ], 405);
    }
}

function require_ajax_request(): void
{
    $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

    if ($requestedWith !== 'xmlhttprequest') {
        json_response([
            'success' => false,
            'message' => 'Ожидается AJAX-запрос.',
        ], 400);
    }
}

function read_json_file(string $filePath, array $fallback = []): array
{
    if (!file_exists($filePath)) {
        return $fallback;
    }

    $raw = file_get_contents($filePath);
    if ($raw === false || trim($raw) === '') {
        return $fallback;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $fallback;
    }

    return $decoded;
}

function safe_json_transaction(string $filePath, callable $callback): array
{
    $handle = fopen($filePath, 'c+');
    if ($handle === false) {
        return ['ok' => false, 'error' => 'Не удалось открыть файл данных.'];
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);

        return ['ok' => false, 'error' => 'Не удалось заблокировать файл данных.'];
    }

    rewind($handle);
    $raw = stream_get_contents($handle);
    $data = [];

    if (is_string($raw) && trim($raw) !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }

    $updatedData = $callback($data);
    if (!is_array($updatedData)) {
        flock($handle, LOCK_UN);
        fclose($handle);

        return ['ok' => false, 'error' => 'Внутренняя ошибка обновления данных.'];
    }

    $json = json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        flock($handle, LOCK_UN);
        fclose($handle);

        return ['ok' => false, 'error' => 'Ошибка сериализации данных.'];
    }

    rewind($handle);
    ftruncate($handle, 0);
    $bytes = fwrite($handle, $json);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    if ($bytes === false) {
        return ['ok' => false, 'error' => 'Ошибка записи данных в файл.'];
    }

    return ['ok' => true, 'data' => $updatedData];
}

function now_iso(): string
{
    return date('c');
}

function get_client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    return sanitize_text($ip, 45);
}

function load_services(): array
{
    return read_json_file(DATA_SERVICES_FILE, []);
}

function load_faq(): array
{
    return read_json_file(DATA_FAQ_FILE, []);
}

function load_doctors(): array
{
    return read_json_file(DATA_DOCTORS_FILE, []);
}

function load_reviews(): array
{
    return read_json_file(DATA_REVIEWS_FILE, []);
}

function sort_by_date_desc(array $items, string $dateKey = 'created_at'): array
{
    usort($items, static function (array $a, array $b) use ($dateKey): int {
        $aTime = strtotime((string) ($a[$dateKey] ?? '')) ?: 0;
        $bTime = strtotime((string) ($b[$dateKey] ?? '')) ?: 0;

        return $bTime <=> $aTime;
    });

    return $items;
}

function service_categories(array $services): array
{
    $categories = [];

    foreach ($services as $service) {
        if (!isset($service['category'], $service['category_label'])) {
            continue;
        }

        $key = (string) $service['category'];
        $categories[$key] = (string) $service['category_label'];
    }

    ksort($categories);

    return $categories;
}

function format_price(int $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' руб.';
}

function render_service_card(array $service): string
{
    $id = (int) ($service['id'] ?? 0);
    $title = e((string) ($service['title'] ?? 'Услуга'));
    $short = e((string) ($service['short'] ?? 'Описание услуги'));
    $categoryLabel = e((string) ($service['category_label'] ?? 'Категория'));
    $priceFrom = (int) ($service['price_from'] ?? 0);
    $duration = e((string) ($service['duration'] ?? 'Индивидуально'));
    $image = e((string) ($service['image'] ?? ''));

    return '<article class="service-card tilt-card reveal" data-category="' . e((string) ($service['category'] ?? '')) . '">
        <div class="service-thumb-wrap">
            <img class="service-thumb" src="' . $image . '" alt="' . $title . '" loading="lazy">
        </div>
        <div class="service-meta">
            <span class="badge">' . $categoryLabel . '</span>
            <span class="price">от ' . format_price($priceFrom) . '</span>
        </div>
        <h3>' . $title . '</h3>
        <p>' . $short . '</p>
        <div class="service-footer">
            <span class="duration">Срок: ' . $duration . '</span>
            <button class="btn btn-small btn-ghost js-service-details" data-service-id="' . $id . '" type="button">Подробнее</button>
        </div>
    </article>';
}

function app_url(string $path = ''): string
{
    if ($path === '') {
        return '/';
    }

    return '/' . ltrim($path, '/');
}

function media_url(string $path = ''): string
{
    $path = trim($path);

    if ($path === '') {
        return '';
    }

    if (preg_match('~^(https?:)?//~i', $path) === 1 || str_starts_with($path, 'data:')) {
        return $path;
    }

    return app_url($path);
}

function is_active_page(string $currentPage, string $checkPage): bool
{
    return $currentPage === $checkPage;
}

function write_error_log(string $message): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents(LOGS_PATH . '/app.log', $line, FILE_APPEND | LOCK_EX);
}