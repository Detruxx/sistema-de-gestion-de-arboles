@extends('layouts.app')

@section('title', 'Registro | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <main class="tramites-page-container" style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        <div class="bg-blurred-image" style="background-image: url('{{ asset('images/home/hero-bg.jpg') }}'); opacity: 0.15; position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;"></div>
        
        <section class="register-wrapper reveal" style="max-width: 650px; width: 100%; position: relative; z-index: 10;">
            <div class="contact-form">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="font-family: var(--font-display); color: var(--deep-canopy); font-size: 2.2rem; margin-bottom: 8px;">Crear Cuenta</h2>
                    <p style="color: var(--forest-night); font-size: 1rem;">Regístrate para reportar y cuidar los árboles de tu comuna</p>
                </div>

                @if ($errors->any())
                    <div class="track-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Mensaje de error personalizado para validación frontend -->
                <div id="js-error-box" class="track-error" style="display: none;">
                    <ul id="js-error-list" style="margin: 0; padding-left: 20px;"></ul>
                </div>

                <form method="POST" action="/register" id="register-form">
                    @csrf

                    <!-- Fila de Nombre y Apellido -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="name">Nombre <span class="required-asterisk">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Juan" class="form-control" required autofocus>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="last_name">Apellido <span class="required-asterisk">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Pérez" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila de Fecha Nacimiento y DNI -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="dob">Fecha de Nacimiento <span class="required-asterisk">*</span></label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob') }}" class="form-control" required>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="dni">DNI <span class="required-asterisk">*</span></label>
                            <input type="text" id="dni" name="dni" value="{{ old('dni') }}" placeholder="12345678" class="form-control" required>
                        </div>
                    </div>

                    <!-- Campo Domicilio (Residencia) -->
                    <div class="form-group">
                        <label for="address">Domicilio <span class="required-asterisk">*</span></label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Av. de Mayo 1234, CABA" class="form-control" required>
                    </div>

                    <!-- Campo Correo -->
                    <div class="form-group">
                        <label for="email">Correo Electrónico <span class="required-asterisk">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="juan.perez@correo.com" class="form-control" required>
                    </div>

                    <!-- Fila de Contraseñas -->
                    <div class="register-form-row">
                        <div class="form-group register-form-group">
                            <label for="password">Contraseña <span class="required-asterisk">*</span></label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="password" id="password" name="password" placeholder="••••••••" class="form-control" required style="padding-right: 40px; width: 100%;">
                                <button type="button" style="position: absolute; right: 15px; background: none; border: none; cursor: pointer; color: var(--forest-night); opacity: 0.6; display: flex; padding: 0;" onclick="togglePasswordVisibility('password', this)" aria-label="Mostrar/Ocultar Contraseña" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group register-form-group">
                            <label for="password_confirmation">Confirmar Contraseña <span class="required-asterisk">*</span></label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="form-control" required style="padding-right: 40px; width: 100%;">
                                <button type="button" style="position: absolute; right: 15px; background: none; border: none; cursor: pointer; color: var(--forest-night); opacity: 0.6; display: flex; padding: 0;" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Mostrar/Ocultar Contraseña" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px; text-align: center;">
                        <button type="submit" class="btn-main-cta" id="submit-btn" style="width: 100%;">Registrarse</button>
                    </div>
                </form>

                <div style="text-align: center; margin-top: 25px;">
                    <p style="color: var(--forest-night); font-size: 0.95rem; margin: 0;">
                        ¿Ya tienes una cuenta? <a href="/login" style="color: var(--deep-canopy); text-decoration: underline; font-weight: 600;">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth/register.js') }}"></script>
@endsection
