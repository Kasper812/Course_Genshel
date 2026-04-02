<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

require_method('GET');

$services = load_services();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    foreach ($services as $service) {
        if ((int) ($service['id'] ?? 0) === $id) {
            json_response([
                'success' => true,
                'item' => $service,
            ]);
        }
    }

    json_response([
        'success' => false,
        'message' => 'Услуга не найдена.',
    ], 404);
}

$category = utf8_lower(sanitize_text((string) ($_GET['category'] ?? 'all'), 40));
$search = utf8_lower(sanitize_text((string) ($_GET['search'] ?? ''), 120));

$filtered = array_values(array_filter($services, static function (array $service) use ($category, $search): bool {
    $serviceCategory = utf8_lower((string) ($service['category'] ?? ''));

    if ($category !== 'all' && $category !== '' && $serviceCategory !== $category) {
        return false;
    }

    if ($search === '') {
        return true;
    }

    $title = utf8_lower((string) ($service['title'] ?? ''));
    $short = utf8_lower((string) ($service['short'] ?? ''));
    $description = utf8_lower((string) ($service['description'] ?? ''));

    return strpos($title, $search) !== false
        || strpos($short, $search) !== false
        || strpos($description, $search) !== false;
}));

json_response([
    'success' => true,
    'items' => $filtered,
    'count' => count($filtered),
]);
