@extends('layouts.app')

@section('title', 'Iniciar Sesión | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-login', 'active')

@section('content')
    <main class="tramites-page-container" style="display: flex; align-items: center; justify-content: center; min-height: 90vh;">
        <section class="login-wrapper reveal" style="max-width: 450px; width: 100%; margin: 20px auto; z-index: 10; position: relative;">
            <div class="contact-form" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 16px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="font-family: var(--font-display); color: var(--deep-canopy); font-size: 2rem; margin-bottom: 8px;">Iniciar Sesión</h2>
                    <p style="color: var(--living-moss); font-size: 0.95rem;">Acceso para inspectores y personal autorizado.</p>
                </div>

                @if ($errors->any())
                    <div style="background-color: #fce8e6; border: 1px solid #f29c9f; border-radius: 8px; padding: 15px; margin-bottom: 25px; color: #a82424; font-size: 0.9rem;">
                        <ul style="margin: 0; padding-left: 15px; list-style-type: square;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 8px; color: var(--forest-night); font-weight: 600; font-size: 0.9rem;">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="correo.inspector@treeba.gob.ar" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%; outline: none; transition: border-color 0.3s ease;" required autofocus>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 8px; color: var(--forest-night); font-weight: 600; font-size: 0.9rem;">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" style="background-color: var(--paper-white); border: 1px solid rgba(45, 122, 79, 0.3); border-radius: 8px; padding: 15px; color: var(--forest-night); font-family: var(--font-body); font-size: 1rem; width: 100%; outline: none; transition: border-color 0.3s ease;" required>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: var(--living-moss); font-size: 0.9rem; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="remember" style="accent-color: var(--living-moss); cursor: pointer;">
                            Recordar sesión
                        </label>
                    </div>

                    <button type="submit" class="btn-main-cta" style="width: 100%; text-align: center; border: none; font-size: 1.05rem; padding: 12px 24px;">Ingresar al Sistema</button>
                </form>
            </div>
        </section>
    </main>
@endsection
