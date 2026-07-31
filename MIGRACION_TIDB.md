# Pasos Pendientes para la Migración a TiDB (Render)

Nos quedamos en el proceso de subir la base de datos a TiDB solucionando el problema de `AUTO_INCREMENT`. Ya tenemos el archivo `data_only.sql` listo.

### Paso 1: Ejecutar Migraciones en Render
Ya hemos subido la ruta secreta al repositorio.
1. Espera a que **Render** termine de hacer el Deploy (que el panel diga *Live*).
2. Entra desde tu navegador a la siguiente URL (reemplazando tu dominio real):
   `https://tu-sitio-arboles.onrender.com/run-migrations`
3. Si todo sale bien, la pantalla te dirá: *"Migraciones (Estructura de la BD) creadas con exito en TiDB."*

### Paso 2: Subir los Datos a TiDB (Usando Datos Móviles)
Como tu red local bloquea el puerto 4000, conéctate a tus **datos móviles (hotspot)** desde tu nuevo dispositivo. Abre la terminal en la carpeta de este proyecto y ejecuta el siguiente comando:

**Si el otro dispositivo es Windows (con XAMPP instalado):**
```powershell
cmd.exe /c "C:\xampp\mysql\bin\mysql.exe -h gateway01.sa-east-1.prod.aws.tidbcloud.com -P 4000 -u 4DMeh4ugq9uw6sB.root -p test --ssl < data_only.sql"
```

**Si el otro dispositivo es Mac/Linux (o tiene mysql instalado globalmente):**
```bash
mysql -h gateway01.sa-east-1.prod.aws.tidbcloud.com -P 4000 -u 4DMeh4ugq9uw6sB.root -p test --ssl < data_only.sql
```

*(Te pedirá la contraseña de TiDB, la pegas, le das Enter y esperas a que termine).*

### Paso 3: Limpieza
Una vez que funcione todo, avísale a Antigravity (a mí) desde el otro dispositivo para borrar la ruta `/run-migrations` y los archivos SQL por seguridad. ¡Éxitos!
