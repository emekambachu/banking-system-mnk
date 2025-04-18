@component('mail::message')
    # Two‑Factor Authentication Code

    Your login code is:

    @component('mail::panel')
        **{{ $secret }}**
    @endcomponent

    This code will expire in **10 minutes**.

    If you did not request this, please ignore this email.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
