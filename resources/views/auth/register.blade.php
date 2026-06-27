@extends('layouts.app')

@section('title', 'Registro | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <main class="tramites-page-container register-page-container">
        <section class="register-wrapper reveal">
            <div class="register-card">
                <div class="register-header">
                    <h2 class="register-title">Crear Cuenta</h2>
                    <p class="register-subtitle">Regístrate para reportar y cuidar los árboles de tu comuna</p>
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

                <!-- Mensaje de error personalizado para validación frontend -->
                <div id="js-error-box" class="login-error-box" style="display: none;">
                    <ul id="js-error-list"></ul>
                </div>

                <form method="POST" action="/register" id="register-form">
                    @csrf

                    <!-- Fila de Nombre y Apellido -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="name" class="register-label">Nombre</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Juan" class="register-input" required autofocus>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="last_name" class="register-label">Apellido</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Pérez" class="register-input" required>
                        </div>
                    </div>

                    <!-- Fila de Fecha Nacimiento y DNI -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="dob" class="register-label">Fecha de Nacimiento</label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob') }}" class="register-input" required>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="dni" class="register-label">DNI</label>
                            <input type="text" id="dni" name="dni" value="{{ old('dni') }}" placeholder="12345678" class="register-input" required>
                        </div>
                    </div>

                    <!-- Campo Domicilio (Residencia) -->
                    <div class="form-group register-form-group">
                        <label for="address" class="register-label">Domicilio</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Av. de Mayo 1234, CABA" class="register-input" required>
                    </div>

                    <!-- Campo Correo -->
                    <div class="form-group register-form-group">
                        <label for="email" class="register-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="juan.perez@correo.com" class="register-input" required>
                    </div>

                    <!-- Fila de Contraseñas -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="password" class="register-label">Contraseña</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" class="register-input" required>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="password_confirmation" class="register-label">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="register-input" required>
                        </div>
                    </div>

                    <button type="submit" class="register-btn" id="submit-btn" style="margin-top: 15px;">Registrarse</button>
                </form>

                <div class="login-register-prompt" style="text-align: center; margin-top: 25px;">
                    <p style="color: var(--forest-night); opacity: 0.8; font-size: 0.9rem; margin: 0;">
                        ¿Ya tienes una cuenta? <a href="/login" style="color: var(--living-moss); text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='var(--deep-canopy)'" onmouseout="this.style.color='var(--living-moss)'">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth/register.js') }}"></script>
@endsection
