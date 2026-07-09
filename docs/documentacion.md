# [Nombre del Proyecto]

> Aplicación web orientada a la gestión integral de arbolado urbano, permitiendo su registro, seguimiento de reclamos y visualización geográfica a través de un mapa interactivo centralizado.Breve descripción de 1 o 2 líneas sobre qué es el proyecto, para qué sirve y qué problema resuelve.

---

## Tecnologías Utilizadas

Enumera aquí las tecnologías principales del proyecto:
- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Javascript Vanilla, Blade.PHP y CSS
- **Base de Datos:** MySQL
- **Otras herramientas:** API de autocompletado y normalizacion de calles (USIG-GCBA), API de Nominatim (OpenStreetMap) para la busqueda de calles por fuera del CABA y para geocodificacion inversa, API de Servidor de Mosaicos (Tiles) de OpenStreetMap que usa la libreria Leaflet para la visualizacion del mapa, API de Cloudflare Turnstile para el uso de Captcha.


## Requisitos Previos
Para poder ejecutar este proyecto, necesitas tener instalado en tu entorno local:
- **PHP:** >= 8.2 (Laravel 12 es compatible con PHP >= 8.2)
- **Composer:** Version 2.x

- **Base de Datos:** MySQL (recomendado usar a través de **XAMPP** para entorno Windows, iniciando los módulos de Apache y MySQL).



##  Instalación y Configuración

Sigue estos pasos para levantar el entorno de desarrollo local:

**1. Instalar dependencias del Backend:**
```bash
composer install
```

**2. Configurar las variables de entorno:**
```bash
# Copiar archivo de ejemplo para crear la configuración local
cp .env.example .env
```

**3. Generar la clave de la aplicación Laravel:**
```bash
php artisan key:generate
```

**4. Configurar la Base de Datos:**
Abre el archivo `.env` y ajusta según tu motor de DB.
*Si es MySQL:*
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_arboles
DB_USERNAME=root
DB_PASSWORD=
```
*Si es SQLite:*
```env
DB_CONNECTION=sqlite
```

**5. Correr las migraciones y seeders (en caso de no contar con base de datos):**
```bash
php artisan migrate --seed
```

**6. Enlazar el Storage (Archivos públicos):**
```bash
php artisan storage:link
```

---

## Uso / Ejecución Local

Para poner en marcha el proyecto, necesitas correr:

```bash
php artisan serve
```

El proyecto estará disponible en: `http://localhost:8000`

---

## Estructura Principal del Proyecto

Breve mención a dónde están las partes clave del código:
- `app/` - Controladores, Modelos y Lógica principal (Backend).
- `database` - Migraciones, seeders y factorias de la base de datos.
- `public/css/` - Hoja de estilos de la aplicación
- `public/js/` - Lógica de los módulos JS del frontend y dashboards.
- `resources/views/` - Vistas Blade y estructura HTML.
- `routes/` - Archivos de rutas (web.php y api.php).

---

## Credenciales de Prueba

Si utilizaste Seeders, deja aquí credenciales de prueba para facilitar la vida de quien clone el proyecto:

| Rol | Email | Contraseña |
| --- | --- | --- |
| Vecino | vecino@example.com | vecino123 |
| Inspector | inspector@example.com | inspector123 |
| Administrador | admin@example.com | admin123 |
| Empresa | empresa@example.com | empresa123 |

---

## Notas adicionales

En el .env estan las claves para el captcha y el usuario de prueba para testear el envio de mails.
