<x-mail::message>
# Recordatorio: Tu árbol necesita agua

Hola {{ $userRequest->user->name ?? 'vecino' }},

Hace **{{ $months }} meses** plantamos un árbol en **{{ $userRequest->street->street_name ?? 'tu cuadra' }} {{ $userRequest->street->street_number ?? '' }}**.

Te recordamos la importancia de regarlo con al menos **20 litros de agua por semana** (especialmente en época de calor) para asegurar su crecimiento y supervivencia.

¡Gracias por tu compromiso con el arbolado urbano!

<x-mail::button :url="url('/cuidados')">
Ver guía de cuidados
</x-mail::button>

Saludos,<br>
{{ config('app.name', 'Arbórea') }}
</x-mail::message>
