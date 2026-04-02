<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

send_base_headers();

$pageTitle = $pageTitle ?? SITE_NAME;
$currentPage = $currentPage ?? 'home';
$csrfToken = get_csrf_token();
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(SITE_NAME) ?> - премиальная стоматология в Красноярске. Современное лечение, диагностика и забота о пациентах.">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('assets/css/responsive.css')) ?>">
</head>
<body data-page="<?= e($currentPage) ?>" class="page-enter">
    <div id="preloader" class="preloader" aria-hidden="true">
        <div class="preloader-spinner"></div>
        <p>Подготавливаем клинику к вашему визиту...</p>
    </div>

    <div class="topline">
        <div class="container topline-inner">
            <span>Лицензия: <?= e(SITE_LICENSE) ?></span>
            <span>Ежедневно: <?= e(SITE_HOURS) ?></span>
            <a href="tel:+73912004577">Экстренная запись: <?= e(SITE_PHONE) ?></a>
        </div>
    </div>

    <header class="site-header" id="top">
        <div class="container header-inner">
            <a class="logo" href="<?= e(app_url('index.php')) ?>">
                <span class="logo-mark"><img src="<?= e(app_url('assets/img/logo_tooth.png')) ?>" alt="Логотип"></span>
                <span class="logo-text">
                    <strong><?= e(SITE_SHORT_NAME) ?></strong>
                    <small>Dental &amp; Implant Center</small>
                </span>
            </a>

            <button class="burger" id="burgerMenu" type="button" aria-label="Открыть меню" aria-expanded="false" aria-controls="siteNav">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="site-nav" id="siteNav">
                <a class="nav-link <?= is_active_page($currentPage, 'home') ? 'active' : '' ?>" href="<?= e(app_url('index.php')) ?>">Главная</a>
                <a class="nav-link <?= is_active_page($currentPage, 'about') ? 'active' : '' ?>" href="<?= e(app_url('pages/about.php')) ?>">О клинике</a>
                <a class="nav-link <?= is_active_page($currentPage, 'services') ? 'active' : '' ?>" href="<?= e(app_url('pages/services.php')) ?>">Услуги</a>
                <a class="nav-link <?= is_active_page($currentPage, 'faq') ? 'active' : '' ?>" href="<?= e(app_url('pages/faq.php')) ?>">FAQ</a>
                <a class="nav-link <?= is_active_page($currentPage, 'contacts') ? 'active' : '' ?>" href="<?= e(app_url('pages/contacts.php')) ?>">Контакты</a>
                <a class="nav-link <?= is_active_page($currentPage, 'feedback') ? 'active' : '' ?>" href="<?= e(app_url('pages/feedback.php')) ?>">Запись</a>
                <a class="nav-link <?= is_active_page($currentPage, 'admin') ? 'active' : '' ?>" href="<?= e(app_url('pages/admin-demo.php')) ?>">Демо-админ</a>
            </nav>

            <div class="header-actions">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Переключить тему">
                    <span class="theme-icon" aria-hidden="true">◐</span>
                    <span class="theme-label">Тема</span>
                </button>
                <a class="btn btn-primary btn-small" href="<?= e(app_url('pages/feedback.php')) ?>">Онлайн-запись</a>
            </div>
        </div>
    </header>

    <main class="site-main">