<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

require_method('POST');
require_ajax_request();

$csrfToken = (string) ($_POST['csrf_token'] ?? '');
if (!verify_csrf_token($csrfToken)) {
    json_response([
        'success' => false,
        'message' => 'Сессия устарела. Обновите страницу.',
    ], 419);
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    json_response([
        'success' => false,
        'message' => 'Подписка отклонена антиспам-фильтром.',
    ], 400);
}

$email = strtolower(sanitize_text((string) ($_POST['email'] ?? ''), 120));
if (!validate_email($email)) {
    json_response([
        'success' => false,
        'message' => 'Введите корректный email для подписки.',
    ], 422);
}

$isDuplicate = false;
$record = [
    'id' => 'sub_' . bin2hex(random_bytes(6)),
    'email' => $email,
    'source' => sanitize_text((string) ($_POST['source_page'] ?? 'footer'), 40),
    'created_at' => now_iso(),
];

$result = safe_json_transaction(DATA_SUBSCRIBERS_FILE, static function (array $entries) use ($email, $record, &$isDuplicate): array {
    foreach ($entries as $entry) {
        $existingEmail = strtolower((string) ($entry['email'] ?? ''));
        if ($existingEmail === $email) {
            $isDuplicate = true;
            return $entries;
        }
    }

    $entries[] = $record;
    if (count($entries) > 3000) {
        $entries = array_slice($entries, -3000);
    }

    return array_values($entries);
});

if (!($result['ok'] ?? false)) {
    write_error_log('subscribe_api_error: ' . (string) ($result['error'] ?? 'unknown'));

    json_response([
        'success' => false,
        'message' => 'Не удалось оформить подписку. Попробуйте позже.',
    ], 500);
}

if ($isDuplicate) {
    json_response([
        'success' => false,
        'message' => 'Этот email уже подписан на рассылку.',
    ], 409);
}

json_response([
    'success' => true,
    'message' => 'Подписка успешно оформлена. Спасибо!',
], 201);
