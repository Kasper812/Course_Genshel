<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Онлайн-запись';
$currentPage = 'feedback';

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">Запись и расчет</p>
        <h1 class="reveal">Оставьте заявку в клинику и получите предварительную оценку лечения</h1>
        <p class="lead reveal">Формы работают через AJAX: без перезагрузки страницы, с валидацией и сохранением в JSON.</p>
    </div>
</section>

<section class="section">
    <div class="container split-grid">
        <article class="glass-card reveal">
            <h2>Быстрая запись к врачу</h2>
            <form class="form js-feedback-form" id="feedbackForm" novalidate>
                <div class="form-row">
                    <label for="feedbackName">Имя</label>
                    <input id="feedbackName" type="text" name="name" required minlength="2" maxlength="70" placeholder="Как к вам обращаться">
                </div>

                <div class="form-row">
                    <label for="feedbackPhone">Телефон</label>
                    <input id="feedbackPhone" type="tel" name="phone" required maxlength="30" placeholder="+7 (___) ___-__-__">
                </div>

                <div class="form-row">
                    <label for="feedbackEmail">Email</label>
                    <input id="feedbackEmail" type="email" name="email" required maxlength="120" placeholder="name@example.com">
                </div>

                <div class="form-row">
                    <label for="feedbackSubject">Направление</label>
                    <input id="feedbackSubject" type="text" name="subject" required minlength="3" maxlength="120" placeholder="Имплантация, лечение кариеса, ортодонтия...">
                </div>

                <div class="form-row">
                    <label for="feedbackMessage">Комментарий к записи</label>
                    <textarea id="feedbackMessage" name="message" rows="6" required minlength="10" maxlength="1500" placeholder="Опишите задачу или симптомы"></textarea>
                </div>

                <input type="hidden" name="source_page" value="feedback">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                <input class="hp-field" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

                <button class="btn btn-primary" type="submit">Отправить заявку</button>
            </form>
        </article>

        <article class="glass-card reveal delay-1">
            <h2>Калькулятор стоимости лечения</h2>
            <form class="form" id="calculatorForm" novalidate>
                <div class="form-row">
                    <label for="calcServiceType">Тип услуги</label>
                    <select id="calcServiceType" name="service_type" required>
                        <option value="diagnostics">Цифровая диагностика</option>
                        <option value="therapy">Терапевтическое лечение</option>
                        <option value="implants">Имплантация</option>
                        <option value="prosthetics">Протезирование</option>
                        <option value="ortho">Ортодонтия</option>
                        <option value="surgery">Хирургическая стоматология</option>
                        <option value="kids">Детская стоматология</option>
                        <option value="aesthetics">Эстетическая стоматология</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="calcComplexity">Сложность случая</label>
                    <select id="calcComplexity" name="complexity" required>
                        <option value="basic">Базовая</option>
                        <option value="advanced">Повышенная</option>
                        <option value="expert">Сложная</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="calcUrgency">Срочность</label>
                    <select id="calcUrgency" name="urgency" required>
                        <option value="standard">Плановый прием</option>
                        <option value="fast">Ускоренная запись</option>
                        <option value="urgent">Экстренно</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="calcTeamSize">Количество специалистов в плане</label>
                    <input id="calcTeamSize" type="number" name="team_size" min="1" max="15" value="2" required>
                </div>

                <div class="form-row checkbox-row">
                    <label>
                        <input id="calcSupport" type="checkbox" name="support_24" value="1">
                        Нужен расширенный сервис сопровождения
                    </label>
                </div>

                <div class="calc-result" id="calcResult">
                    <span>Предварительная стоимость:</span>
                    <strong id="calcTotal">0 руб.</strong>
                </div>

                <div class="form-row">
                    <label for="calcClientName">Контактное лицо</label>
                    <input id="calcClientName" type="text" name="name" required minlength="2" maxlength="70">
                </div>

                <div class="form-row">
                    <label for="calcClientPhone">Телефон</label>
                    <input id="calcClientPhone" type="tel" name="phone" required maxlength="30">
                </div>

                <div class="form-row">
                    <label for="calcClientEmail">Email</label>
                    <input id="calcClientEmail" type="email" name="email" required maxlength="120">
                </div>

                <div class="form-row">
                    <label for="calcComment">Комментарий</label>
                    <textarea id="calcComment" name="comment" rows="4" maxlength="700"></textarea>
                </div>

                <input type="hidden" name="source_page" value="calculator">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                <input class="hp-field" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

                <button class="btn btn-primary" type="submit">Отправить расчет</button>
            </form>
        </article>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>