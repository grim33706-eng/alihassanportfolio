<?php

use Illuminate\Support\HtmlString;

/*
| Template helpers for editable content.
*/

if (! function_exists('content_tokens')) {
    /**
     * Replace {name}, {first_name}, {role} and {location} with the profile values.
     */
    function content_tokens(?string $text): string
    {
        $person = config('portfolio.person', []);

        return strtr((string) $text, [
            '{name}' => $person['name'] ?? '',
            '{first_name}' => $person['first_name'] ?? '',
            '{role}' => $person['role'] ?? '',
            '{location}' => $person['location'] ?? '',
        ]);
    }
}

if (! function_exists('txt')) {
    /**
     * Plain text with profile placeholders filled in (escape it with {{ }}).
     */
    function txt(?string $text): string
    {
        return content_tokens($text);
    }
}

if (! function_exists('rich')) {
    /**
     * Safe formatted text: everything is escaped, then *words* become the gradient
     * highlight and line breaks become $break. Print with {!! !!} or pass to components.
     */
    function rich(?string $text, string $break = '<br>'): HtmlString
    {
        $html = e(content_tokens($text));
        $html = preg_replace('/\*(.+?)\*/s', '<span class="text-gradient">$1</span>', $html);
        $html = preg_replace('/\r?\n/', $break, $html);

        return new HtmlString($html);
    }
}

if (! function_exists('media_url')) {
    /**
     * Public URL of an uploaded file (stored on the "public" disk), or null.
     */
    function media_url(?string $path): ?string
    {
        return $path ? asset('storage/'.ltrim($path, '/')) : null;
    }
}
