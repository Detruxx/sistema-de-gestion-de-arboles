
<!-- esto llama a la vista navbar-->
<!-- llamo a attributes para poder usar la clase navbar desde el layout app y asi agregarle clases dinamicamente -->
<header {{ $attributes->merge(['class' => 'navbar']) }} id="navbar">

        <a href="/" class="nav-brand">
            <div class="logo"><img src="{{ asset('img/logo.png') }}" alt="logo"></div>
            <span class="brand-name">Arborea</span>
        </a>
        <nav class="nav-links">
            <a href="/mapa" class="nav-pill">Mapa</a>
            <a href="#modificaciones" class="nav-pill">Modificaciones</a>
            <a href="#cuidados" class="nav-pill">Cuidados</a>
            <a href="#reclamos" class="nav-pill">Reclamos</a>
            <a href="#contacto" class="nav-pill">Contacto</a>
            <a href="/login" class="nav-pill btn-login">Login</a>
        </nav>
</header>
