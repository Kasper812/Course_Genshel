<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Контакты';
$currentPage = 'contacts';

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">Контакты</p>
        <h1 class="reveal">Приезжайте в клинику или оставьте заявку онлайн</h1>
        <p class="lead reveal">
            Наш адрес: <strong><?= e(SITE_ADDRESS) ?></strong>. Работаем ежедневно, чтобы вы могли выбрать удобное время приема.
        </p>
    </div>
</section>

<section class="section">
    <div class="container contacts-grid">
        <article class="glass-card reveal">
            <h2>Контактная информация</h2>
            <ul class="contact-list">
                <li><strong>Адрес:</strong> <?= e(SITE_ADDRESS) ?></li>
                <li><strong>Телефон:</strong> <a href="tel:+73912004577"><?= e(SITE_PHONE) ?></a></li>
                <li><strong>Email:</strong> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
                <li><strong>График работы:</strong> <?= e(SITE_HOURS) ?></li>
                <li><strong>Лицензия:</strong> <?= e(SITE_LICENSE) ?></li>
            </ul>

            <div class="map-placeholder" aria-label="Схема расположения клиники">
                <h3>Клиника на карте</h3>
                <p><?= e(SITE_ADDRESS) ?></p>
                <small>До нас удобно добраться из центра города на автомобиле и общественном транспорте.</small>
            </div>

            <div class="contact-badges">
                <span>Парковка рядом</span>
                <span>Удобная зона ожидания</span>
                <span>Прием без очередей</span>
            </div>
        </article>

        <article class="glass-card reveal delay-1">
            <h2>Запрос на консультацию</h2>
            <form class="form js-feedback-form" id="contactForm" novalidate>
                <div class="form-row">
                    <label for="contactName">Имя</label>
                    <input id="contactName" type="text" name="name" required minlength="2" maxlength="70" placeholder="Как к вам обращаться">
                </div>

                <div class="form-row">
                    <label for="contactPhone">Телефон</label>
                    <input id="contactPhone" type="tel" name="phone" required maxlength="30" placeholder="+7 (___) ___-__-__">
                </div>

                <div class="form-row">
                    <label for="contactEmail">Email</label>
                    <input id="contactEmail" type="email" name="email" required maxlength="120" placeholder="name@example.com">
                </div>

                <div class="form-row">
                    <label for="contactSubject">Причина обращения</label>
                    <input id="contactSubject" type="text" name="subject" required minlength="3" maxlength="120" placeholder="Боль, консультация, имплантация...">
                </div>

                <div class="form-row">
                    <label for="contactMessage">Комментарий</label>
                    <textarea id="contactMessage" name="message" rows="5" required minlength="10" maxlength="1500" placeholder="Опишите кратко ваш вопрос"></textarea>
                </div>

                <input type="hidden" name="source_page" value="contacts">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                <input class="hp-field" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

                <button class="btn btn-primary" type="submit">Отправить заявку</button>
            </form>
        </article>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>