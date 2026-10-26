New message from the portfolio contact form

Name:    {{ $contactMessage->name }}
Email:   {{ $contactMessage->email }}
Subject: {{ $contactMessage->subject }}
Sent:    {{ $contactMessage->created_at->toDayDateTimeString() }}

{{ $contactMessage->message }}
