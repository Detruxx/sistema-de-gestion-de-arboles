<x-mail::message>
# ¡Tu nuevo árbol ya está plantado!

Hola {{ $userRequest->user->name ?? 'vecino' }},

Hemos completado exitosamente la plantación en **{{ $userRequest->street->street_name ?? 'tu cuadra' }} {{ $userRequest->street->street_number ?? '' }}**.

Desde hoy, comienza un trabajo en equipo para cuidarlo. Recuerda que los primeros años son fundamentales para que crezca fuerte y sano.

<x-mail::button :url="url('/cuidados')">
Ver guía de cuidados
</x-mail::button>

Saludos,<br>
{{ config('app.name', 'Arbórea') }}
</x-mail::message>
