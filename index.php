<?php

require_once __DIR__ . '/includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Главная';
$currentPage = 'home';
$services = load_services();
$featuredServices = array_slice($services, 0, 8);
$doctors = load_doctors();
$reviews = load_reviews();
$galleryImages = [
    [
        'url' => 'assets/img/clinic_real_exterior.png',
        'title' => 'Фасад Genshel Clinics',
    ],
    [
        'url' => 'assets/img/clinic_real_hall.png',
        'title' => 'Холл и ресепшен',
    ],
    [
        'url' => 'assets/img/clinic_real_cabinet1.png',
        'title' => 'Кабинет №1',
    ],
    [
        'url' => 'assets/img/clinic_real_cabinet2.png',
        'title' => 'Кабинет №2',
    ],
];

include INCLUDES_PATH . '/header.php';
?>
<section class="hero-section hero-luxury">
    <div class="brand-heading">
        <h1>Genshel clinics</h1>
        <p>Стоматологическая клиника</p>
    </div>
    <div class="container hero-grid">
        <div class="hero-content reveal">
            <p class="kicker">Премиальная стоматология в Красноярске</p>
            <h1>Точная диагностика, бережное лечение и улыбка, которой вы будете гордиться</h1>
            <p class="hero-text">
                <?= e(SITE_NAME) ?> объединяет опытных врачей, цифровые технологии и сервис уровня private clinic.
                Запишитесь онлайн и получите персональный план лечения уже на первой консультации.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= e(app_url('pages/feedback.php')) ?>">Записаться на прием</a>
                <a class="btn btn-secondary" href="<?= e(app_url('pages/services.php')) ?>">Смотреть услуги</a>
            </div>
            <div class="hero-meta">
                <span><?= e(SITE_ADDRESS) ?></span>
                <span><?= e(SITE_HOURS) ?></span>
                <span>Лицензия: <?= e(SITE_LICENSE) ?></span>
            </div>

            <div class="hero-problems" id="heroProblems">
                <p>Частый запрос пациентов:</p>
                <div class="problem-chips">
                    <button class="problem-chip active" type="button" data-problem="pain">Острая боль</button>
                    <button class="problem-chip" type="button" data-problem="implant">Нет зуба</button>
                    <button class="problem-chip" type="button" data-problem="bite">Неровный прикус</button>
                    <button class="problem-chip" type="button" data-problem="kids">Ребенок боится стоматолога</button>
                </div>
                <div class="problem-display" id="problemDisplay">
                    <strong>Острая боль:</strong> примем в день обращения и снимем симптом уже на первом визите.
                </div>
            </div>
        </div>

        <div class="hero-panel reveal delay-1">
            <div class="hero-slider" id="heroSlider">
                <article class="hero-slide active">
                    <h3>Цифровая диагностика</h3>
                    <p>КТ, 3D-планирование и точный расчет лечения без сюрпризов по срокам и стоимости.</p>
                    <span class="slide-note">Точность на старте и прогнозируемый результат</span>
                </article>
                <article class="hero-slide">
                    <h3>Имплантация и протезирование</h3>
                    <p>Комплексное восстановление зубов под ключ с бережной хирургией и эстетикой.</p>
                    <span class="slide-note">Функциональность и естественная красота улыбки</span>
                </article>
                <article class="hero-slide">
                    <h3>Сервис без стресса</h3>
                    <p>Заботливые врачи, комфортные кабинеты и подробное сопровождение на каждом этапе.</p>
                    <span class="slide-note">Пациент понимает, что и зачем мы делаем</span>
                </article>
            </div>
            <div class="hero-dots" id="heroDots">
                <button class="hero-dot active" type="button" data-slide="0" aria-label="Слайд 1"></button>
                <button class="hero-dot" type="button" data-slide="1" aria-label="Слайд 2"></button>
                <button class="hero-dot" type="button" data-slide="2" aria-label="Слайд 3"></button>
            </div>

            <div class="hero-media-grid">
                <img src="<?= e(media_url((string) $galleryImages[0]['url'])) ?>" alt="<?= e($galleryImages[0]['title']) ?>" loading="lazy">
                <img src="<?= e(media_url((string) $galleryImages[1]['url'])) ?>" alt="<?= e($galleryImages[1]['title']) ?>" loading="lazy">
                <?php if (isset($galleryImages[2])): ?>
                    <img src="<?= e(media_url((string) $galleryImages[2]['url'])) ?>" alt="<?= e($galleryImages[2]['title']) ?>" loading="lazy">
                <?php endif; ?>
            </div>

            <a class="btn btn-ghost" href="<?= e(app_url('pages/contacts.php')) ?>">Как нас найти</a>
        </div>
    </div>
