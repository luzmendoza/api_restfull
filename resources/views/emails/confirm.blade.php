
@component('mail::message')
# Hola {{ $user->name }}

Haz cambiado tu correo electronico, ve al siguiente boton para finalizar con el cambio

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Confirmar correo
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
