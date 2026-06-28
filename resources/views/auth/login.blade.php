@extends('layouts.app')

@section('title', 'Iniciar Sesión | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')

@section('content')
    <main class="tramites-page-container login-page-container">
        <section class="login-wrapper reveal">


            <div class="login-card">
                <div class="login-header" style="margin-bottom: 25px;">
                    <h2 class="login-title">Ingresar</h2>
                    <p id="role-subtitle">Inicia sesión en la plataforma TreeBA</p>
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
