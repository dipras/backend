@component('mail::message')
# Hi {{ $user->name }}

Silahkan klik link dibawah ini untuk melakukan konfirmasi email anda.

@component('mail::button', ['url' => $url ])
Verifikasi Alamat Email
@endcomponent

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent
