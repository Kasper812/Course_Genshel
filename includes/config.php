<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Krasnoyarsk');

define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('PAGES_PATH', BASE_PATH . '/pages');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('API_PATH', BASE_PATH . '/api');
define('DATA_PATH', BASE_PATH . '/data');
define('LOGS_PATH', BASE_PATH . '/logs');

define('DATA_FEEDBACK_FILE', DATA_PATH . '/feedback.json');
define('DATA_SUBSCRIBERS_FILE', DATA_PATH . '/subscribers.json');
define('DATA_SERVICES_FILE', DATA_PATH . '/services.json');
define('DATA_FAQ_FILE', DATA_PATH . '/faq.json');
define('DATA_CALCULATOR_FILE', DATA_PATH . '/calculator_requests.json');
define('DATA_DOCTORS_FILE', DATA_PATH . '/doctors.json');
define('DATA_REVIEWS_FILE', DATA_PATH . '/reviews.json');

define('SITE_NAME', 'Genshel Clinics');
define('SITE_SHORT_NAME', 'Genshel');
define('SITE_TAGLINE', 'Премиальная стоматология в Красноярске для взрослых и детей');
define('SITE_ADDRESS', 'г. Красноярск, 1-я Хабаровская, 6');
define('SITE_PHONE', '+7 (391) 200-45-77');
define('SITE_EMAIL', 'hello@astradent-clinic.ru');
define('SITE_HOURS', 'Пн-Вс: 08:00 - 21:00');
define('SITE_LICENSE', 'ЛО-24-01-009999 от 14.03.2025');

$requiredDirectories = [
    DATA_PATH,
    LOGS_PATH,
];

foreach ($requiredDirectories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0775, true);
    }
}