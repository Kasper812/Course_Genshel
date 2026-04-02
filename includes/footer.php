<?php
?>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <section>
                <h3><?= e(SITE_SHORT_NAME) ?>, Красноярск</h3>
                <p><?= e(SITE_TAGLINE) ?></p>
                <p class="muted"><?= e(SITE_ADDRESS) ?></p>
                <p class="muted">Лицензия: <?= e(SITE_LICENSE) ?></p>
            </section>

            <section>
                <h3>Быстрые ссылки</h3>
                <ul class="footer-list">
                    <li><a href="<?= e(app_url('pages/services.php')) ?>">Направления лечения</a></li>
                    <li><a href="<?= e(app_url('pages/about.php')) ?>">Команда врачей</a></li>
                    <li><a href="<?= e(app_url('pages/contacts.php')) ?>">Как добраться</a></li>
                    <li><a href="<?= e(app_url('pages/faq.php')) ?>">Вопросы и ответы</a></li>
                </ul>
            </section>

            <section>
                <h3>Подписка на новости</h3>
                <form class="subscribe-form" id="subscribeForm" novalidate>
                    <label for="subscribeEmail">Акции, профилактика, рекомендации врачей</label>
                    <input id="subscribeEmail" type="email" name="email" placeholder="Ваш email" required>
                    <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                    <input class="hp-field" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <button class="btn btn-primary" type="submit">Подписаться</button>
                </form>
            </section>

            <section>
                <h3>Контакты</h3>
                <ul class="footer-list">
                    <li>Телефон: <a href="tel:+73912004577"><?= e(SITE_PHONE) ?></a></li>
                    <li>Email: <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
                    <li>График: <?= e(SITE_HOURS) ?></li>
                </ul>
                <a class="btn btn-ghost btn-small" href="<?= e(app_url('pages/feedback.php')) ?>">Записаться онлайн</a>
            </section>
        </div>

        <div class="container footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Учебный проект для курсовой работы.</span>
            <a href="<?= e(app_url('pages/admin-demo.php')) ?>">Демо-панель</a>
        </div>
    </footer>

    <button class="to-top" id="toTopBtn" type="button" aria-label="Наверх">↑</button>

    <div class="page-transition" id="pageTransition" aria-hidden="true"></div>

    <div class="modal" id="appModal" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="modal-overlay" data-close-modal></div>
        <div class="modal-dialog" role="document">
            <button class="modal-close" type="button" data-close-modal aria-label="Закрыть">×</button>
            <div class="modal-content" id="appModalContent"></div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

    <script src="<?= e(app_url('assets/js/ui.js')) ?>"></script>
    <script src="<?= e(app_url('assets/js/main.js')) ?>"></script>
    <script src="<?= e(app_url('assets/js/ajax.js')) ?>"></script>
</body>
</html>