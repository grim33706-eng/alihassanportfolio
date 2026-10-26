<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ContactRequest extends FormRequest
{
    /**
     * Forms submitted faster than this are treated as bots.
     */
    private const MIN_SECONDS_TO_SUBMIT = 3;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Trim and strip tags before validation so stored data is plain text.
     */
    protected function prepareForValidation(): void
    {
        $clean = fn ($value) => is_string($value) ? trim(strip_tags($value)) : $value;

        $this->merge([
            'name' => $clean($this->input('name')),
            'email' => is_string($this->input('email')) ? strtolower(trim($this->input('email'))) : null,
            'subject' => $clean($this->input('subject')),
            'message' => $clean($this->input('message')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:150'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            // Honeypot: real visitors never see or fill this field.
            'company_website' => ['nullable', 'max:0'],
            'form_started_at' => ['required', 'integer'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $startedAt = (int) $this->input('form_started_at');

                if ($startedAt > 0 && (time() - $startedAt) < self::MIN_SECONDS_TO_SUBMIT) {
                    $validator->errors()->add('message', 'That was quick! Please take a moment and send your message again.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please tell me your name.',
            'name.min' => 'Please tell me your name.',
            'subject.min' => 'Please add a short subject.',
            'email.required' => 'I need an email address to reply to.',
            'email.email' => 'That email address doesn\'t look quite right.',
            'subject.required' => 'Please add a short subject.',
            'message.required' => 'Please write a message.',
            'message.min' => 'Please share a little more detail (at least 20 characters).',
            'company_website.max' => 'Your message could not be sent.',
            'form_started_at.required' => 'Your message could not be sent. Please refresh the page and try again.',
        ];
    }
}
