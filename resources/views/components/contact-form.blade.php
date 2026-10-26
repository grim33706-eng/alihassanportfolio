<form method="POST" action="{{ route('contact.store') }}" class="contact-form" data-contact-form novalidate>
    @csrf
    <input type="hidden" name="form_started_at" value="{{ time() }}">

    {{-- Honeypot: hidden from people and screen readers, tempting to bots. --}}
    <div class="hp-field" aria-hidden="true">
        <label for="company_website">Company website</label>
        <input type="text" id="company_website" name="company_website" tabindex="-1" autocomplete="off">
    </div>

    <div class="form-status" data-form-status role="status" aria-live="polite" @if (! session('contact_success') && ! session('contact_error')) hidden @endif>
        @if (session('contact_success'))
            <div class="form-alert form-alert--success"><x-icon name="check" size="20" /> <span>{{ session('contact_success') }}</span></div>
        @elseif (session('contact_error'))
            <div class="form-alert form-alert--error"><x-icon name="alert" size="20" /> <span>{{ session('contact_error') }}</span></div>
        @endif
    </div>

    <div class="row g-3">
        @foreach ([
            ['name', 'Your name', 'text', 'name', 'col-md-6'],
            ['email', 'Email address', 'email', 'email', 'col-md-6'],
            ['subject', 'Subject', 'text', 'off', 'col-12'],
        ] as [$field, $label, $type, $autocomplete, $col])
            <div class="{{ $col }}">
                <div class="field @error($field) is-invalid @enderror">
                    <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" class="field__input" placeholder=" "
                        value="{{ old($field) }}" autocomplete="{{ $autocomplete }}" required
                        maxlength="{{ $field === 'email' ? 150 : ($field === 'name' ? 100 : 150) }}"
                        aria-describedby="{{ $field }}-error" @error($field) aria-invalid="true" @enderror>
                    <label for="{{ $field }}" class="field__label">{{ $label }}</label>
                    <p class="field__error" id="{{ $field }}-error" data-error-for="{{ $field }}">@error($field){{ $message }}@enderror</p>
                </div>
            </div>
        @endforeach

        <div class="col-12">
            <div class="field field--textarea @error('message') is-invalid @enderror">
                <textarea id="message" name="message" class="field__input" rows="6" placeholder=" " required maxlength="5000"
                    aria-describedby="message-error" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                <label for="message" class="field__label">Tell me about your project or role</label>
                <p class="field__error" id="message-error" data-error-for="message">@error('message'){{ $message }}@enderror</p>
            </div>
        </div>
    </div>

    <div class="contact-form__foot">
        <p class="contact-form__note">Your details are only used to reply to your message.</p>
        <x-button type="submit" icon="send" data-submit>Send message</x-button>
    </div>
</form>
