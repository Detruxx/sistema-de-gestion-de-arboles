@extends('layouts.app')

@section('title', 'Iniciar Sesión | Arborea')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    <main class="tramites-page-container" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: #ffffff;">
        <div class="login-bg-branches-left" style="background-image: url('{{ asset('img/user/login_background_new.png') }}'); position: absolute; top: 0; left: 0; width: 37.5%; height: 100%; background-size: 200% auto; background-position: left center; background-repeat: no-repeat; z-index: 0;"></div>
        <div class="login-bg-branches-right" style="background-image: url('{{ asset('img/user/login_background_new.png') }}'); position: absolute; top: 0; right: 0; width: 37.5%; height: 100%; background-size: 200% auto; background-position: right center; background-repeat: no-repeat; z-index: 0;"></div>
        
        <section class="login-wrapper reveal" style="max-width: 450px; width: 100%; position: relative; z-index: 10;">
            <div class="contact-form">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="font-family: var(--font-display); color: var(--deep-canopy); font-size: 2.2rem; margin-bottom: 8px; line-height: 1.3; padding-bottom: 5px;">Ingresar</h2>
                    <p style="color: var(--forest-night); font-size: 1rem;">Inicia sesión en la plataforma Arborea</p>
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

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Correo Electrónico <span class="required-asterisk">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" class="form-control" required autofocus>
                    </div>

                    <div class="form-group">
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

                    <div class="form-group checkbox-group" style="margin-top: 15px; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="remember" id="remember" style="accent-color: var(--spring-leaf); margin: 0; width: 18px; height: 18px; cursor: pointer;">
                        <label for="remember" style="font-weight: normal; color: var(--forest-night); margin: 0; cursor: pointer;">Recordar sesión</label>
                    </div>

                    <!-- Captcha de seguridad Turnstile -->
                    <div class="form-group" style="margin-top: 15px; margin-bottom: 15px;">
                        <x-turnstile />
                        @error('cf-turnstile-response')
                            <span class="error-text" style="color: #d32f2f; font-size: 0.85rem; display: block; margin-top: 5px;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-actions" style="margin-top: 30px; text-align: center;">
                        <button type="submit" class="btn-main-cta" style="width: 100%;">Ingresar al Sistema</button>
                    </div>
                </form>

                <div style="text-align: center; margin-top: 25px;">
                    <p style="color: var(--forest-night); font-size: 0.95rem; margin: 0;">
                        ¿No tienes una cuenta? <a href="/register" style="color: var(--deep-canopy); text-decoration: underline; font-weight: 600;">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection

