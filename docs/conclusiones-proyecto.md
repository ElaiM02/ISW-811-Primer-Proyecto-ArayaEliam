# Conclusiones del Proyecto

# Estudiante: Edgar Eliam Araya Alvarado

# Curso: ISW811 — Aplicaciones Web Utilizando Software Libre

# Proyecto 1: Laravel From Scratch 2026

# Profesor: Misael Matamoros Soto

---

## 1. Principales aprendizajes

A lo largo del proyecto construí una aplicación web completa con **Laravel 12**, partiendo desde
cero hasta una app funcional para gestionar ideas. Los aprendizajes más importantes fueron:

- **Fundamentos de Laravel:** routing, controladores, migraciones, modelos Eloquent y el patrón MVC.
- **Blade y componentes:** creación de componentes reutilizables (`<x-layout>`, `<x-card>`,
  `<x-modal>`, `<x-form.field>`, iconos SVG) para no repetir código.
- **Relaciones Eloquent:** `belongsTo` y `hasMany` para asociar ideas con usuarios y con sus pasos.
- **Enums y casts:** uso de un enum `IdeaStatus` con casteo automático, y `AsArrayObject` para
  guardar arrays (links) como JSON.
- **Autenticación y autorización:** registro/login, middleware `auth`/`guest`, Gates y Policies
  para asegurar que cada usuario solo acceda a sus propias ideas.
- **AlpineJS:** interactividad del lado del cliente (modales, mensajes flash, inputs dinámicos de
  links y pasos) sin necesidad de un framework pesado.
- **Formularios avanzados:** validación con Form Requests, subida de imágenes al almacenamiento,
  inputs dinámicos y edición precargada.
- **Clases de acción:** refactorización de la lógica de negocio (`CreateIdea`, `UpdateIdea`) para
  mantener los controladores simples y reutilizar la lógica.
- **Pruebas automatizadas:** uso de Pest para verificar registro, login, creación y autorización.
- **Despliegue:** publicación del proyecto en la máquina virtual con Apache y manejo del ciclo
  de cambios → build → producción.

---

## 2. Dificultades encontradas

- **Testing de navegador con Pest 4:** el curso usa `visit()` (browser testing), que requiere
  **PHP 8.3 y Pest 4**, pero la máquina virtual corre **PHP 8.2**. Intentar instalarlo provocó
  bloqueos del sistema (soft lockups) por la lentitud de la carpeta compartida de VirtualBox.
- **Nombres de componentes distintos al video:** como venía de talleres anteriores con nombres
  propios (`<x-layout>` en vez de `<x-layouts.idea>`, carpeta `form/` en vez de un `<x-form>`),
  varios componentes daban error "Unable to locate a class or view for component".
- **Componentes y assets faltantes:** errores por iconos no creados (`icons.arrow-back`,
  `icon.close`), rutas sin nombre (`Route [idea.index] not defined`) y clases de Tailwind sin
  compilar.
- **Migración duplicada:** al adelantar la columna `title`, quedaron dos migraciones creando la
  tabla `ideas`, lo que rompía `migrate`.
- **Mass assignment y columnas inexistentes:** intentar insertar `steps` e `image` directamente
  en la tabla `ideas` (columnas que no existen) generaba errores SQL.
- **Autorización con nombre equivocado:** un 403 en todas las ideas porque la policy tenía el
  método `update` pero el controlador llamaba `workWith`.
- **HTML mal cerrado en formularios:** etiquetas `<fieldset>` sin cerrar y bloques duplicados que
  desordenaban por completo el modal.

---

## 3. Soluciones aplicadas

- **Testing en la VM:** en lugar de browser testing con `visit()`, usé **feature tests con Pest 3**
  (`get()`, `post()`, `patch()`), que reproducen las mismas aserciones sin abrir un navegador y sí
  corren en PHP 8.2. Configuré **SQLite en memoria** en `phpunit.xml` para no borrar la base real.
- **Adaptación de nombres:** ajusté cada componente al nombre real de mi proyecto y creé los
  componentes/iconos que faltaban.
- **Rutas con nombre:** nombré todas las rutas de ideas (`idea.index`, `idea.show`, etc.) para que
  los `route()` funcionaran y fueran más mantenibles.
- **Recompilación de assets:** aprendí que tras cambiar clases de Tailwind debía correr
  `npm run build` (y **no** `npm run dev`, que rompía los estilos al servir desde `localhost:5173`
  inalcanzable desde el dominio de la VM).
- **Migración única:** eliminé la migración duplicada, edité la original y usé `migrate:fresh`.
- **Separación de datos:** en los controladores/acciones usé `$request->safe()->except([...])`
  para excluir `steps` e `image` del `create()`, guardando la imagen con `store('ideas','public')`
  y los pasos con `createMany()` en su propia tabla.
- **Policy corregida:** renombré el método de la policy a `workWith` para que coincidiera con
  `Gate::authorize('workWith', $idea)`.
- **`php artisan storage:link`:** creé el enlace simbólico para servir las imágenes subidas.

---

## 4. Funcionalidades más relevantes

- **Modales con AlpineJS** para crear y editar ideas sin recargar la página, con transiciones y
  cierre por Escape o clic fuera.
- **Inputs dinámicos** de links y pasos accionables (agregar/eliminar en el momento).
- **Filtrado por estado** con contadores por categoría, usando `when()` en la consulta.
- **Autorización con Policies**, que garantiza que un usuario no vea ni modifique ideas ajenas.
- **Carga de imágenes destacadas** al almacenamiento de Laravel con `storage:link`.
- **Clases de acción** (`CreateIdea`, `UpdateIdea`) que mantienen el controlador limpio y la
  lógica reutilizable, dentro de transacciones de base de datos.
- **Descripción con Markdown** mediante un accessor y el plugin de tipografía de Tailwind.
- **Edición de perfil** con notificación al correo anterior cuando el email cambia.

---

## 5. Posibles mejoras futuras

- **Soporte de equipos (teams):** permitir que varios usuarios colaboren sobre las mismas ideas,
  con autorización más fina (ver pero no editar/eliminar ideas de otros).
- **Actualizar el entorno a PHP 8.3** para poder usar el browser testing real con Pest 4 y así
  tener pruebas de extremo a extremo con navegador.
- **Preservar el estado `completed` de los pasos** de forma más robusta (por ejemplo con `upsert`
  en lugar de borrar y recrear).
- **Confirmación de contraseña** al cambiar credenciales sensibles del perfil.
- **Notificaciones por más canales** (base de datos, en la interfaz) además del correo.
- **Paginación** de las ideas cuando la lista crezca.
- **Optimización de la máquina virtual** (más RAM/CPU o sacar `vendor/` de la carpeta compartida)
  para acelerar Composer y npm.

---

## Reflexión final

Este proyecto integró prácticamente todos los conceptos del curso en una sola aplicación real. La
mayor lección no fue solo aprender Laravel, sino **resolver problemas de entorno y adaptación**:
cuando algo del video no funcionaba en mi máquina virtual, tuve que entender la causa raíz
(versiones de PHP, nombres de componentes, migraciones, autorización) y aplicar una solución
equivalente. Ese proceso de depuración fue tan valioso como la funcionalidad final.
