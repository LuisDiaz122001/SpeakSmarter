# SpeakSmarter

Plataforma de cursos y materiales para profesores.

README temporal del proyecto. Este archivo se actualizará cuando el proyecto esté terminado.

---

## Descripción

Este proyecto es una plataforma web orientada a profesores, donde podrán:

* Crear y administrar cursos.
* Subir materiales de clase.
* Gestionar usuarios y roles.
* Organizar contenido para sus estudiantes.

Actualmente el proyecto se encuentra en desarrollo.

---

## Tecnologías utilizadas

* Laravel
* PHP
* Vue.js
* JavaScript
* Vite
* Artisan
* MySQL
* npm

---

## Funcionalidades implementadas hasta ahora

* Instalación inicial de Laravel.
* Configuración de Vite.
* Integración con Vue.
* Sistema base de usuarios.
* Sistema base de roles.
* Configuración del entorno de desarrollo.

---

## Funcionalidades planeadas

* Gestión completa de cursos.
* Gestión de materiales y archivos.
* Panel para profesores.
* Panel para administradores.
* Asignación de permisos por rol.
* Registro e inicio de sesión.
* Gestión de categorías.
* Lecciones dentro de cada curso.
* Vista para estudiantes.
* Sistema de búsqueda.
* Diseño responsive.

---

## Requisitos

Antes de ejecutar el proyecto, asegúrate de tener instalado:

* PHP 8.x
* Composer
* Node.js
* npm
* MySQL
* XAMPP o un servidor local similar

---

## Instalación

Clona el repositorio:

```bash
git clone <url-del-repositorio>
cd SpeakSmarter
```

Instala las dependencias de PHP:

```bash
composer install
```

Instala las dependencias de JavaScript:

```bash
npm install
```

Copia el archivo de entorno:

```bash
cp .env.example .env
```

Genera la clave de la aplicación:

```bash
php artisan key:generate
```

Configura la base de datos en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=speaksmarter
DB_USERNAME=root
DB_PASSWORD=
```

Ejecuta las migraciones:

```bash
php artisan migrate
```

Si tienes seeders:

```bash
php artisan db:seed
```

Inicia el servidor de Laravel:

```bash
php artisan serve
```

En otra terminal, ejecuta Vite:

```bash
npm run dev
```

---

## Estructura inicial del proyecto

```text
app/
bootstrap/
config/
database/
public/
resources/
 ├── js/
 ├── views/
 └── css/
routes/
storage/
```

---

## Scripts útiles

```bash
php artisan serve
php artisan migrate
php artisan db:seed
php artisan make:model Nombre -mcr
npm install
npm run dev
npm run build
```

---

## Estado actual

🚧 Proyecto en desarrollo.

El README será actualizado cuando:

* El sistema de cursos esté terminado.
* Los roles y permisos estén completos.
* Exista documentación final de instalación y despliegue.
* Se agreguen capturas de pantalla y ejemplos de uso.

---

## Autor

Desarrollado por Luis Diaz como proyecto SpeakSmarter, una plataforma educativa para profesores y estudiantes.
