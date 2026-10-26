@extends('admin.layout')

@section('title', 'Log in')

@section('content')
<div class="admin-login">
    <div class="panel">
        <h1 class="panel__title">Admin log in</h1>
        <p class="admin-muted">Manage the before-and-after case studies shown on the portfolio.</p>
        <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-4">
            @csrf
            <div class="field @error('password') is-invalid @enderror">
                <input type="password" id="password" name="password" class="field__input" placeholder=" " required autofocus autocomplete="current-password">
                <label for="password" class="field__label">Password</label>
            </div>
            <x-button type="submit" icon="arrow-right" class="w-100 mt-3">Log in</x-button>
        </form>
    </div>
</div>
@endsection
