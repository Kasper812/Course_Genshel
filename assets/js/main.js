(function () {
    "use strict";

    function initPreloader() {
        var preloader = document.getElementById("preloader");
        if (!preloader) {
            return;
        }

        window.addEventListener("load", function () {
            window.setTimeout(function () {
                preloader.classList.add("hidden");
            }, 280);
        });
    }

    function initThemeToggle() {
        var root = document.documentElement;
        var toggle = document.getElementById("themeToggle");
        if (!toggle) {
            return;
        }

        var saved = localStorage.getItem("astradent_theme");
        var initialTheme = saved === "dark" || saved === "light" ? saved : "light";
        root.setAttribute("data-theme", initialTheme);

        toggle.addEventListener("click", function () {
            var current = root.getAttribute("data-theme") === "dark" ? "dark" : "light";
            var next = current === "dark" ? "light" : "dark";
            root.setAttribute("data-theme", next);
            localStorage.setItem("astradent_theme", next);

            if (window.UI && typeof window.UI.showToast === "function") {
                window.UI.showToast(next === "dark" ? "Включена темная тема" : "Включена светлая тема", "info", 1800);
            }
        });
    }

    function initMobileMenu() {
        var burger = document.getElementById("burgerMenu");
        var nav = document.getElementById("siteNav");
        if (!burger || !nav) {
            return;
        }

        burger.addEventListener("click", function () {
            var isOpen = document.body.classList.toggle("nav-open");
            burger.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });

        nav.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            if (target.classList.contains("nav-link")) {
                document.body.classList.remove("nav-open");
                burger.setAttribute("aria-expanded", "false");
            }
        });
    }

    function initSmoothScroll() {
        document.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var link = target.closest('a[href^="#"]');
            if (!link) {
                return;
            }

            var href = link.getAttribute("href") || "";
            if (href.length < 2) {
                return;
            }

            var section = document.querySelector(href);
            if (!section) {
                return;
            }

            event.preventDefault();
            section.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    function initRevealAnimation() {
        var revealNodes = document.querySelectorAll(".reveal");
        if (!revealNodes.length) {
            return;
        }

        if (!("IntersectionObserver" in window)) {
            revealNodes.forEach(function (node) {
                node.classList.add("show");
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealNodes.forEach(function (node) {
            observer.observe(node);
        });
    }

    function animateNumber(node, target) {
        var duration = 1200;
        var start = 0;
        var startTime = null;

        function tick(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var value = Math.floor(progress * (target - start) + start);
            node.textContent = String(value);

            if (progress < 1) {
                window.requestAnimationFrame(tick);
            } else {
                node.textContent = String(target);
            }
        }

        window.requestAnimationFrame(tick);
    }

    function initCounters() {
        var counters = document.querySelectorAll(".js-counter");
        if (!counters.length) {
            return;
        }

        var started = new WeakSet();

        if (!("IntersectionObserver" in window)) {
            counters.forEach(function (counter) {
                var target = Number(counter.getAttribute("data-target")) || 0;
                animateNumber(counter, target);
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting || started.has(entry.target)) {
                    return;
                }

                started.add(entry.target);
                var target = Number(entry.target.getAttribute("data-target")) || 0;
                animateNumber(entry.target, target);
            });
        }, { threshold: 0.35 });

        counters.forEach(function (counter) {
            observer.observe(counter);
        });
    }

    function initToTopButton() {
        var btn = document.getElementById("toTopBtn");
        if (!btn) {
            return;
        }

        function onScroll() {
            if (window.scrollY > 300) {
                btn.classList.add("visible");
            } else {
                btn.classList.remove("visible");
            }
        }

        window.addEventListener("scroll", onScroll);
        onScroll();

        btn.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    function initPageTransitions() {
        var transition = document.getElementById("pageTransition");
        if (!transition) {
            return;
        }

        document.addEventListener("click", function (event) {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var link = target.closest("a");
            if (!link) {
                return;
            }

            var href = link.getAttribute("href");
            if (!href || href.startsWith("#") || link.target === "_blank" || link.hasAttribute("download")) {
                return;
            }

            var isInternal = href.startsWith("/") || href.startsWith("index.php") || href.startsWith("pages/");
            if (!isInternal) {
                return;
            }

            event.preventDefault();
            transition.classList.add("active");

            window.setTimeout(function () {
                window.location.href = href;
            }, 220);
        });
    }

    function initHeroSlider() {
        var slider = document.getElementById("heroSlider");
        var dotsRoot = document.getElementById("heroDots");
        if (!slider || !dotsRoot) {
            return;
        }

        var slides = Array.from(slider.querySelectorAll(".hero-slide"));
        var dots = Array.from(dotsRoot.querySelectorAll(".hero-dot"));
        if (!slides.length || !dots.length) {
            return;
        }

        var activeIndex = 0;
        var timer = null;

        function setSlide(index) {
            activeIndex = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) {
                slide.classList.toggle("active", i === activeIndex);
            });
            dots.forEach(function (dot, i) {
                dot.classList.toggle("active", i === activeIndex);
            });
        }

        function startAuto() {
            timer = window.setInterval(function () {
                setSlide(activeIndex + 1);
            }, 5000);
        }

        function restartAuto() {
            if (timer) {
                window.clearInterval(timer);
            }
            startAuto();
        }

        dotsRoot.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var dot = target.closest(".hero-dot");
            if (!dot) {
                return;
            }

            var index = Number(dot.getAttribute("data-slide"));
            if (!Number.isFinite(index)) {
                return;
            }

            setSlide(index);
            restartAuto();
        });

        setSlide(0);
        startAuto();
    }

    function initProblemSelector() {
        var root = document.getElementById("heroProblems");
        var display = document.getElementById("problemDisplay");
        if (!root || !display) {
            return;
        }

        var texts = {
            pain: "<strong>Острая боль:</strong> примем в день обращения и снимем симптом уже на первом визите.",
            implant: "<strong>Нет зуба:</strong> подберем стратегию восстановления с понятными сроками.",
            bite: "<strong>Неровный прикус:</strong> составим ортодонтический план с прогнозом по этапам.",
            kids: "<strong>Детский прием:</strong> мягкая адаптация, спокойная коммуникация и бережное лечение."
        };

        root.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var chip = target.closest(".problem-chip");
            if (!chip) {
                return;
            }

            var key = chip.getAttribute("data-problem") || "pain";
            root.querySelectorAll(".problem-chip").forEach(function (btn) {
                btn.classList.toggle("active", btn === chip);
            });

            display.style.opacity = "0";
            window.setTimeout(function () {
                display.innerHTML = texts[key] || texts.pain;
                display.style.opacity = "1";
            }, 120);
        });
    }

    function initTiltCards() {
        var cards = document.querySelectorAll(".tilt-card");
        if (!cards.length || window.matchMedia("(pointer: coarse)").matches) {
            return;
        }

        cards.forEach(function (card) {
            card.addEventListener("mousemove", function (event) {
                var rect = card.getBoundingClientRect();
                var x = event.clientX - rect.left;
                var y = event.clientY - rect.top;

                var rotateY = ((x / rect.width) - 0.5) * 6;
                var rotateX = ((y / rect.height) - 0.5) * -6;

                card.style.transform = "perspective(900px) rotateX(" + rotateX + "deg) rotateY(" + rotateY + "deg) translateY(-4px)";
            });

            card.addEventListener("mouseleave", function () {
                card.style.transform = "";
            });
        });
    }

    function initGalleryModal() {
        var gallery = document.getElementById("galleryGrid");
        if (!gallery || !window.UI || typeof window.UI.openModal !== "function") {
            return;
        }

        gallery.addEventListener("click", function (event) {
            var target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            var btn = target.closest(".gallery-item");
            if (!btn) {
                return;
            }

            var image = btn.getAttribute("data-image") || "";
            var title = btn.getAttribute("data-title") || "Фото клиники";

            window.UI.openModal({
                title: title,
                bodyHtml: '<img class="modal-image" src="' + image + '" alt="' + title + '">' +
                    '<p>Фотография клиники и оборудования для визуальной демонстрации проекта.</p>'
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initPreloader();
        initThemeToggle();
        initMobileMenu();
        initSmoothScroll();
        initRevealAnimation();
        initCounters();
        initToTopButton();
        initPageTransitions();
        initHeroSlider();
        initProblemSelector();
        initTiltCards();
        initGalleryModal();
    });
})();