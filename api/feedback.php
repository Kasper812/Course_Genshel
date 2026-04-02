<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

require_method('POST');
require_ajax_request();

$csrfToken = (string) ($_POST['csrf_token'] ?? '');
if (!verify_csrf_token($csrfToken)) {
    json_response([
        'success' => false,
        'message' => 'Сессия устарела. Обновите страницу и попробуйте снова.',
    ], 419);
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    json_response([
        'success' => false,
        'message' => 'Запрос отклонен системой антиспама.',
    ], 400);
}

$name = sanitize_text((string) ($_POST['name'] ?? ''), 70);
$phone = normalize_phone((string) ($_POST['phone'] ?? ''));
$email = strtolower(sanitize_text((string) ($_POST['email'] ?? ''), 120));
$subject = sanitize_text((string) ($_POST['subject'] ?? ''), 120);
$message = sanitize_multiline_text((string) ($_POST['message'] ?? ''), 1500);
$sourcePage = sanitize_text((string) ($_POST['source_page'] ?? 'unknown'), 40);

$errors = [];

if ($name === '' || utf8_length($name) < 2) {
    $errors['name'] = 'Укажите корректное имя (не менее 2 символов).';
}

if ($phone === '' || !validate_phone($phone)) {
    $errors['phone'] = 'Укажите корректный номер телефона.';
}

if ($email === '' || !validate_email($email)) {
    $errors['email'] = 'Укажите корректный email.';
}

if ($subject === '' || utf8_length($subject) < 3) {
    $errors['subject'] = 'Тема должна содержать не менее 3 символов.';
}

if ($message === '' || utf8_length($message) < 10) {
    $errors['message'] = 'Сообщение должно содержать не менее 10 символов.';
}

if (!empty($errors)) {
    json_response([
        'success' => false,
        'message' => 'Проверьте корректность заполнения формы.',
        'errors' => $errors,
    ], 422);
}

$record = [
    'id' => 'fb_' . bin2hex(random_bytes(6)),
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'source' => $sourcePage,
    'created_at' => now_iso(),
    'ip' => get_client_ip(),
    'user_agent' => sanitize_text((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'), 255),
];

$result = safe_json_transaction(DATA_FEEDBACK_FILE, static function (array $entries) use ($record): array {
    $entries[] = $record;

    if (count($entries) > 1000) {
        $entries = array_slice($entries, -1000);
    }

    return array_values($entries);
});

if (!($result['ok'] ?? false)) {
    write_error_log('feedback_api_error: ' . (string) ($result['error'] ?? 'unknown'));

    json_response([
        'success' => false,
        'message' => 'Не удалось сохранить заявку. Повторите попытку позже.',
    ], 500);
}

json_response([
    'success' => true,
    'message' => 'Спасибо! Заявка успешно отправлена. Мы скоро с вами свяжемся.',
], 201);
