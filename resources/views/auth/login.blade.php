@extends('layouts.app')

@section('title', 'Iniciar Sesión | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')

@section('content')
    <main class="tramites-page-container login-page-container">
        <section class="login-wrapper reveal">


            <div class="login-card">
                <div class="login-header">
                    <h2 class="login-title">Ingresar</h2>
                    <p id="role-subtitle">Inicia sesión en la plataforma TreeBA</p>
                </div>

                <!-- Selector de Rol (Vecino vs Administrador) -->
                <div class="role-tabs">
                    <button type="button" class="role-tab active" id="tab-vecino" onclick="switchRole('vecino')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Soy Vecino
                    </button>
                    <button type="button" class="role-tab" id="tab-admin" onclick="switchRole('admin')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        Soy Administrador
                    </button>
                </div>

                @if ($errors->any())
                    <div class="login-error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group login-form-group">
                        <label for="email" class="login-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" class="login-input" required autofocus>
                    </div>

                    <div class="form-group login-form-group">
                        <label for="password" class="login-label">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" class="login-input" required>
                    </div>

                    <div class="login-options">
                        <label class="login-checkbox-label">
                            <input type="checkbox" name="remember" class="login-checkbox">
                            Recordar sesión
                        </label>
                    </div>

                    <button type="submit" class="login-btn" id="submit-btn">Ingresar al Sistema</button>
                </form>

                <div class="login-register-prompt" style="text-align: center; margin-top: 20px;">
                    <p style="color: rgba(245, 249, 246, 0.7); font-size: 0.9rem; margin: 0;">
                        ¿No tienes una cuenta? <a href="/register" style="color: var(--spring-leaf); text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='var(--paper-white)'" onmouseout="this.style.color='var(--spring-leaf)'">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth/login.js') }}"></script>
@endsection
