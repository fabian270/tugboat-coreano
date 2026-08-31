# Tugboat Coreano

Aplicación web para aprender coreano (한국어) usando cartillas de fichas (flashcards). Está pensada para hablantes de español, optimizada para **smartphones** (pantallas táctiles, diseño responsive, modo oscuro y claro), y corre en un único contenedor Docker.

## Tecnologías

- **PHP 8.3** (Apache + mod_rewrite, rutas limpias)
- **SQLite** (base de datos local, persistente en un volumen Docker)
- **Bootstrap 5.3** (local, sin CDN; la app funciona sin internet)
- **Docker Compose** (un solo servicio)

## Roles

| Rol | Qué puede hacer |
|-----|-----------------|
| **Admin** | Crear/editar/eliminar usuarios, activarlos o desactivarlos, reiniciar contraseñas, crear/editar/eliminar cartillas y fichas, y consultar el progreso de cada alumno. |
| **Alumno** | Ver sus cartillas, estudiar con flashcards y repasar las pendientes. |

El primer admin se crea automáticamente al iniciar el contenedor.

## Puesta en marcha

```bash
docker compose up --build
```

Abrir `http://localhost:8080`.

Entrar con el admin por defecto:

- Usuario: `admin`
- Contraseña: `admin123`

> **Importante:** cambia la contraseña del admin en pantalla (Usuarios → Editar → Nueva contraseña) o configúrala al levantar. Para más seguridad, usa variables de entorno:

```bash
PUERTO=8081 ADMIN_USUARIO=admin ADMIN_PASSWORD=UnPasswordSeguro docker compose up --build
```

### Variables de entorno

| Variable | Por defecto | Descripción |
|----------|-------------|-------------|
| `PUERTO` | `8080` | Puerto del contenedor en el host |
| `ADMIN_USUARIO` | `admin` | Usuario del admin inicial |
| `ADMIN_PASSWORD` | `admin123` | Contraseña del admin inicial |
| `ADMIN_NOMBRE` | `Administrador` | Nombre visible del admin inicial |

## Contenido inicial

Al primer arranque se crean estas cartillas precargadas (solo si la base de datos está vacía):

- Hangul · Consonantes
- Hangul · Vocales
- Saludos
- Números · nativos
- Números · sino-coreanos
- Vocabulario cotidiano
- Frases útiles

El admin puede agregar, editar o eliminar cartillas y fichas desde el panel.

## Mecánica de estudio

1. Entras a una cartilla.
2. Ves la **cara coreana** (hangul + romanización).
3. Toca la tarjeta para **voltearla** y ver la traducción al español.
4. Pulsa **«Lo sé»** o **«Repasar»** (botones grandes, pensados para el pulgar).
5. Al terminar ves tu resumen y puedes repetir todas o solo las que quedaron pendientes.

El progreso se guarda por usuario (aciertos, repasos y estado de cada ficha).

## Base de datos

- Archivo: `data/coreano.db` dentro de la app (volumen `datos_db` en Docker).
- Se crea el esquema y el contenido inicial automáticamente la primera vez.
- Para restablecer todo: `docker compose down -v` y volver a `docker compose up --build`.

## Estructura

```
├── Dockerfile
├── docker-compose.yml
├── entrypoint.sh
└── app/
    ├── config.php          # arranque y constantes
    ├── database.php        # conexión PDO / SQLite + esquema
    ├── auth.php            # sesión, login, roles, CSRF
    ├── seed.php            # contenido inicial
    ├── src/                # controladores
    ├── public/             # front controller, assets, .htaccess
    └── vistas/             # plantillas (Bootstrap)
```

## Notas

- Tema: el botón ☀/☾ alterna oscuro/claro; se guarda en el navegador y en la cuenta del usuario.
- En Windows, `docker compose` debe ejecutarse desde Docker Desktop.

## Herramientas utilizadas

Esta aplicación fue creada utilizando inteligencia artificial como asistente de desarrollo. Las herramientas utilizadas fueron:

- **OpenCode** — agente de IA para terminal que interactúa con el código, ejecuta comandos, lee/escribe archivos y gestiona el flujo de trabajo
- **Big Pickle** — modelo de IA principal que actuó como agente de codificación, generando y depurando todo el código del proyecto