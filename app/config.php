<?php
declare(strict_types=1);

const NOMBRE_APP = 'Tugboat Coreano';
const VERSION_ASSETS = 4;
const BASE_DIR    = __DIR__;
const DATOS_DIR   = BASE_DIR . '/data';
const DB_PATH     = DATOS_DIR . '/coreano.db';

define('ADMIN_USUARIO', getenv('ADMIN_USUARIO') ?: 'admin');
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'admin123');
define('ADMIN_NOMBRE', getenv('ADMIN_NOMBRE') ?: 'Administrador');

date_default_timezone_set('UTC');

require BASE_DIR . '/util.php';
require BASE_DIR . '/database.php';
require BASE_DIR . '/auth.php';
require BASE_DIR . '/seed.php';

session_start();

verificar_instalacion();