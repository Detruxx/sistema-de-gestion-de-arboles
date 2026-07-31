FROM webdevops/php-nginx:8.2-alpine

# Le decimos al servidor web que la carpeta pública es "public"
ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production
ENV APP_DEBUG=false

# Para evitar el error de sesiones por si conectas la BD después
ENV SESSION_DRIVER=cookie
ENV CACHE_STORE=file

WORKDIR /app

# Copiamos el código de la aplicación
COPY . .

# Instalamos dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ajustamos permisos para que el servidor pueda leer/escribir
RUN chown -R application:application /app
