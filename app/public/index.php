<?php
declare(strict_types=1);

require __DIR__ . '/../config.php';
require BASE_DIR . '/src/controlador_auth.php';
require BASE_DIR . '/src/controlador_alumno.php';
require BASE_DIR . '/src/controlador_admin.php';
require BASE_DIR . '/vistas/layout.php';

$ruta = trim((string) ($_GET['ruta'] ?? ''), '/');

error_reporting(E_ALL);
ini_set('display_errors', '0');

switch ($ruta) {
    case '':
    case 'login':
        procesar_login();
        break;
    case 'logout':
        procesar_logout();
        break;
    case 'cambiar-contrasena':
        pag_cambiar_contrasena();
        break;
    case 'inicio':
        pag_inicio();
        break;
    case 'estudiar':
        pag_estudiar();
        break;
    case 'api/fichas':
        api_fichas();
        break;
    case 'api/progreso':
        api_progreso();
        break;
    case 'api/tema':
        api_tema();
        break;
    case 'admin/usuarios':
        pag_admin_usuarios();
        break;
    case 'admin/cartillas':
        pag_admin_cartillas();
        break;
    case 'admin/fichas':
        pag_admin_fichas();
        break;
    case 'admin/progreso':
        pag_admin_progreso();
        break;
    default:
        http_response_code(404);
        echo 'Página no encontrada.';
}