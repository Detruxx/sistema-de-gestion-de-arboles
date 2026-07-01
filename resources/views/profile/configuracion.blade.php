@extends('layouts.app')

@section('title', 'Configuración de Cuenta | TreeBA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile/profile.css') }}">
    <style>
        .profile-avatar-wrapper:hover .avatar-edit-overlay {
            opacity: 1 !important;
        }
        .default-avatar-option:hover {
            transform: scale(1.15);
            border-color: var(--living-moss) !important;
        }
        .default-avatar-option.selected {
            border-color: var(--living-moss) !important;
            box-shadow: 0 0 0 3px rgba(91, 191, 140, 0.4);
        }
    </style>
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
                <div class="profile-card-header" style="flex-direction: column; align-items: center; text-align: center; border-bottom: none; padding-bottom: 10px;">
                    <div class="profile-avatar-wrapper" style="position: relative; cursor: pointer; width: 100px; height: 100px;">
                        <div class="profile-avatar-large" id="profile-avatar-preview" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: rgba(91, 191, 140, 0.15); border: 3px solid var(--living-moss); transition: all 0.3s ease;">
                            <svg id="profile-avatar-svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <img id="profile-avatar-img" src="" alt="Avatar" style="display: none; width: 100%; height: 100%; object-fit: cover;">
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
                <div class="avatar-selection-container" style="border-top: 1px solid rgba(45, 122, 79, 0.15); border-bottom: 1px solid rgba(45, 122, 79, 0.15); padding: 20px 0; display: flex; flex-direction: column; gap: 15px; align-items: center;">
                    <span class="info-label" style="font-size: 0.8rem;">Elegir avatar o subir foto</span>
                    <div class="default-avatars" style="display: flex; gap: 15px; justify-content: center;">
                        <img class="default-avatar-option" data-avatar="/img/avatar1.png" src="{{ asset('img/avatar1.png') }}" alt="Avatar 1" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                        <img class="default-avatar-option" data-avatar="/img/avatar2.png" src="{{ asset('img/avatar2.png') }}" alt="Avatar 2" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                        <img class="default-avatar-option" data-avatar="/img/avatar3.png" src="{{ asset('img/avatar3.png') }}" alt="Avatar 3" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;">
                    </div>
                    <div style="display: flex; justify-content: center; gap: 10px; width: 100%; flex-wrap: wrap;">
                        <button type="button" class="btn-main-cta" onclick="document.getElementById('avatar-file-input').click()" style="background-color: transparent; border: 2px solid var(--living-moss); color: var(--living-moss); font-size: 0.85rem; padding: 6px 12px; margin: 0;">
                            Subir propia imagen
                        </button>
                        <button type="button" id="btn-remove-avatar" class="btn-main-cta" style="background-color: transparent; border: 2px solid #d32f2f; color: #d32f2f; font-size: 0.85rem; padding: 6px 12px; margin: 0; display: none;">
                            Quitar foto
                        </button>
                    </div>
                    <input type="file" id="avatar-file-input" accept="image/*" style="display: none;">
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const avatarSvg = document.getElementById('profile-avatar-svg');
            const avatarImg = document.getElementById('profile-avatar-img');
            const navAvatarSvg = document.getElementById('nav-avatar-svg');
            const navAvatarImg = document.getElementById('nav-avatar-img');
            
            const fileInput = document.getElementById('avatar-file-input');
            const removeBtn = document.getElementById('btn-remove-avatar');
            const defaultOptions = document.querySelectorAll('.default-avatar-option');

            // Cargar avatar inicial
            const savedAvatar = localStorage.getItem('user_avatar');
            if (savedAvatar) {
                updateAvatarUI(savedAvatar);
            }

            // Manejar click en avatares predeterminados
            defaultOptions.forEach(opt => {
                opt.addEventListener('click', () => {
                    const avatarUrl = opt.getAttribute('data-avatar');
                    localStorage.setItem('user_avatar', avatarUrl);
                    updateAvatarUI(avatarUrl);
                });
            });

            // Manejar subida de archivo propio
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const base64Data = event.target.result;
                        localStorage.setItem('user_avatar', base64Data);
                        updateAvatarUI(base64Data);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Eliminar avatar
            removeBtn.addEventListener('click', () => {
                localStorage.removeItem('user_avatar');
                
                // Resetear vistas previas del perfil
                avatarImg.style.display = 'none';
                avatarImg.src = '';
                avatarSvg.style.display = 'block';

                // Resetear navbar
                if (navAvatarImg && navAvatarSvg) {
                    navAvatarImg.style.display = 'none';
                    navAvatarImg.src = '';
                    navAvatarSvg.style.display = 'block';
                }

                // Ocultar botón eliminar y quitar seleccionados
                removeBtn.style.display = 'none';
                defaultOptions.forEach(o => o.classList.remove('selected'));
                fileInput.value = '';
            });

            function updateAvatarUI(src) {
                // Actualizar perfil
                avatarImg.src = src;
                avatarImg.style.display = 'block';
                avatarSvg.style.display = 'none';

                // Actualizar navbar
                if (navAvatarImg && navAvatarSvg) {
                    navAvatarImg.src = src;
                    navAvatarImg.style.display = 'block';
                    navAvatarSvg.style.display = 'none';
                }

                // Mostrar botón de eliminar
                removeBtn.style.display = 'inline-block';

                // Marcar cuál está seleccionado si es predeterminado
                defaultOptions.forEach(o => {
                    if (o.getAttribute('data-avatar') === src) {
                        o.classList.add('selected');
                    } else {
                        o.classList.remove('selected');
                    }
                });
            }
        });
    </script>
@endsection
