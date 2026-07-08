@extends('layouts.app')

@section('title', 'Configuración de Cuenta | Arborea')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/configuracion.css') }}">
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Configuración de Cuenta</h1>
            <p class="section-subtitle">Gestiona tus datos personales y la seguridad de tu acceso a Arborea.</p>
        </section>

        <div class="profile-grid reveal delay-1">
            <!-- Columna Datos Personales -->
            <div class="profile-card">
                <div class="profile-card-header" style="flex-direction: column; align-items: center; text-align: center; border-bottom: none; padding-bottom: 10px;">
                    <div class="profile-avatar-wrapper" style="position: relative; cursor: pointer; width: 100px; height: 100px;">
                        <div class="profile-avatar-large" id="profile-avatar-preview" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: rgba(91, 191, 140, 0.15); border: 3px solid var(--living-moss); transition: all 0.3s ease;">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ str_starts_with(Auth::user()->profile_photo, '/img/user/') ? asset(Auth::user()->profile_photo) : Storage::url(Auth::user()->profile_photo) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            @endif
                        </div>
                        <!-- Overlay al pasar el mouse para editar -->
                        <div class="avatar-edit-overlay" onclick="document.getElementById('avatar-file-input').click()" style="position: absolute; top: 0; left: 0; width: 100px; height: 100px; border-radius: 50%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: opacity 0.3s ease;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h2 style="font-size: 1.5rem; margin-bottom: 5px;">Datos del Perfil</h2>
                        <p class="card-subtitle">Información actual de tu cuenta.</p>
                    </div>
                </div>

                <!-- Selección de Avatar -->
                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="avatar-selection-container" style="border-top: 1px solid rgba(45, 122, 79, 0.15); border-bottom: 1px solid rgba(45, 122, 79, 0.15); padding: 20px 0; display: flex; flex-direction: column; gap: 15px; align-items: center;">
                    @csrf
                    <span class="info-label" style="font-size: 0.8rem;">Elegir avatar o subir foto</span>
                    <div class="default-avatars" style="display: flex; gap: 15px; justify-content: center;">
                        <button type="submit" name="default_avatar" value="/img/user/avatar1.png" style="background: none; border: none; padding: 0;">
                            <img class="default-avatar-option" src="{{ asset('img/user/avatar1.png') }}" alt="Avatar 1" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                        </button>
                        <button type="submit" name="default_avatar" value="/img/user/avatar2.png" style="background: none; border: none; padding: 0;">
                            <img class="default-avatar-option" src="{{ asset('img/user/avatar2.png') }}" alt="Avatar 2" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                        </button>
                        <button type="submit" name="default_avatar" value="/img/user/avatar3.png" style="background: none; border: none; padding: 0;">
                            <img class="default-avatar-option" src="{{ asset('img/user/avatar3.png') }}" alt="Avatar 3" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                        </button>
                    </div>
                    <div style="display: flex; justify-content: center; gap: 10px; width: 100%; flex-wrap: wrap;">
                        <button type="button" class="btn-main-cta" onclick="document.getElementById('avatar-file-input').click()" style="background-color: transparent; border: 2px solid var(--living-moss); color: var(--living-moss); font-size: 0.85rem; padding: 6px 12px; margin: 0;">
                            Subir propia imagen
                        </button>
                        @if(Auth::user()->profile_photo)
                        <button type="submit" name="default_avatar" value="" class="btn-main-cta" style="background-color: transparent; border: 2px solid #d32f2f; color: #d32f2f; font-size: 0.85rem; padding: 6px 12px; margin: 0;">
                            Quitar foto
                        </button>
                        @endif
                    </div>
                    <input type="file" name="profile_photo" id="avatar-file-input" accept="image/*" style="display: none;" onchange="document.getElementById('avatar-form').submit()">
                </form>

                <div class="profile-info-list">
                    <div class="info-item">
                        <span class="info-label">Nombre Completo</span>
                        <span class="info-value">{{ $user ? $user->name . ' ' . $user->last_name : 'Vecino Juan' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Correo Electrónico</span>
                        <span class="info-value">{{ $user ? $user->email : 'vecino@example.com' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Rol del Sistema</span>
                        <span class="info-value role-badge {{ $user ? $user->role : 'vecino' }}">
                            {{ $user ? ucfirst($user->role) : 'Vecino' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Columna Seguridad / Cambiar Contraseña -->
            <div class="profile-card">
                <h2>Seguridad de la Cuenta</h2>
                <p class="card-subtitle">Cambia tu contraseña de acceso.</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.password.update') }}" method="POST" class="profile-form">
                    @csrf
                    <div class="form-group">
                        <label for="current_password">Contraseña Actual</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required placeholder="Mínimo 4 caracteres">
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required placeholder="Repite la nueva contraseña">
                    </div>

                    <button type="submit" class="btn-main-cta" style="width: 100%; border: none; margin-top: 15px;">
                        Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de campos de formulario
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
                if (input.value) {
                    input.parentElement.classList.add('focused');
                }
            });
        });
    </script>
@endsection
