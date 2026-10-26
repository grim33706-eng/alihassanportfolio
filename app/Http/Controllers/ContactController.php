<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact', [
            'person' => config('portfolio.person'),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $message = ContactMessage::create([
                ...$request->safe()->only(['name', 'email', 'subject', 'message']),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]);
        } catch (Throwable $e) {
            Log::error('Contact message could not be saved.', ['error' => $e->getMessage()]);

            return $this->respond($request, false, 'Sorry — your message could not be sent right now. Please try again shortly or reach out on LinkedIn.', 500);
        }

        // A failed notification should not lose the message: it is already stored.
        if ($to = config('portfolio.person.notify_email')) {
            try {
                Mail::to($to)->send(new NewContactMessage($message));
            } catch (Throwable $e) {
                Log::warning('Contact notification email failed.', ['id' => $message->id, 'error' => $e->getMessage()]);
            }
        }

        return $this->respond($request, true, 'Thank you, '.Str::before($message->name, ' ').'! Your message has been sent — I\'ll get back to you soon.');
    }

    private function respond(ContactRequest $request, bool $ok, string $text, int $status = 200): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok, 'message' => $text], $status);
        }

        return $ok
            ? redirect()->route('contact')->with('contact_success', $text)
            : back()->withInput()->with('contact_error', $text);
    }
}
