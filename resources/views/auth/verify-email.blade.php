@extends('layouts.app')

@section('title', 'Verifica tu correo | TreeBA')

@section('content')
    <main class="page-container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; position: relative;">
        
        <!-- Contenido principal vacío o con algún texto de fondo, ya que el modal ocupa la pantalla -->
        <div style="text-align: center; color: var(--forest-night); opacity: 0.5;">
            <h2>Verificación de Correo Requerida</h2>
            <p>Por favor revisa el modal en pantalla para continuar.</p>
        </div>

        <!-- El modal se muestra por defecto al cargar esta página (show="true") -->
        <x-layouts.alert-modal type="success" title="¡Ya casi estamos!" show="true" hideActions="true">
            
            <p class="alert-modal-message" style="margin-bottom: 20px;">
                Antes de continuar utilizando el sistema de arbolado, necesitamos que verifiques tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar.
            </p>

            @if (session('message'))
                <div style="color: var(--living-moss); background-color: rgba(45, 122, 79, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; text-align: center;">
                    {{ session('message') }}
                </div>
            @endif

            <hr style="border: 0; border-top: 1px solid rgba(45, 122, 79, 0.15); margin: 25px 0 20px 0;">

            <form method="POST" action="{{ route('verification.send') }}" style="text-align: center;">
                @csrf
                <p style="margin-bottom: 12px; color: var(--forest-night); font-size: 0.95rem;">¿No recibiste el correo electrónico?</p>
                <button type="submit" class="btn-main-cta" style="width: 100%;">
                    Reenviar correo de verificación
                </button>
            </form>

        </x-alert-modal>
    </main>
@endsection
