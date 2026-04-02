# AstraDent Clinic - Курсовой веб-проект

Полностью готовое веб-приложение стоматологического центра на **HTML + CSS + JavaScript + PHP + AJAX**.

Проект выполнен без базы данных, без фреймворков и без сборщиков: все данные хранятся в JSON-файлах.

## Что реализовано

- Премиальный адаптивный дизайн в медицинской стилистике (градиенты, glassmorphism, карточки, анимации).
- Плавные переходы между страницами, preloader, reveal-анимации, toasts, модальные окна, кнопка "наверх".
- Переключение темной/светлой темы с сохранением в `localStorage`.
- Главная страница с hero-слайдером, интерактивным блоком сценариев пациента, услугами, врачами и отзывами.
- Страница услуг с AJAX-фильтрацией и поиском без перезагрузки.
- Страница контактов с формой обратной связи и серверной валидацией.
- Страница записи с калькулятором стоимости лечения (AJAX + серверный расчет).
- Страница FAQ с AJAX-загрузкой вопросов.
- Демо-админ панель для просмотра заявок, подписчиков, статистики, поиска и фильтрации.

## AJAX-сценарии

1. Отправка формы обратной связи: `POST /api/feedback.php`
2. Подписка на новости: `POST /api/subscribe.php`
3. Загрузка FAQ: `GET /api/faq.php`
4. Отправка расчета калькулятора: `POST /api/calculator.php`
5. Подгрузка услуг и деталей: `GET /api/services.php`

## Безопасность

- Проверка HTTP-метода в API (`GET/POST`).
- Проверка AJAX-заголовка `X-Requested-With` для POST API.
- CSRF-токен для форм.
- Honeypot-поле против простого спама.
- Клиентская и серверная валидация.
- Фильтрация и нормализация ввода (`trim`, `strip_tags`, ограничения длины, email/phone).
- Безопасный вывод через `htmlspecialchars`.
- Безопасная запись JSON с `flock` (блокировка файла) и обработкой ошибок.
- Корректные JSON-ответы с HTTP-кодами.

## Архитектура

```text
/project
  index.php
  /pages
    about.php
    services.php
    contacts.php
    feedback.php
    faq.php
    admin-demo.php
  /assets
    /css
      style.css
      responsive.css
    /js
      main.js
      ajax.js
      ui.js
  /includes
    config.php
    functions.php
    header.php
    footer.php
  /api
    feedback.php
    subscribe.php
    calculator.php
    faq.php
    services.php
  /data
    services.json
    doctors.json
    reviews.json
    faq.json
    feedback.json
    subscribers.json
    calculator_requests.json
  /logs
    .gitkeep
  README.md
```

## Где что используется

- **HTML/PHP-шаблоны**: `index.php`, `pages/*.php`, `includes/header.php`, `includes/footer.php`
- **CSS-дизайн и адаптив**: `assets/css/style.css`, `assets/css/responsive.css`
- **JS-интерактивность и UI**: `assets/js/main.js`, `assets/js/ui.js`
- **AJAX-клиент**: `assets/js/ajax.js`
- **PHP API + валидация + безопасность**: `api/*.php`, `includes/functions.php`
- **Данные без БД**: `data/*.json`

## Технологии

- HTML5
- CSS3
- Vanilla JavaScript (ES6+)
- PHP 8+
- AJAX (`fetch`)
- JSON-файлы как хранилище

## Запуск

1. Установить PHP.
2. Открыть папку проекта в терминале.
3. Выполнить:

```bash
php -S localhost:8000
```

4. Открыть в браузере:

```text
http://localhost:8000
```

Если `php` не в PATH, можно запустить так:

```bash
C:\OSPanel\modules\PHP-8.1\php.exe -S localhost:8000
```

## Примечание

Проект готов к демонстрации на защите: запуск в один шаг, рабочие формы, AJAX, серверная логика, адаптивный UI и базовые меры безопасности.
## Изображения

В проект добавлены тематические фотографии стоматологии из открытых фотостоков (Pexels) по прямым URL.

Примеры использованных источников:
- https://www.pexels.com/photo/portrait-of-smiling-dentist-in-clinic-6627836/
- https://www.pexels.com/photo/dentist-performing-dental-treatment-5622021/
- https://www.pexels.com/photo/a-man-dentist-in-blue-scrub-suit-is-checking-up-a-patient-s-teeth-6812453/
- https://www.pexels.com/photo/photo-of-woman-in-blue-shirt-lying-on-dental-chair-5355921/

Примечание: для отображения внешних изображений требуется доступ к интернету.