@extends('admin.layout')

@section('title', $message->subject)
@section('crumb', 'Messages')

@section('content')
<a href="{{ route('admin.messages.index') }}" class="adm-back"><x-icon name="chevron-left" size="16" /> All messages</a>

<section class="adm-card adm-message">
    <header class="adm-message__head">
        <span class="adm-avatar adm-avatar--lg">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
        <div>
            <h1 class="adm-h1">{{ $message->subject }}</h1>
            <p class="adm-muted">
                From <strong>{{ $message->name }}</strong> · <a href="mailto:{{ $message->email }}" class="adm-link">{{ $message->email }}</a>
                · {{ $message->created_at->format('j F Y, H:i') }}
            </p>
        </div>
    </header>
    <div class="adm-message__body">{!! nl2br(e($message->message)) !!}</div>
    <footer class="adm-message__foot">
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="adm-btn adm-btn--primary"><x-icon name="mail" size="16" /> Reply by email</a>
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="adm-btn adm-btn--danger" data-confirm="Delete this message? This cannot be undone."><x-icon name="trash" size="16" /> Delete</button>
        </form>
    </footer>
</section>
@endsection
