@props([
    'type' => 'success', // success, error, warning, info
    'title' => '',
    'message' => '',
    'image' => '',
    'show' => false, // Para mostrarlo por defecto si viene de un redirect
    'hideActions' => false, // Para ocultar los botones por defecto y usar el slot
    'closeRoute' => '' // Ruta para redirigir al cerrar
])

<div class="alert-modal-overlay {{ $show ? 'active' : '' }}" id="alert-modal-{{ $type }}">
    <div class="alert-modal-box alert-modal-{{ $type }}">
        @if($closeRoute)
            <a href="{{ $closeRoute }}" class="alert-modal-close" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">&times;</a>
        @else
            <button type="button" class="alert-modal-close" onclick="closeAlertModal('alert-modal-{{ $type }}')">&times;</button>
        @endif
        
        @if($image)
            <div class="alert-modal-image">
                <img src="{{ $image }}" alt="{{ $type }}">
            </div>
        @endif

        <h3 class="alert-modal-title">{{ $title }}</h3>
        @if($message)
            <p class="alert-modal-message">{{ $message }}</p>
        @endif
        
        {{ $slot ?? '' }}

        @if(!$hideActions)
            <div class="alert-modal-actions">
                @if($type === 'success')
                    <button type="button" class="btn-main-cta alert-modal-btn" onclick="closeAlertModal('alert-modal-{{ $type }}')">Entendido</button>
                @elseif($type === 'error')
                    <button type="button" class="btn-secondary alert-modal-btn" onclick="closeAlertModal('alert-modal-{{ $type }}')">Volver</button>
                @else
                    <button type="button" class="btn-main-cta alert-modal-btn" onclick="closeAlertModal('alert-modal-{{ $type }}')">Cerrar</button>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    if (typeof window.closeAlertModal !== 'function') {
        window.closeAlertModal = function(modalId) {
            document.getElementById(modalId).classList.remove('active');
        };
        
        window.openAlertModal = function(modalId) {
            document.getElementById(modalId).classList.add('active');
        };
    }
</script>
