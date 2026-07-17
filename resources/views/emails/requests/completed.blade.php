<x-mail::message>
# ¡Buenas noticias!

Hola {{ $userRequest->user->name ?? 'vecino' }},

Tu solicitud de **{{ $userRequest->requestType->task_description ?? 'Servicio' }}** en la dirección **{{ $userRequest->street->street_name ?? 'tu cuadra' }} {{ $userRequest->street->street_number ?? '' }}** ha sido resuelta exitosamente por nuestro equipo.

Gracias por ayudarnos a mantener nuestra ciudad más verde.

<x-mail::button :url="url('/mis-reclamos')">
Ver mis solicitudes
</x-mail::button>

Saludos,<br>
{{ config('app.name', 'Arbórea') }}
</x-mail::message>
