/**
 * Admin panel behaviour: sidebar, repeaters, live previews, image picks,
 * unsaved-change tracking and confirmations. No dependencies.
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------
       Sidebar (mobile)
    ------------------------------------------------------------------ */
    const sidebar = document.querySelector('[data-adm-sidebar]');
    const menuBtn = document.querySelector('[data-adm-menu]');
    const scrim = document.querySelector('[data-adm-scrim]');
    const setMenu = (open) => {
        if (!sidebar) return;
        sidebar.classList.toggle('is-open', open);
        scrim.hidden = !open;
        menuBtn.setAttribute('aria-expanded', String(open));
    };
    if (menuBtn) {
        menuBtn.addEventListener('click', () => setMenu(!sidebar.classList.contains('is-open')));
        scrim.addEventListener('click', () => setMenu(false));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
    }

    /* ------------------------------------------------------------------
       Rich text preview: *word* → highlighted, newlines → breaks
    ------------------------------------------------------------------ */
    const escapeHtml = (s) => s.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]));
    const renderRich = (input) => {
        const preview = input.parentElement.querySelector('[data-rich-preview]');
        if (!preview) return;
        const value = input.value;
        // Only show a preview when there is something to preview.
        if (!/\*.+?\*/.test(value) && !/\n/.test(value)) { preview.innerHTML = ''; return; }
        preview.innerHTML = escapeHtml(value)
            .replace(/\*(.+?)\*/gs, '<span class="text-gradient">$1</span>')
            .replace(/\n/g, '<br>');
    };

    /* ------------------------------------------------------------------
       Auto-growing textareas
    ------------------------------------------------------------------ */
    const grow = (el) => {
        el.style.height = 'auto';
        el.style.height = `${el.scrollHeight + 2}px`;
    };

    function enhance(scope) {
        scope.querySelectorAll('[data-rich]').forEach(renderRich);
        scope.querySelectorAll('textarea[data-autogrow]').forEach(grow);
    }

    document.addEventListener('input', (e) => {
        const t = e.target;
        if (t.matches('[data-rich]')) renderRich(t);
        if (t.matches('textarea[data-autogrow]')) grow(t);
        // Keep repeater item titles in step with their label field.
        const item = t.closest('[data-repeater-item]');
        if (item) {
            const title = item.querySelector(':scope > .adm-item__head [data-item-title]');
            const source = title && title.dataset.source;
            if (source && t.name && t.name.endsWith(`[${source}]`) && t.closest('[data-repeater-item]') === item) {
                title.textContent = t.value.trim() || 'New item';
            }
        }
    });

    /* ------------------------------------------------------------------
       Image fields: show the picked file before upload
    ------------------------------------------------------------------ */
    document.addEventListener('change', (e) => {
        const input = e.target;
        if (!input.matches('[data-image-input]') || !input.files.length) return;
        const field = input.closest('[data-image-field]');
        const file = input.files[0];
        const preview = field.querySelector('[data-image-preview]');
        const name = field.querySelector('[data-image-name]');
        if (file.type.startsWith('image/')) {
            const url = URL.createObjectURL(file);
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = url;
            img.alt = '';
            preview.appendChild(img);
        }
        if (name) name.textContent = `${file.name} · will upload when you save`;
    });

    /* ------------------------------------------------------------------
       Repeaters: add / remove / reorder / collapse
    ------------------------------------------------------------------ */
    const refreshRepeater = (repeater) => {
        const items = repeater.querySelector(':scope > [data-repeater-items]');
        const count = items.children.length;
        const field = repeater.closest('.adm-field');
        const counter = field && field.querySelector(':scope > .adm-label-row [data-repeater-count]');
        if (counter) counter.textContent = count;
        const add = repeater.querySelector(':scope > [data-repeater-add]');
        const max = parseInt(repeater.dataset.max || '0', 10);
        if (add) add.disabled = max > 0 && count >= max;
    };

    let uid = 0;
    document.addEventListener('click', (e) => {
        const add = e.target.closest('[data-repeater-add]');
        if (add) {
            const repeater = add.closest('[data-repeater]');
            const template = repeater.querySelector(':scope > [data-repeater-template]');
            const placeholder = template.dataset.placeholder;
            const key = `n${Date.now()}${uid++}`;
            const html = template.innerHTML.split(placeholder).join(key);
            const wrap = document.createElement('div');
            wrap.innerHTML = html.trim();
            const item = wrap.firstElementChild;
            item.removeAttribute('data-collapsed');
            item.classList.add('is-new');
            repeater.querySelector(':scope > [data-repeater-items]').appendChild(item);
            refreshRepeater(repeater);
            enhance(item);
            const first = item.querySelector('input:not([type=hidden]), textarea, select');
            if (first) first.focus();
            markDirty();
            return;
        }

        const item = e.target.closest('[data-repeater-item]');
        if (!item) return;

        if (e.target.closest('[data-item-remove]')) {
            const repeater = item.closest('[data-repeater]');
            const title = item.querySelector('[data-item-title]').textContent.trim();
            if (!confirm(`Remove "${title}"? It will be deleted when you save.`)) return;
            item.remove();
            refreshRepeater(repeater);
            markDirty();
        } else if (e.target.closest('[data-item-up]')) {
            const prev = item.previousElementSibling;
            if (prev) { item.parentNode.insertBefore(item, prev); markDirty(); }
        } else if (e.target.closest('[data-item-down]')) {
            const next = item.nextElementSibling;
            if (next) { item.parentNode.insertBefore(next, item); markDirty(); }
        } else if (e.target.closest('[data-item-toggle]')) {
            const collapsed = item.hasAttribute('data-collapsed');
            item.toggleAttribute('data-collapsed', !collapsed);
            item.querySelectorAll(':scope > .adm-item__head [data-item-toggle]').forEach((b) => b.setAttribute('aria-expanded', String(collapsed)));
            if (collapsed) enhance(item);
        }
    });

    /* ------------------------------------------------------------------
       Unsaved changes
    ------------------------------------------------------------------ */
    let dirty = false;
    const savebar = document.querySelector('[data-savebar]');
    const state = document.querySelector('[data-save-state]');
    function markDirty() {
        if (dirty || !savebar) return;
        dirty = true;
        savebar.classList.add('is-dirty');
        state.innerHTML = '<span class="adm-dot adm-dot--dirty"></span> You have unsaved changes';
    }
    document.querySelectorAll('[data-adm-form]').forEach((form) => {
        form.addEventListener('input', markDirty);
        form.addEventListener('change', markDirty);
        form.addEventListener('submit', () => {
            dirty = false;
            const btn = form.querySelector('.adm-savebar .adm-btn--primary');
            if (btn) { btn.disabled = true; btn.lastChild.textContent = ' Saving…'; }
        });
    });
    window.addEventListener('beforeunload', (e) => {
        if (!dirty) return;
        e.preventDefault();
        e.returnValue = '';
    });

    /* ------------------------------------------------------------------
       Confirmations (reset, delete)
    ------------------------------------------------------------------ */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-confirm]');
        if (btn && !confirm(btn.dataset.confirm)) {
            e.preventDefault();
            return;
        }
        if (btn) dirty = false;
    });

    // Success notices fade after a few seconds.
    document.querySelectorAll('[data-flash]').forEach((flash) => {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.6s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 650);
        }, 5000);
    });

    document.querySelectorAll('[data-repeater]').forEach(refreshRepeater);
    enhance(document);
})();
