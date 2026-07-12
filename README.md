# Idea — Proyecto 1 (Laravel From Scratch 2026)

Aplicación web para gestionar **ideas**: crear, editar, filtrar por estado, asociar enlaces,
registrar pasos accionables, subir imágenes destacadas y editar el perfil de usuario.

- **Curso:** ISW811 — Aplicaciones Web Utilizando Software Libre
- **Estudiante:** Edgar Eliam Araya Alvarado
- **Profesor:** Misael Matamoros Soto
- **Basado en:** [Laravel From Scratch (2026 Edition)](https://laracasts.com/series/laravel-from-scratch-2026)

---

## Características

- Autenticación (registro, login, logout) con middleware `auth`/`guest`.
- CRUD de ideas mediante **modales con AlpineJS** (crear y editar sin recargar).
- **Estado** de cada idea (`pending`, `in_progress`, `completed`) con enum y etiquetas de color.
- **Filtrado** por estado con contadores por categoría.
- **Enlaces** y **pasos accionables** dinámicos (uno o varios por idea).
- **Carga de imágenes** destacadas al almacenamiento de Laravel.
- **Autorización** con Policies (cada usuario solo gestiona sus propias ideas).
- **Descripción con Markdown** (accessor + Tailwind Typography).
- **Edición de perfil** con notificación al correo anterior si el email cambia.
- **Clases de acción** (`CreateIdea`, `UpdateIdea`) y **pruebas automatizadas** con Pest.

---

## Requisitos

- PHP **8.2+**
- Composer
- Node.js y npm
- MySQL (o MariaDB) — o SQLite
- Servidor web (Apache/Nginx) o `php artisan serve`

Este proyecto está pensado para correr dentro de una **máquina virtual Vagrant** (Debian + Apache),
accesible en `http://lfts.isw811.xyz`.

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio> lfts.isw811.xyz
cd lfts.isw811.xyz

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de JavaScript
npm install

# 4. Crear el archivo de entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar la base de datos en .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Ejecutar las migraciones
php artisan migrate

# 7. Crear el enlace simbólico de almacenamiento (para las imágenes subidas)
php artisan storage:link

# 8. Compilar los assets
npm run build
```

---

## Ejecución

**Opción A — Con la máquina virtual (Apache):**

```bash
# desde la carpeta de la VM (host)
vagrant up
```

Luego visita `http://lfts.isw811.xyz` en el navegador.

**Opción B — Servidor de desarrollo local:**

```bash
php artisan serve
```

Visita `http://127.0.0.1:8000`.

> **Nota sobre assets:** al cambiar clases de Tailwind o componentes, recompila con `npm run build`.
> No uses `npm run dev` cuando accedas por el dominio de la VM (el servidor de Vite en `localhost:5173`
> no es alcanzable desde ese dominio y los estilos no cargarían).

---

## Pruebas

Las pruebas usan **Pest** y **SQLite en memoria** (configurado en `phpunit.xml`), por lo que no
afectan la base de datos real.

```bash
# correr toda la suite
vendor/bin/pest

# o con artisan
php artisan test

# correr un archivo específico
vendor/bin/pest tests/Feature/CreateIdeaTest.php
```

Cubren: registro, login, logout, validación, creación de ideas y autorización.

> El curso usa *browser testing* con `visit()` (Pest 4 / PHP 8.3). Como este entorno usa PHP 8.2,
> las pruebas se implementaron como **feature tests** equivalentes (`get`, `post`, `patch`).

---

## Formato de código

```bash
composer run format   # ejecuta Rector y Pint
```

---

## Despliegue (documentado)

El despliegue se realiza en la **máquina virtual** con Apache. Flujo para publicar cambios:

```bash
# dentro de la VM, en la carpeta del proyecto
git pull

composer install --no-dev --optimize-autoloader
npm install && npm run build

php artisan migrate --force
php artisan storage:link      # solo la primera vez
php artisan config:cache
php artisan route:cache
```

El sitio queda disponible en `http://lfts.isw811.xyz` mediante el VirtualHost de Apache
(`lfts.isw811.xyz.conf`), con `DocumentRoot` apuntando a la carpeta `public/`.

> El video original despliega con **Laravel Forge** (push a GitHub → deploy automático). En este
> curso el despliegue equivalente es manual en la VM, como se documenta arriba.

---

## Documentación del proceso

El detalle del desarrollo, episodio por episodio, está en la carpeta `docs/`:

- `docs/entregable01.md`, `docs/entregable02.md`, `docs/entregable03.md` — avances por entregable.
- `docs/conclusiones-proyecto.md` — conclusiones y reflexión final.
- `docs/Images-entregableXX/` — capturas de pantalla como evidencia.

---

## Licencia

Proyecto académico basado en Laravel (framework open source bajo licencia
[MIT](https://opensource.org/licenses/MIT)).
