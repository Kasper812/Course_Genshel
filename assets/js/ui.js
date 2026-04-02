(function () {
    "use strict";

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getModalElements() {
        return {
            root: document.getElementById("appModal"),
            content: document.getElementById("appModalContent")
        };
    }

    function openModal(payload) {
        const modal = getModalElements();
        if (!modal.root || !modal.content) {
            return;
        }

        const title = payload && payload.title ? `<h3>${escapeHtml(payload.title)}</h3>` : "";
        const bodyHtml = payload && payload.bodyHtml ? payload.bodyHtml : "";

        modal.content.innerHTML = `${title}${bodyHtml}`;
        modal.root.classList.add("is-open");
        modal.root.setAttribute("aria-hidden", "false");
        document.body.classList.add("modal-open");
    }

    function closeModal() {
        const modal = getModalElements();
        if (!modal.root || !modal.content) {
            return;
        }

        modal.root.classList.remove("is-open");
        modal.root.setAttribute("aria-hidden", "true");
        modal.content.innerHTML = "";
        document.body.classList.remove("modal-open");
    }

    function showToast(message, type, timeout) {
        const container = document.getElementById("toastContainer");
        if (!container) {
            return;
        }

        const toast = document.createElement("div");
        toast.className = `toast ${type || "info"}`;
        toast.textContent = message;
        container.appendChild(toast);

        window.setTimeout(function () {
            toast.style.opacity = "0";
            toast.style.transform = "translateX(16px)";
            window.setTimeout(function () {
                toast.remove();
            }, 220);
        }, timeout || 4200);
    }

    function setButtonLoading(button, loading, text) {
        if (!button) {
            return;
        }

        if (loading) {
            if (!button.dataset.originalText) {
                button.dataset.originalText = button.textContent || "";
            }
            button.disabled = true;
            button.textContent = text || "Отправка...";
        } else {
            button.disabled = false;
            button.textContent = button.dataset.originalText || button.textContent || "Готово";
        }
    }

    document.addEventListener("click", function (event) {
        if (!(event.target instanceof HTMLElement)) {
            return;
        }

        if (event.target.hasAttribute("data-close-modal")) {
            closeModal();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });

    window.UI = {
        escapeHtml: escapeHtml,
        openModal: openModal,
        closeModal: closeModal,
        showToast: showToast,
        setButtonLoading: setButtonLoading
    };
})();
