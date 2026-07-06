@props([
    'count' => null, // Si es nulo, se muestra como un punto rojo sin número
    'isDot' => false, // Para forzar que sea un punto (dot)
    'position' => 'static', // static, absolute
    'top' => '-2px',
    'right' => '-2px'
])

@php
    $baseStyles = "background-color: #ef4444; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; aspect-ratio: 1/1;";
    
    if ($isDot || $count === null) {
        // Estilo para el punto flotante (dot)
        $sizeStyles = "width: 14px; height: 14px; min-width: 14px; min-height: 14px; border: 2px solid var(--forest-night);";
    } else {
        // Estilo para la burbuja con número
        $sizeStyles = "width: 22px; height: 22px; min-width: 22px; min-height: 22px; font-size: 0.75rem;";
    }

    $positionStyles = "";
    if ($position === 'absolute') {
        $positionStyles = "position: absolute; top: {$top}; right: {$right}; z-index: 10;";
    }
@endphp

<span {{ $attributes->merge(['class' => 'notification-badge', 'style' => $baseStyles . ' ' . $sizeStyles . ' ' . $positionStyles]) }}>
    @if(!$isDot && $count !== null)
        {{ $count > 99 ? '99+' : $count }}
    @endif
</span>
