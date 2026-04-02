<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'FAQ';
$currentPage = 'faq';

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">FAQ</p>
        <h1 class="reveal">Ответы на частые вопросы пациентов</h1>
        <p class="lead reveal">Список вопросов загружается асинхронно из JSON через API без перезагрузки страницы.</p>
    </div>
</section>

<section class="section">
    <div class="container faq-wrapper">
        <div class="faq-top reveal">
            <h2>Перед визитом полезно знать</h2>
            <button class="btn btn-secondary" id="reloadFaqBtn" type="button">Обновить FAQ</button>
        </div>
        <div id="faqList" class="faq-list" aria-live="polite"></div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>