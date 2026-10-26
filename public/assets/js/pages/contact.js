/**
 * Contact form: client-side validation mirroring the server rules,
 * then an AJAX submit. Falls back to a normal POST without JavaScript.
 */
(function () {
    'use strict';

    const form = document.querySelector('[data-contact-form]');
    if (!form) return;

    const status = form.querySelector('[data-form-status]');
    const submit = form.querySelector('[data-submit]');
    const submitLabel = submit.querySelector('.btn-ui__label');

    const rules = {
        name: (v) => (v.length < 2 ? 'Please tell me your name.' : ''),
        email: (v) => (!v ? 'I need an email address to reply to.' : !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? 'That email address doesn\'t look quite right.' : ''),
        subject: (v) => (v.length < 3 ? 'Please add a short subject.' : ''),
        message: (v) => (!v ? 'Please write a message.' : v.length < 20 ? 'Please share a little more detail (at least 20 characters).' : ''),
    };

    const setError = (name, message) => {
        const input = form.elements[name];
        const field = input.closest('.field');
        const error = form.querySelector(`[data-error-for="${name}"]`);
        error.textContent = message || '';
        field.classList.toggle('is-invalid', !!message);
        field.classList.toggle('is-valid', !message && input.value.trim() !== '');
        if (message) input.setAttribute('aria-invalid', 'true');
        else input.removeAttribute('aria-invalid');
    };

    const validate = (name) => {
        const message = rules[name](form.elements[name].value.trim());
        setError(name, message);
        return !message;
    };

    const showStatus = (ok, text) => {
        status.hidden = false;
        status.innerHTML = '';
        const box = document.createElement('div');
        box.className = `form-alert form-alert--${ok ? 'success' : 'error'}`;
        box.innerHTML = ok
            ? '<svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>'
            : '<svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3.5 2.5 20h19z"/><path d="M12 10v4m0 3v.01"/></svg>';
        const span = document.createElement('span');
        span.textContent = text;
        box.appendChild(span);
        status.appendChild(box);
        if (window.App && window.App.motionOK) gsap.from(box, { y: -10, opacity: 0, duration: 0.4 });
    };

    Object.keys(rules).forEach((name) => {
        const input = form.elements[name];
        input.addEventListener('blur', () => { if (input.value.trim()) validate(name); });
        input.addEventListener('input', () => { if (input.closest('.field').classList.contains('is-invalid')) validate(name); });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const results = Object.keys(rules).map(validate);
        if (results.includes(false)) {
            form.querySelector('[aria-invalid="true"]')?.focus();
            return;
        }

        submit.disabled = true;
        submitLabel.textContent = 'Sending…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok && data.ok) {
                showStatus(true, data.message);
                form.reset();
                form.querySelectorAll('.field').forEach((f) => f.classList.remove('is-valid', 'is-invalid'));
            } else if (response.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([name, messages]) => {
                    if (rules[name]) setError(name, messages[0]);
                    else showStatus(false, messages[0]);
                });
                form.querySelector('[aria-invalid="true"]')?.focus();
            } else if (response.status === 419) {
                showStatus(false, 'Your session expired. Please refresh the page and try again.');
            } else {
                showStatus(false, data.message || 'Sorry — something went wrong. Please try again.');
            }
        } catch (err) {
            showStatus(false, 'Network error — please check your connection and try again.');
        } finally {
            submit.disabled = false;
            submitLabel.textContent = 'Send message';
        }
    });
})();
