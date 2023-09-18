@component('mail::message')
# OTP Code

Your OTP code is: **{{ $otp }}**

This OTP code will expire in a short time.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
