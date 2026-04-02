<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Услуги';
$currentPage = 'services';

$services = load_services();
$categories = service_categories($services);

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">Услуги клиники</p>
        <h1 class="reveal">Все ключевые стоматологические направления в одном центре</h1>
        <p class="lead reveal">
            Используйте фильтры и поиск, чтобы быстро найти подходящую услугу.
            Детали открываются в модальном окне без перезагрузки страницы.
        </p>
        <div class="service-tags reveal">
            <?php foreach ($categories as $slug => $label): ?>
                <span><?= e($label) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="service-controls reveal">
            <div class="filter-buttons" id="serviceCategoryFilters">
                <button class="filter-btn active" data-category="all" type="button">Все услуги</button>
                <?php foreach ($categories as $slug => $label): ?>
                    <button class="filter-btn" data-category="<?= e($slug) ?>" type="button"><?= e($label) ?></button>
                <?php endforeach; ?>
            </div>

            <label class="search-wrap" for="serviceSearch">
                <input id="serviceSearch" type="search" placeholder="Например: имплантация, кариес, элайнеры">
            </label>
        </div>

        <div class="services-grid services-grid-rich" id="servicesGrid">
            <?php foreach ($services as $service): ?>
                <?= render_service_card($service) ?>
            <?php endforeach; ?>
        </div>

        <noscript>
            <p>Для динамической фильтрации включите JavaScript в браузере.</p>
        </noscript>
    </div>
</section>

<section class="section section-accent">
    <div class="container cta-banner reveal">
        <h2>Нужна помощь с выбором специалиста?</h2>
        <p>Оставьте заявку, и администратор подберет врача под вашу задачу и желаемое время приема.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(app_url('pages/feedback.php')) ?>">Оставить заявку</a>
            <a class="btn btn-ghost" href="<?= e(app_url('pages/contacts.php')) ?>">Контакты клиники</a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>