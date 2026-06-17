@extends('layouts.app')

@section('title', 'Configuración de Cuenta | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <main class="profile-page-container">
        <section class="profile-header reveal">
            <h1 class="hero-title">Configuración de Cuenta</h1>
            <p class="section-subtitle">Gestiona tus datos personales y la seguridad de tu acceso a TreeBA.</p>
        </section>

        <div class="profile-grid reveal delay-1">
            <!-- Columna Datos Personales -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-avatar-large">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h2>Datos del Perfil</h2>
                        <p class="card-subtitle">Información actual de tu cuenta.</p>
                    </div>
                </div>

                <div class="profile-info-list">
                    <div class="info-item">
                        <span class="info-label">Nombre Completo</span>
                        <span class="info-value">{{ $user ? $user->name : 'Vecino Juan' }}</span>
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
