@extends('admin.layout')

@section('title', 'Dashboard')
@section('crumb', 'Overview')

@section('content')
@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $pages = collect($sections)->where('group', 'Pages');
    $content = collect($sections)->where('group', 'Content');
@endphp

<section class="adm-hero">
    <div>
        <p class="adm-eyebrow">{{ now()->format('l, j F Y') }}</p>
        <h1 class="adm-hero__title">{{ $greeting }}, {{ config('portfolio.person.first_name') ?: config('portfolio.person.name') }}.</h1>
        <p class="adm-muted">Edit any page below. Saved changes go live on the website straight away.</p>
    </div>
    <div class="adm-hero__actions">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="adm-btn adm-btn--primary">Open the website <x-icon name="arrow-up-right" size="16" /></a>
        <a href="{{ route('admin.settings') }}" class="adm-btn adm-btn--ghost">Settings</a>
    </div>
</section>

<div class="adm-stats">
    <a href="{{ route('admin.messages.index') }}" class="adm-stat">
        <span class="adm-stat__icon"><x-icon name="mail" size="20" /></span>
        <span class="adm-stat__value">{{ $stats['messages'] }}</span>
        <span class="adm-stat__label">Messages</span>
        <span @class(['adm-stat__note', 'is-accent' => $stats['unread']])>{{ $stats['unread'] ? $stats['unread'].' unread' : 'All read' }}</span>
    </a>
    <a href="{{ route('admin.case-studies.index') }}" class="adm-stat">
        <span class="adm-stat__icon"><x-icon name="columns" size="20" /></span>
        <span class="adm-stat__value">{{ $stats['caseStudies'] }}</span>
        <span class="adm-stat__label">Case studies</span>
        <span class="adm-stat__note">{{ $stats['caseStudiesDemo'] ? $stats['caseStudiesDemo'].' demonstration' : 'All real data' }}</span>
    </a>
    <a href="{{ route('admin.content.edit', 'projects') }}" class="adm-stat">
        <span class="adm-stat__icon"><x-icon name="target" size="20" /></span>
        <span class="adm-stat__value">{{ $stats['projects'] }}</span>
        <span class="adm-stat__label">Projects</span>
        <span class="adm-stat__note">{{ $stats['markets'] }} markets</span>
    </a>
    <div class="adm-stat">
        <span class="adm-stat__icon"><x-icon name="layers" size="20" /></span>
        <span class="adm-stat__value">{{ $stats['edited'] }}</span>
        <span class="adm-stat__label">Edited sections</span>
        <span class="adm-stat__note">{{ $lastEdit ? 'Last change '.$lastEdit->diffForHumans() : 'Showing original content' }}</span>
    </div>
</div>

<div class="adm-dash-grid">
    <section class="adm-card">
        <header class="adm-card__head adm-card__head--row">
            <h2 class="adm-card__title">Pages</h2>
            <span class="adm-help">Headings, buttons and text on each page</span>
        </header>
        <div class="adm-card__body">
            <div class="adm-tiles">
                @foreach ($pages as $id => $section)
                    <a href="{{ route('admin.content.edit', $id) }}" class="adm-tile">
                        <span class="adm-tile__icon"><x-icon :name="$section['icon']" size="20" /></span>
                        <span class="adm-tile__label">{{ $section['label'] }}</span>
                        <x-icon name="arrow-right" size="15" class="adm-tile__arrow" />
                    </a>
                @endforeach
            </div>
        </div>

        <header class="adm-card__head adm-card__head--row adm-card__head--sub">
            <h2 class="adm-card__title">Content</h2>
            <span class="adm-help">Lists used across several pages</span>
        </header>
        <div class="adm-card__body">
            <div class="adm-tiles">
                @foreach ($content as $id => $section)
                    <a href="{{ route('admin.content.edit', $id) }}" class="adm-tile">
                        <span class="adm-tile__icon"><x-icon :name="$section['icon']" size="20" /></span>
                        <span class="adm-tile__label">{{ $section['label'] }}</span>
                        <x-icon name="arrow-right" size="15" class="adm-tile__arrow" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="adm-card">
        <header class="adm-card__head adm-card__head--row">
            <h2 class="adm-card__title">Recent messages</h2>
            <a href="{{ route('admin.messages.index') }}" class="adm-link">View all</a>
        </header>
        <div class="adm-card__body">
            @forelse ($recentMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" @class(['adm-msg-row', 'is-unread' => ! $message->read_at])>
                    <span class="adm-avatar">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                    <span class="adm-msg-row__text">
                        <strong>{{ $message->name }}</strong>
                        <span>{{ $message->subject }}</span>
                    </span>
                    <span class="adm-msg-row__time">{{ $message->created_at->diffForHumans(short: true) }}</span>
                </a>
            @empty
                <div class="adm-empty">
                    <x-icon name="mail" size="26" />
                    <p>No messages yet. Contact-form submissions will appear here.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
