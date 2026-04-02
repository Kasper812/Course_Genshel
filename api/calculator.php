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
        'message' => 'Запрос отклонен антиспам-фильтром.',
    ], 400);
}

$serviceLabels = [
    'diagnostics' => 'Цифровая диагностика',
    'therapy' => 'Терапевтическое лечение',
    'implants' => 'Имплантация',
    'prosthetics' => 'Протезирование',
    'ortho' => 'Ортодонтия',
    'surgery' => 'Хирургическая стоматология',
    'kids' => 'Детская стоматология',
    'aesthetics' => 'Эстетическая стоматология',
];

$complexityFactors = [
    'basic' => 1.0,
    'advanced' => 1.3,
    'expert' => 1.65,
];

$urgencyFactors = [
    'standard' => 1.0,
    'fast' => 1.18,
    'urgent' => 1.38,
];

$basePrices = [
    'diagnostics' => 3500,
    'therapy' => 6500,
    'implants' => 32000,
    'prosthetics' => 18500,
    'ortho' => 95000,
    'surgery' => 7000,
    'kids' => 2900,
    'aesthetics' => 14500,
];

$serviceType = sanitize_text((string) ($_POST['service_type'] ?? ''), 30);
$complexity = sanitize_text((string) ($_POST['complexity'] ?? ''), 30);
$urgency = sanitize_text((string) ($_POST['urgency'] ?? ''), 30);
$teamSize = (int) ($_POST['team_size'] ?? 1);
$support24 = (string) ($_POST['support_24'] ?? '0') === '1';

$name = sanitize_text((string) ($_POST['name'] ?? ''), 70);
$phone = normalize_phone((string) ($_POST['phone'] ?? ''));
$email = strtolower(sanitize_text((string) ($_POST['email'] ?? ''), 120));
$comment = sanitize_multiline_text((string) ($_POST['comment'] ?? ''), 700);

$errors = [];

if (!isset($basePrices[$serviceType])) {
    $errors['service_type'] = 'Выберите корректный тип услуги.';
}

if (!isset($complexityFactors[$complexity])) {
    $errors['complexity'] = 'Выберите корректный уровень сложности.';
}

if (!isset($urgencyFactors[$urgency])) {
    $errors['urgency'] = 'Выберите корректную срочность.';
}

if ($teamSize < 1 || $teamSize > 15) {
    $errors['team_size'] = 'Количество специалистов должно быть от 1 до 15.';
}

if ($name === '' || utf8_length($name) < 2) {
    $errors['name'] = 'Укажите имя (минимум 2 символа).';
}

if ($phone === '' || !validate_phone($phone)) {
    $errors['phone'] = 'Укажите корректный телефон.';
}

if ($email === '' || !validate_email($email)) {
    $errors['email'] = 'Укажите корректный email.';
}

if (!empty($errors)) {
    json_response([
        'success' => false,
        'message' => 'Проверьте заполненные поля калькулятора.',
        'errors' => $errors,
    ], 422);
}

$base = $basePrices[$serviceType];
$complexityFactor = $complexityFactors[$complexity];
$urgencyFactor = $urgencyFactors[$urgency];
$teamFactor = 1 + (($teamSize - 1) * 0.06);
$comfortCost = $support24 ? 5500 : 0;
$total = (int) round(($base * $complexityFactor * $urgencyFactor * $teamFactor) + $comfortCost);

$record = [
    'id' => 'calc_' . bin2hex(random_bytes(6)),
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'service_type' => $serviceType,
    'service_type_label' => $serviceLabels[$serviceType],
    'complexity' => $complexity,
    'urgency' => $urgency,
    'team_size' => $teamSize,
    'support_24' => $support24,
    'comment' => $comment,
    'total' => $total,
    'source' => sanitize_text((string) ($_POST['source_page'] ?? 'calculator'), 40),
    'created_at' => now_iso(),
    'ip' => get_client_ip(),
    'user_agent' => sanitize_text((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'), 255),
];

$result = safe_json_transaction(DATA_CALCULATOR_FILE, static function (array $entries) use ($record): array {
    $entries[] = $record;

    if (count($entries) > 1000) {
        $entries = array_slice($entries, -1000);
    }

    return array_values($entries);
});

if (!($result['ok'] ?? false)) {
    write_error_log('calculator_api_error: ' . (string) ($result['error'] ?? 'unknown'));

    json_response([
        'success' => false,
        'message' => 'Ошибка сохранения расчета. Повторите попытку позже.',
    ], 500);
}

json_response([
    'success' => true,
    'message' => 'Расчет сохранен. Мы свяжемся с вами для уточнения деталей.',
    'total' => $total,
    'total_formatted' => format_price($total),
], 201);