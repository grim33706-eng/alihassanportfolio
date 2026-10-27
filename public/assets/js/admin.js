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
       Image fields: preview a picked file; Remove / Undo
    ------------------------------------------------------------------ */
    const HELP = 'JPG, PNG, WebP, GIF or SVG · up to 4 MB';
    const imageState = (field) => {
        const saved = field.dataset.hasSaved === '1';
        const fileInput = field.querySelector('[data-image-input]');
        const picked = fileInput.files.length > 0;
        const removing = field.querySelector('[data-image-remove-input]').value === '1';
        const preview = field.querySelector('[data-image-preview]');
        const savedImg = preview.querySelector('[data-saved-image]');
        const pickedImg = preview.querySelector('[data-picked-image]');
        const removeBtn = field.querySelector('[data-image-remove]');

        field.classList.toggle('is-removing', removing);
        field.classList.toggle('has-image', (saved && !removing) || picked);
        if (savedImg) savedImg.hidden = picked;
        if (pickedImg && !picked) pickedImg.remove();

        removeBtn.hidden = !saved && !picked;
        field.querySelector('[data-image-remove-label]').textContent = picked
            ? 'Cancel new image'
            : (removing ? 'Undo remove' : 'Remove image');
        field.querySelector('[data-image-pick-label]').textContent = saved || picked ? 'Replace image' : 'Upload image';
        field.querySelector('[data-image-name]').textContent = picked
            ? `${fileInput.files[0].name} · will upload when you save`
            : (removing ? 'The image will be removed when you save.' : HELP);
    };

    document.addEventListener('change', (e) => {
        const input = e.target;
        if (!input.matches('[data-image-input]')) return;
        const field = input.closest('[data-image-field]');
        const preview = field.querySelector('[data-image-preview]');
        const old = preview.querySelector('[data-picked-image]');
        if (old) old.remove();
        if (input.files.length) {
            // Picking a new image replaces the saved one, so a pending removal is cancelled.
            field.querySelector('[data-image-remove-input]').value = '0';
            const file = input.files[0];
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = '';
                img.dataset.pickedImage = '';
                preview.prepend(img);
            }
        }
        imageState(field);
    });

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-image-remove]');
        if (!btn) return;
        const field = btn.closest('[data-image-field]');
        const fileInput = field.querySelector('[data-image-input]');
        const removeInput = field.querySelector('[data-image-remove-input]');
        if (fileInput.files.length) {
            fileInput.value = ''; // cancel the newly picked file
        } else {
            removeInput.value = removeInput.value === '1' ? '0' : '1';
        }
        imageState(field);
        markDirty();
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

    /* ------------------------------------------------------------------
       Section tabs (General, Hero, …): highlight the section in view
    ------------------------------------------------------------------ */
    const jump = document.querySelector('.adm-jump');
    if (jump) {
        const links = Array.from(jump.querySelectorAll('a[href^="#"]'));
        const cards = links.map((a) => document.querySelector(a.getAttribute('href'))).filter(Boolean);
        let locked = false; // while a clicked tab's smooth scroll runs, ignore the observer
        let lockTimer;

        const activate = (id) => {
            links.forEach((a) => {
                const on = a.getAttribute('href') === `#${id}`;
                a.classList.toggle('is-active', on);
                if (on) {
                    a.setAttribute('aria-current', 'true');
                    // Keep the active tab visible inside the scrollable bar.
                    const left = a.offsetLeft - (jump.clientWidth - a.offsetWidth) / 2;
                    jump.scrollTo({ left: Math.max(0, left), behavior: 'smooth' });
                } else {
                    a.removeAttribute('aria-current');
                }
            });
        };

        links.forEach((a) => {
            a.addEventListener('click', () => {
                activate(a.getAttribute('href').slice(1));
                locked = true;
                clearTimeout(lockTimer);
                lockTimer = setTimeout(() => { locked = false; }, 900);
            });
        });

        // Current section = the last one whose top has passed just below the sticky tab bar
        // (or the last section once the page is scrolled to the bottom).
        let current = null;
        let ticking = false;
        const update = () => {
            ticking = false;
            if (locked) return;
            const line = jump.getBoundingClientRect().bottom + 24;
            let id = cards[0].id;
            for (const card of cards) {
                if (card.getBoundingClientRect().top <= line) id = card.id;
                else break;
            }
            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) {
                id = cards[cards.length - 1].id;
            }
            if (id !== current) {
                current = id;
                activate(id);
            }
        };
        window.addEventListener('scroll', () => {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
        window.addEventListener('resize', update);

        if (location.hash && document.querySelector(location.hash)) {
            current = location.hash.slice(1);
            activate(current);
        } else {
            update();
        }
    }

    document.querySelectorAll('[data-repeater]').forEach(refreshRepeater);
    enhance(document);
})();
