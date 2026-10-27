@extends('admin.layout')

@section('title', 'Settings')
@section('crumb', 'Admin')

@section('content')
<div class="adm-page-head">
    <span class="adm-page-head__icon"><x-icon name="compass" size="22" /></span>
    <div class="adm-page-head__text">
        <h1 class="adm-h1">Settings</h1>
        <p class="adm-muted">Your name, photo and contact details, used across the whole site, plus the admin password.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="adm-form" data-adm-form id="settings-form">
    @csrf
    @method('PUT')

    @foreach ($parts as $i => $part)
        <section class="adm-card">
            <header class="adm-card__head">
                <h2 class="adm-card__title">{{ $part['label'] }}</h2>
            </header>
            <div class="adm-card__body">
                <div class="adm-fields">
                    @foreach ($part['fields'] as $field)
                        @include('admin.fields.field', ['field' => $field, 'value' => $values[$i][$field['name']] ?? null, 'name' => 'parts['.$i.']['.$field['name'].']', 'file' => 'files['.$i.']['.$field['name'].']'])
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <div class="adm-savebar" data-savebar>
        <p class="adm-savebar__state" data-save-state><span class="adm-dot"></span> All changes saved</p>
        <div class="adm-savebar__actions">
            <button type="submit" class="adm-btn adm-btn--primary"><x-icon name="check" size="16" /> Save settings</button>
        </div>
    </div>
</form>

<section class="adm-card" id="password">
    <header class="adm-card__head">
        <h2 class="adm-card__title">Admin password</h2>
        <p class="adm-help">After changing it here, the password in your server settings (ADMIN_PASSWORD) is no longer used.</p>
    </header>
    <div class="adm-card__body">
        <form method="POST" action="{{ route('admin.settings.password') }}" class="adm-fields adm-fields--3">
            @csrf
            @method('PUT')
            <div class="adm-field">
                <label class="adm-label" for="current_password">Current password</label>
                <input class="adm-input" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="adm-field">
                <label class="adm-label" for="new_password">New password</label>
                <input class="adm-input" type="password" id="new_password" name="password" autocomplete="new-password" minlength="10" required>
                <p class="adm-help">At least 10 characters.</p>
            </div>
            <div class="adm-field">
                <label class="adm-label" for="new_password_confirmation">Repeat new password</label>
                <input class="adm-input" type="password" id="new_password_confirmation" name="password_confirmation" autocomplete="new-password" required>
            </div>
            <div class="adm-field adm-field--wide">
                <button type="submit" class="adm-btn adm-btn--ghost"><x-icon name="check" size="16" /> Change password</button>
            </div>
        </form>
    </div>
</section>
@endsection
