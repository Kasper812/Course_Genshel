(function () {
    "use strict";

    function uiToast(message, type, timeout) {
        if (window.UI && typeof window.UI.showToast === "function") {
            window.UI.showToast(message, type, timeout);
        }
    }

    function setLoading(button, loading, text) {
        if (window.UI && typeof window.UI.setButtonLoading === "function") {
            window.UI.setButtonLoading(button, loading, text);
        }
    }

    function escapeHtml(value) {
        if (window.UI && typeof window.UI.escapeHtml === "function") {
            return window.UI.escapeHtml(value);
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute("content") || "" : "";
    }

    function formatRub(value) {
        return new Intl.NumberFormat("ru-RU").format(value) + " руб.";
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePhone(phone) {
        var digits = String(phone || "").replace(/\D+/g, "");
        return digits.length >= 10 && digits.length <= 15;
    }

    async function fetchJson(url, options) {
        var response = await fetch(url, options);
        var data;

        try {
            data = await response.json();
        } catch (error) {
            data = { success: false, message: "Некорректный ответ сервера." };
        }

        if (!response.ok || data.success === false) {
            var errorMessage = (data && data.message) || "Не удалось выполнить запрос.";
            var errorObject = new Error(errorMessage);
            errorObject.payload = data;
            throw errorObject;
        }

        return data;
    }

    function clearInvalid(form) {
        form.querySelectorAll(".is-invalid").forEach(function (node) {
            node.classList.remove("is-invalid");
        });
    }

    function markInvalid(form, fieldNames) {
        fieldNames.forEach(function (name) {
            var field = form.querySelector('[name="' + name + '"]');
            if (field) {
                field.classList.add("is-invalid");
            }
        });
    }

    function collectFeedbackErrors(formData) {
        var errors = [];
        var name = String(formData.get("name") || "").trim();
        var phone = String(formData.get("phone") || "").trim();
        var email = String(formData.get("email") || "").trim();
        var subject = String(formData.get("subject") || "").trim();
        var message = String(formData.get("message") || "").trim();

        if (name.length < 2) {
            errors.push("name");
        }
        if (!validatePhone(phone)) {
            errors.push("phone");
        }
        if (!validateEmail(email)) {
            errors.push("email");
        }
        if (subject.length < 3) {
            errors.push("subject");
        }
        if (message.length < 10) {
            errors.push("message");
        }

        return errors;
    }

    function setupFeedbackForms() {
        var forms = document.querySelectorAll(".js-feedback-form");
        if (!forms.length) {
            return;
        }

        forms.forEach(function (form) {
            form.addEventListener("submit", async function (event) {
                event.preventDefault();
                clearInvalid(form);

                var submitButton = form.querySelector('button[type="submit"]');
                var formData = new FormData(form);

                if (!formData.get("csrf_token")) {
                    formData.append("csrf_token", getCsrfToken());
                }

                var clientErrors = collectFeedbackErrors(formData);
                if (clientErrors.length) {
                    markInvalid(form, clientErrors);
                    uiToast("Проверьте поля формы перед отправкой.", "error");
                    return;
                }

                setLoading(submitButton, true, "Отправляем...");

                try {
                    var data = await fetchJson("/api/feedback.php", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });

                    form.reset();
                    uiToast(data.message || "Заявка отправлена.", "success");
                } catch (error) {
                    var payload = error.payload || {};
                    if (payload.errors) {
                        markInvalid(form, Object.keys(payload.errors));
                    }
                    uiToast(error.message || "Ошибка отправки формы.", "error");
                } finally {
                    setLoading(submitButton, false);
                }
            });
        });
    }

    function setupSubscribeForm() {
        var form = document.getElementById("subscribeForm");
        if (!form) {
            return;
        }

        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            clearInvalid(form);

            var submitButton = form.querySelector('button[type="submit"]');
            var formData = new FormData(form);
            formData.set("source_page", document.body.getAttribute("data-page") || "footer");

            if (!validateEmail(String(formData.get("email") || "").trim())) {
                markInvalid(form, ["email"]);
                uiToast("Введите корректный email.", "error");
                return;
            }

            setLoading(submitButton, true, "Подписываем...");

            try {
                var data = await fetchJson("/api/subscribe.php", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                form.reset();
                uiToast(data.message || "Подписка оформлена.", "success");
            } catch (error) {
                uiToast(error.message || "Не удалось оформить подписку.", "error");
            } finally {
                setLoading(submitButton, false);
            }
        });
    }

    function renderFaq(items, container) {
        if (!items.length) {
            container.innerHTML = '<p class="muted">Вопросы пока не добавлены.</p>';
            return;
        }

        container.innerHTML = items
            .map(function (item) {
                return (
                    '<article class="faq-item">' +
                    '<button class="faq-question" type="button">' + escapeHtml(item.question || "Без вопроса") + "</button>" +
                    '<div class="faq-answer"><p>' + escapeHtml(item.answer || "") + "</p></div>" +
                    "</article>"
                );
            })
            .join("");
    }

    function setupFaqLoader() {
        var container = document.getElementById("faqList");
        if (!container) {
            return;
        }

        var reloadBtn = document.getElementById("reloadFaqBtn");

        async function loadFaq() {
            container.innerHTML = '<p class="muted">Загрузка FAQ...</p>';
            if (reloadBtn) {
                setLoading(reloadBtn, true, "Обновляем...");
            }

            try {
                var data = await fetchJson("/api/faq.php", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });
                renderFaq(data.items || [], container);
            } catch (error) {
                container.innerHTML = '<p class="muted">Не удалось загрузить FAQ.</p>';
                uiToast(error.message || "Ошибка загрузки FAQ", "error");
            } finally {
                if (reloadBtn) {
                    setLoading(reloadBtn, false);
                }
            }
        }

        container.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement) || !target.classList.contains("faq-question")) {
                return;
            }

            var item = target.closest(".faq-item");
            if (!item) {
                return;
            }

            item.classList.toggle("open");
            var answer = item.querySelector(".faq-answer");
            if (!answer) {
                return;
            }

            if (item.classList.contains("open")) {
                answer.style.maxHeight = answer.scrollHeight + "px";
            } else {
                answer.style.maxHeight = "0px";
            }
        });

        if (reloadBtn) {
            reloadBtn.addEventListener("click", loadFaq);
        }

        loadFaq();
    }

    function buildServiceCard(service) {
        var id = Number(service.id || 0);
        var category = escapeHtml(service.category || "");
        var categoryLabel = escapeHtml(service.category_label || "Категория");
        var price = formatRub(Number(service.price_from || 0));
        var title = escapeHtml(service.title || "Услуга");
        var shortText = escapeHtml(service.short || "");
        var duration = escapeHtml(service.duration || "");
        var image = escapeHtml(service.image || "");

        return (
            '<article class="service-card tilt-card reveal show" data-category="' + category + '">' +
            '<div class="service-thumb-wrap">' +
            '<img class="service-thumb" src="' + image + '" alt="' + title + '" loading="lazy">' +
            "</div>" +
            '<div class="service-meta">' +
            '<span class="badge">' + categoryLabel + "</span>" +
            '<span class="price">от ' + price + "</span>" +
            "</div>" +
            "<h3>" + title + "</h3>" +
            "<p>" + shortText + "</p>" +
            '<div class="service-footer">' +
            '<span class="duration">Срок: ' + duration + "</span>" +
            '<button class="btn btn-small btn-ghost js-service-details" type="button" data-service-id="' + id + '">Подробнее</button>' +
            "</div>" +
            "</article>"
        );
    }

    function setupServicesAjax() {
        var grid = document.getElementById("servicesGrid");
        if (!grid) {
            return;
        }

        var filterRoot = document.getElementById("serviceCategoryFilters");
        var searchInput = document.getElementById("serviceSearch");
        var state = {
            category: "all",
            search: ""
        };

        function setActiveCategoryButton(category) {
            if (!filterRoot) {
                return;
            }

            filterRoot.querySelectorAll(".filter-btn").forEach(function (btn) {
                btn.classList.toggle("active", btn.getAttribute("data-category") === category);
            });
        }

        async function loadServices() {
            var params = new URLSearchParams();
            params.set("category", state.category);
            if (state.search.trim()) {
                params.set("search", state.search.trim());
            }

            grid.innerHTML = '<p class="muted">Загрузка услуг...</p>';

            try {
                var data = await fetchJson("/api/services.php?" + params.toString(), {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                var items = Array.isArray(data.items) ? data.items : [];
                if (!items.length) {
                    grid.innerHTML = '<p class="muted">Ничего не найдено. Измените фильтр или поисковый запрос.</p>';
                    return;
                }

                grid.innerHTML = items.map(buildServiceCard).join("");
            } catch (error) {
                grid.innerHTML = '<p class="muted">Не удалось загрузить услуги.</p>';
                uiToast(error.message || "Ошибка загрузки услуг", "error");
            }
        }

        if (filterRoot) {
            filterRoot.addEventListener("click", function (event) {
                var target = event.target;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                var button = target.closest(".filter-btn");
                if (!button) {
                    return;
                }

                state.category = button.getAttribute("data-category") || "all";
                setActiveCategoryButton(state.category);
                loadServices();
            });
        }

        if (searchInput) {
            var timer;
            searchInput.addEventListener("input", function () {
                window.clearTimeout(timer);
                timer = window.setTimeout(function () {
                    state.search = searchInput.value || "";
                    loadServices();
                }, 260);
            });
        }

        grid.addEventListener("click", async function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var button = target.closest(".js-service-details");
            if (!button) {
                return;
            }

            var id = button.getAttribute("data-service-id");
            if (!id) {
                return;
            }

            try {
                var data = await fetchJson("/api/services.php?id=" + encodeURIComponent(id), {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                var item = data.item || {};
                var image = escapeHtml(item.image || "");
                var features = Array.isArray(item.features)
                    ? '<ul class="modal-list">' + item.features.map(function (feature) {
                        return "<li>" + escapeHtml(feature) + "</li>";
                    }).join("") + "</ul>"
                    : "";

                if (window.UI && typeof window.UI.openModal === "function") {
                    window.UI.openModal({
                        title: item.title || "Детали услуги",
                        bodyHtml:
                            '<img class="modal-image" src="' + image + '" alt="' + escapeHtml(item.title || "Услуга") + '">' +
                            "<p>" + escapeHtml(item.description || "") + "</p>" +
                            "<p><strong>Категория:</strong> " + escapeHtml(item.category_label || "") + "</p>" +
                            "<p><strong>Срок:</strong> " + escapeHtml(item.duration || "") + "</p>" +
                            "<p><strong>Стоимость:</strong> от " + formatRub(Number(item.price_from || 0)) + "</p>" +
                            features
                    });
                }
            } catch (error) {
                uiToast(error.message || "Не удалось получить детали услуги", "error");
            }
        });

        loadServices();
    }

    function setupCalculator() {
        var form = document.getElementById("calculatorForm");
        if (!form) {
            return;
        }

        var totalNode = document.getElementById("calcTotal");

        var prices = {
            diagnostics: 3500,
            therapy: 6500,
            implants: 32000,
            prosthetics: 18500,
            ortho: 95000,
            surgery: 7000,
            kids: 2900,
            aesthetics: 14500
        };

        var complexity = {
            basic: 1,
            advanced: 1.3,
            expert: 1.65
        };

        var urgency = {
            standard: 1,
            fast: 1.18,
            urgent: 1.38
        };

        function getCurrentTotal() {
            var serviceType = String(form.service_type.value || "therapy");
            var complexityType = String(form.complexity.value || "basic");
            var urgencyType = String(form.urgency.value || "standard");
            var teamSize = Number(form.team_size.value || 1);
            var support = form.support_24.checked;

            var base = prices[serviceType] || prices.therapy;
            var complexityFactor = complexity[complexityType] || 1;
            var urgencyFactor = urgency[urgencyType] || 1;
            var teamFactor = 1 + ((Math.max(1, Math.min(15, teamSize)) - 1) * 0.06);
            var supportCost = support ? 5500 : 0;

            return Math.round((base * complexityFactor * urgencyFactor * teamFactor) + supportCost);
        }

        function recalcTotal() {
            var total = getCurrentTotal();
            if (totalNode) {
                totalNode.textContent = formatRub(total);
            }
        }

        ["change", "input"].forEach(function (eventName) {
            form.addEventListener(eventName, function () {
                recalcTotal();
            });
        });

        recalcTotal();

        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            clearInvalid(form);

            var submitButton = form.querySelector('button[type="submit"]');
            var formData = new FormData(form);
            formData.set("client_total", String(getCurrentTotal()));

            var clientErrors = [];
            if (String(formData.get("name") || "").trim().length < 2) {
                clientErrors.push("name");
            }
            if (!validatePhone(String(formData.get("phone") || "").trim())) {
                clientErrors.push("phone");
            }
            if (!validateEmail(String(formData.get("email") || "").trim())) {
                clientErrors.push("email");
            }

            if (clientErrors.length) {
                markInvalid(form, clientErrors);
                uiToast("Проверьте контактные данные в калькуляторе.", "error");
                return;
            }

            setLoading(submitButton, true, "Сохраняем...");

            try {
                var data = await fetchJson("/api/calculator.php", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                if (totalNode && data.total_formatted) {
                    totalNode.textContent = data.total_formatted;
                }

                form.reset();
                recalcTotal();
                uiToast(data.message || "Расчет отправлен.", "success");
            } catch (error) {
                var payload = error.payload || {};
                if (payload.errors) {
                    markInvalid(form, Object.keys(payload.errors));
                }
                uiToast(error.message || "Ошибка отправки расчета.", "error");
            } finally {
                setLoading(submitButton, false);
            }
        });
    }

    function setupAdminFilters() {
        var dashboard = document.getElementById("adminDashboard");
        if (!dashboard) {
            return;
        }

        var searchInput = document.getElementById("adminSearch");
        var typeFilter = document.getElementById("adminTypeFilter");
        var rows = Array.from(document.querySelectorAll("#requestsTable tbody tr"));

        if (!rows.length) {
            return;
        }

        function applyFilter() {
            var query = searchInput ? searchInput.value.trim().toLowerCase() : "";
            var selectedType = typeFilter ? typeFilter.value : "all";

            rows.forEach(function (row) {
                var rowType = row.getAttribute("data-type") || "";
                var text = row.textContent ? row.textContent.toLowerCase() : "";
                var byType = selectedType === "all" || rowType === selectedType;
                var byText = query === "" || text.indexOf(query) !== -1;

                row.style.display = byType && byText ? "" : "none";
            });
        }

        if (searchInput) {
            searchInput.addEventListener("input", applyFilter);
        }
        if (typeFilter) {
            typeFilter.addEventListener("change", applyFilter);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        setupFeedbackForms();
        setupSubscribeForm();
        setupFaqLoader();
        setupServicesAjax();
        setupCalculator();
        setupAdminFilters();
    });
})();