<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        if (!is_dir(DATOS_DIR)) {
            mkdir(DATOS_DIR, 0775, true);
        }
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA journal_mode = WAL;');
        $pdo->exec('PRAGMA foreign_keys = ON;');
    }
    return $pdo;
}

function crear_esquema(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS usuarios (
            id                    INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre                TEXT NOT NULL,
            usuario               TEXT NOT NULL UNIQUE,
            password_hash         TEXT NOT NULL,
            rol                   TEXT NOT NULL DEFAULT \'alumno\',
            activo                INTEGER NOT NULL DEFAULT 1,
            debe_cambiar_password INTEGER NOT NULL DEFAULT 0,
            tema                  TEXT NOT NULL DEFAULT \'oscuro\',
            creado_en             TEXT NOT NULL DEFAULT (datetime(\'now\'))
        );'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS cartillas (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo      TEXT NOT NULL,
            descripcion TEXT NOT NULL DEFAULT \'\',
            color       TEXT NOT NULL DEFAULT \'primary\',
            orden       INTEGER NOT NULL DEFAULT 0,
            creado_en   TEXT NOT NULL DEFAULT (datetime(\'now\'))
        );'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS fichas (
            id                  INTEGER PRIMARY KEY AUTOINCREMENT,
            cartilla_id         INTEGER NOT NULL REFERENCES cartillas(id) ON DELETE CASCADE,
            hangul              TEXT NOT NULL,
            romanizacion        TEXT NOT NULL DEFAULT \'\',
            traduccion          TEXT NOT NULL,
            ejemplo             TEXT NOT NULL DEFAULT \'\',
            ejemplo_traduccion  TEXT NOT NULL DEFAULT \'\',
            orden               INTEGER NOT NULL DEFAULT 0
        );'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS progreso (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            usuario_id      INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
            ficha_id        INTEGER NOT NULL REFERENCES fichas(id) ON DELETE CASCADE,
            lo_se           INTEGER NOT NULL DEFAULT 0,
            repasar         INTEGER NOT NULL DEFAULT 0,
            vistas          INTEGER NOT NULL DEFAULT 0,
            ultimo_estado   TEXT NOT NULL DEFAULT \'repasar\',
            actualizado_en  TEXT NOT NULL DEFAULT (datetime(\'now\')),
            UNIQUE (usuario_id, ficha_id)
        );'
    );
}

function migrar_esquema(): void
{
    $col = null;
    foreach (db()->query('PRAGMA table_info(usuarios)')->fetchAll() as $c) {
        if ($c['name'] === 'debe_cambiar_password') {
            $col = $c;
            break;
        }
    }
    if ($col === null) {
        db()->exec('ALTER TABLE usuarios ADD COLUMN debe_cambiar_password INTEGER NOT NULL DEFAULT 0');
    }
}

function verificar_instalacion(): void
{
    crear_esquema();
    migrar_esquema();
    sembrar_contenido_si_vacio();
    crear_admin_si_no_existe();
}

function colores_cartilla(): array
{
    return ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
}

function color_valido(string $color): bool
{
    return in_array($color, colores_cartilla(), true);
}