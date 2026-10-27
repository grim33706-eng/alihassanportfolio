@extends('admin.layout')

@section('title', 'Messages')
@section('crumb', 'Manage')

@section('content')
<div class="adm-page-head">
    <span class="adm-page-head__icon"><x-icon name="mail" size="22" /></span>
    <div class="adm-page-head__text">
        <h1 class="adm-h1">Messages</h1>
        <p class="adm-muted">Everything sent through the contact form, newest first.</p>
    </div>
    <div class="adm-page-head__actions">
        <form method="POST" action="{{ route('admin.messages.read') }}">
            @csrf
            <button type="submit" class="adm-btn adm-btn--ghost"><x-icon name="check" size="15" /> Mark all as read</button>
        </form>
    </div>
</div>

<section class="adm-card">
    <div class="adm-card__body adm-card__body--flush">
        @forelse ($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}" @class(['adm-msg-row', 'adm-msg-row--lg', 'is-unread' => ! $message->read_at])>
                <span class="adm-avatar">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                <span class="adm-msg-row__text">
                    <strong>{{ $message->name }} <small>{{ $message->email }}</small></strong>
                    <span>{{ $message->subject }} — <em>{{ \Illuminate\Support\Str::limit($message->message, 110) }}</em></span>
                </span>
                <span class="adm-msg-row__time">{{ $message->created_at->format('j M, H:i') }}</span>
            </a>
        @empty
            <div class="adm-empty">
                <x-icon name="mail" size="28" />
                <p>No messages yet. Contact-form submissions will appear here.</p>
            </div>
        @endforelse
    </div>
</section>

@if ($messages->hasPages())
    <div class="adm-pager">
        @if ($messages->previousPageUrl())<a class="adm-btn adm-btn--ghost adm-btn--sm" href="{{ $messages->previousPageUrl() }}">Newer</a>@endif
        <span class="adm-muted">Page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}</span>
        @if ($messages->nextPageUrl())<a class="adm-btn adm-btn--ghost adm-btn--sm" href="{{ $messages->nextPageUrl() }}">Older</a>@endif
    </div>
@endif
@endsection
