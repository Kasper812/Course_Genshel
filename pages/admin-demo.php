<?php

require_once __DIR__ . '/../includes/config.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Демо-админ панель';
$currentPage = 'admin';

$feedbackList = sort_by_date_desc(read_json_file(DATA_FEEDBACK_FILE, []));
$subscriberList = sort_by_date_desc(read_json_file(DATA_SUBSCRIBERS_FILE, []));
$calculatorList = sort_by_date_desc(read_json_file(DATA_CALCULATOR_FILE, []));

$allRequests = [];

foreach ($feedbackList as $item) {
    $allRequests[] = [
        'type' => 'feedback',
        'type_label' => 'Заявка на консультацию',
        'name' => (string) ($item['name'] ?? ''),
        'phone' => (string) ($item['phone'] ?? ''),
        'email' => (string) ($item['email'] ?? ''),
        'subject' => (string) ($item['subject'] ?? ''),
        'price' => '',
        'created_at' => (string) ($item['created_at'] ?? ''),
    ];
}

foreach ($calculatorList as $item) {
    $allRequests[] = [
        'type' => 'calculator',
        'type_label' => 'Калькулятор лечения',
        'name' => (string) ($item['name'] ?? ''),
        'phone' => (string) ($item['phone'] ?? ''),
        'email' => (string) ($item['email'] ?? ''),
        'subject' => (string) ($item['service_type_label'] ?? 'Калькулятор услуги'),
        'price' => isset($item['total']) ? format_price((int) $item['total']) : '',
        'created_at' => (string) ($item['created_at'] ?? ''),
    ];
}

$allRequests = sort_by_date_desc($allRequests);

include INCLUDES_PATH . '/header.php';
?>
<section class="page-hero section-soft">
    <div class="container">
        <p class="kicker reveal">Demo only</p>
        <h1 class="reveal">Демонстрационная панель администратора (без авторизации)</h1>
        <p class="lead reveal">Эта страница создана только для защиты курсового проекта и демонстрации обработки данных.</p>
    </div>
</section>

<section class="section" id="adminDashboard">
    <div class="container">
        <div class="stats-grid admin-stats">
            <article class="stat-card reveal">
                <span class="stat-number"><?= (int) count($feedbackList) ?></span>
                <p>заявок на консультацию</p>
            </article>
            <article class="stat-card reveal delay-1">
                <span class="stat-number"><?= (int) count($calculatorList) ?></span>
                <p>запросов из калькулятора</p>
            </article>
            <article class="stat-card reveal delay-2">
                <span class="stat-number"><?= (int) count($subscriberList) ?></span>
                <p>подписчиков рассылки</p>
            </article>
            <article class="stat-card reveal delay-3">
                <span class="stat-number"><?= (int) (count($feedbackList) + count($calculatorList)) ?></span>
                <p>всего клиентских обращений</p>
            </article>
        </div>

        <div class="admin-controls reveal">
            <label for="adminSearch">Поиск по обращениям</label>
            <input id="adminSearch" type="search" placeholder="Имя, email, услуга...">

            <label for="adminTypeFilter">Тип заявки</label>
            <select id="adminTypeFilter">
                <option value="all">Все типы</option>
                <option value="feedback">Консультация</option>
                <option value="calculator">Калькулятор</option>
            </select>
        </div>

        <div class="table-wrap reveal">
            <h2>Последние обращения пациентов</h2>
            <table class="admin-table" id="requestsTable">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Пациент</th>
                        <th>Контакты</th>
                        <th>Запрос</th>
                        <th>Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allRequests as $request): ?>
                        <tr data-type="<?= e($request['type']) ?>">
                            <td><?= e(date('d.m.Y H:i', strtotime($request['created_at']) ?: time())) ?></td>
                            <td><?= e($request['type_label']) ?></td>
                            <td><?= e($request['name']) ?></td>
                            <td>
                                <div><?= e($request['phone']) ?></div>
                                <div><?= e($request['email']) ?></div>
                            </td>
                            <td><?= e($request['subject']) ?></td>
                            <td><?= e($request['price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-wrap reveal">
            <h2>Подписчики новостей</h2>
            <table class="admin-table" id="subscribersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Дата подписки</th>
                        <th>Источник</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscriberList as $index => $subscriber): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= e((string) ($subscriber['email'] ?? '')) ?></td>
                            <td><?= e(date('d.m.Y H:i', strtotime((string) ($subscriber['created_at'] ?? '')) ?: time())) ?></td>
                            <td><?= e((string) ($subscriber['source'] ?? 'footer')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>