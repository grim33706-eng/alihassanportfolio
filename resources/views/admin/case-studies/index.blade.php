@extends('admin.layout')

@section('title', 'Case studies')

@section('content')
<div class="admin-head">
    <div>
        <h1 class="admin-title">Before &amp; after case studies</h1>
        <p class="admin-muted">Add a case study, upload its before and after screenshots, and untick “Demonstration” once it shows real campaign data.</p>
    </div>
    <x-button :href="route('admin.case-studies.create')" icon="arrow-right">New case study</x-button>
</div>

<div class="table-wrap">
    <table class="data-table admin-table">
        <thead>
            <tr>
                <th scope="col">Case study</th>
                <th scope="col">Screenshots</th>
                <th scope="col">Status</th>
                <th scope="col">Order</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($caseStudies as $cs)
                <tr>
                    <th scope="row">
                        <a href="{{ route('admin.case-studies.edit', $cs) }}" class="admin-link">{{ $cs->title }}</a>
                        <small class="d-block admin-muted">{{ $cs->industry }} · {{ $cs->country }}</small>
                    </th>
                    <td>
                        <span @class(['admin-count', 'is-empty' => ! $cs->before_count])>{{ $cs->before_count }} before</span>
                        <span @class(['admin-count', 'is-empty' => ! $cs->after_count])>{{ $cs->after_count }} after</span>
                    </td>
                    <td>
                        @if ($cs->is_demo)<span class="demo-badge">Demonstration</span>@else<span class="verified-badge">Real data</span>@endif
                        @if (! $cs->is_published)<span class="admin-pill">Hidden</span>@endif
                        @if ($cs->is_featured)<span class="admin-pill admin-pill--accent">Featured</span>@endif
                    </td>
                    <td>{{ $cs->sort_order }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.case-studies.edit', $cs) }}" class="btn-ui btn-ui--ghost btn-ui--sm"><span class="btn-ui__label">Edit</span><x-icon name="edit" size="16" /></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="admin-muted">No case studies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
