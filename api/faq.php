<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

require_method('GET');

$faqItems = load_faq();

json_response([
    'success' => true,
    'items' => array_values($faqItems),
    'count' => count($faqItems),
]);
