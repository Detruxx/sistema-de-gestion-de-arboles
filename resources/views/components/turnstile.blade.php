<!-- Aca va el script de turnstile -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<!-- Si o si se tiene que llamar cf-turnstile para cargar los estilos del script -->
 <!-- configuramos para que el data-sitekey se saque del .env para mayor seguridad evitando hardcodearlo  -->
<div class="cf-turnstile" data-sitekey="{{ config('services.cloudflare.turnstile.site_key') }}"></div>