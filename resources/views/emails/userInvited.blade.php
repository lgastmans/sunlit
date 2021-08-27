@component('mail::message')
# Hello {{ $name }},

You've been invited to join {{ config('app.name') }}.

To complete your registration, follow this <a href="{{ $url }}">link</a> (or paste into your browser) within the next 24 hours.

@component('mail::button', ['url' => $url ] )
Complete your registration
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
