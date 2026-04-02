<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'О клинике';
$currentPage = 'about';
$doctors = load_doctors();

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">О клинике</p>
        <h1 class="reveal"><?= e(SITE_NAME) ?>: стоматология, где технологии работают на комфорт пациента</h1>
        <p class="lead reveal">
            Мы строим лечение на доказательных протоколах, цифровой диагностике и персональном подходе.
            Пациент всегда понимает план, стоимость и ожидаемый результат на каждом этапе.
        </p>
    </div>
</section>

<section class="section">
    <div class="container split-grid">
        <article class="glass-card reveal">
            <h2>Наша миссия</h2>
            <p>
                Делать высокотехнологичную стоматологию доступной и понятной для жителей Красноярска.
                Не просто лечить зубы, а возвращать уверенность в себе и качество жизни.
            </p>
            <ul class="check-list">
                <li>Прозрачная диагностика и план лечения</li>
                <li>Бережные методики и современная анестезия</li>
                <li>Долгосрочное сопровождение после лечения</li>
            </ul>
        </article>

        <article class="glass-card reveal delay-1">
            <h2>Наши ценности</h2>
            <ul class="check-list">
                <li>Медицинская точность и безопасность процедур</li>
                <li>Эмпатия и уважение к каждому пациенту</li>
                <li>Эстетика результата без компромисса по функции</li>
                <li>Ответственность команды за конечный результат</li>
            </ul>
        </article>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Как мы работаем</p>
            <h2>Маршрут пациента от первого визита до стабильного результата</h2>
        </div>

        <div class="timeline">
            <article class="timeline-item reveal">
                <span class="timeline-step">01</span>
                <div>
                    <h3>Диагностика и консультация</h3>
                    <p>Проводим КТ, осмотр и цифровой фотопротокол, обсуждаем жалобы и цели лечения.</p>
                </div>
            </article>
            <article class="timeline-item reveal delay-1">
                <span class="timeline-step">02</span>
                <div>
                    <h3>Персональный план</h3>
                    <p>Составляем этапы лечения с ориентиром по срокам, бюджету и ожидаемому клиническому результату.</p>
                </div>
            </article>
            <article class="timeline-item reveal delay-2">
                <span class="timeline-step">03</span>
                <div>
                    <h3>Лечение и контроль</h3>
                    <p>Проводим процедуры по протоколу, фиксируем динамику и информируем пациента о каждом шаге.</p>
                </div>
            </article>
            <article class="timeline-item reveal delay-3">
                <span class="timeline-step">04</span>
                <div>
                    <h3>Профилактика и поддержка</h3>
                    <p>Назначаем профилактические визиты и персональный домашний уход для стабильного эффекта.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Экспертность</p>
            <h2>Команда специалистов разных направлений</h2>
        </div>

        <div class="doctors-grid compact">
            <?php foreach ($doctors as $doctor): ?>
                <article class="doctor-card tilt-card reveal">
                    <img class="doctor-photo" src="<?= e(media_url((string) ($doctor['photo_url'] ?? ''))) ?>" alt="<?= e((string) ($doctor['name'] ?? 'Врач')) ?>" loading="lazy">
                    <h3><?= e((string) ($doctor['name'] ?? 'Врач')) ?></h3>
                    <p class="doctor-role"><?= e((string) ($doctor['position'] ?? '')) ?></p>
                    <p><?= e((string) ($doctor['specialization'] ?? '')) ?></p>
                    <div class="doctor-meta"><?= e((string) ($doctor['experience'] ?? '')) ?></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Ключевые показатели</p>
            <h2>Числа, которые подтверждают уровень клиники</h2>
        </div>

        <div class="stats-grid">
            <article class="stat-card reveal">
                <span class="stat-number js-counter" data-target="18">0</span>
                <p>лет практики команды</p>
            </article>
            <article class="stat-card reveal delay-1">
                <span class="stat-number js-counter" data-target="12000">0</span>
                <p>клинических случаев</p>
            </article>
            <article class="stat-card reveal delay-2">
                <span class="stat-number js-counter" data-target="96">0</span>
                <p>% пациентов рекомендуют нас</p>
            </article>
            <article class="stat-card reveal delay-3">
                <span class="stat-number js-counter" data-target="30">0</span>
                <p>минут среднее ожидание приема</p>
            </article>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>