</section>

<section class="stats-strip">
    <div class="container stats-strip-grid reveal">
        <article><strong class="js-counter" data-target="18">0</strong><span>лет клинической практики</span></article>
        <article><strong class="js-counter" data-target="12000">0</strong><span>успешных кейсов лечения</span></article>
        <article><strong class="js-counter" data-target="97">0</strong><span>пациентов по рекомендациям</span></article>
        <article><strong class="js-counter" data-target="365">0</strong><span>дней в году на связи</span></article>
    </div>
</section>

<section class="section section-soft" id="services-preview">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Направления лечения</p>
            <h2>Услуги, с которых обычно начинается путь к здоровой улыбке</h2>
        </div>
        <div class="services-grid services-grid-rich" id="homeServicesGrid">
            <?php foreach ($featuredServices as $service): ?>
                <?= render_service_card($service) ?>
            <?php endforeach; ?>
        </div>
        <div class="section-actions reveal">
            <a class="btn btn-primary" href="<?= e(app_url('pages/services.php')) ?>">Все услуги клиники</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Клиника в деталях</p>
            <h2>Современные кабинеты, оборудование и комфортная атмосфера</h2>
        </div>
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($galleryImages as $item): ?>
                <button class="gallery-item tilt-card reveal" type="button" data-image="<?= e(media_url((string) $item['url'])) ?>" data-title="<?= e($item['title']) ?>">
                    <img src="<?= e(media_url((string) $item['url'])) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                    <span><?= e($item['title']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Команда</p>
            <h2>Врачи, которым доверяют сложные клинические случаи</h2>
        </div>
        <div class="doctors-grid">
            <?php foreach ($doctors as $doctor): ?>
                <article class="doctor-card tilt-card reveal">
                    <img class="doctor-photo" src="<?= e(media_url((string) ($doctor['photo_url'] ?? ''))) ?>" alt="<?= e((string) ($doctor['name'] ?? 'Врач')) ?>" loading="lazy">
                    <h3><?= e((string) ($doctor['name'] ?? 'Врач')) ?></h3>
                    <p class="doctor-role"><?= e((string) ($doctor['position'] ?? 'Специалист')) ?></p>
                    <p><?= e((string) ($doctor['specialization'] ?? '')) ?></p>
                    <div class="doctor-meta"><?= e((string) ($doctor['experience'] ?? '')) ?></div>
                    <blockquote><?= e((string) ($doctor['quote'] ?? '')) ?></blockquote>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Отзывы пациентов</p>
            <h2>Реальные впечатления после лечения</h2>
        </div>
        <div class="reviews-track" id="reviewsTrack">
            <?php foreach ($reviews as $review): ?>
                <article class="review-card tilt-card reveal">
                    <div class="review-rating">Рейтинг: <?= max(1, min(5, (int) ($review['rating'] ?? 5))) ?>/5</div>
                    <p><?= e((string) ($review['text'] ?? '')) ?></p>
                    <div class="review-author"><?= e((string) ($review['author'] ?? 'Пациент')) ?></div>
                    <small><?= e((string) ($review['service'] ?? '')) ?></small>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-accent">
    <div class="container cta-banner reveal">
        <h2>Нужна консультация стоматолога сегодня?</h2>
        <p>Оставьте заявку в 2 клика. Администратор свяжется с вами, поможет выбрать врача и удобное время приема.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(app_url('pages/feedback.php')) ?>">Записаться онлайн</a>
            <a class="btn btn-ghost" href="<?= e(app_url('pages/faq.php')) ?>">Ответы на вопросы</a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>