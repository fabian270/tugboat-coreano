<?php
declare(strict_types=1);

function estadisticas_cartillas(int $usuario_id): array
{
    $stmt = db()->prepare(
        'SELECT c.id, c.titulo, c.descripcion, c.color, c.orden,
                COUNT(f.id)                        AS total,
                SUM(CASE WHEN f.id IS NULL THEN 0
                         WHEN p.ultimo_estado = \'lo_se\' THEN 1 ELSE 0 END) AS dominadas,
                SUM(CASE WHEN f.id IS NULL THEN 0
                         WHEN p.ultimo_estado = \'repasar\' THEN 1 ELSE 0 END) AS repasar,
                SUM(CASE WHEN f.id IS NULL THEN 0
                         WHEN p.id IS NULL THEN 1 ELSE 0 END) AS sin_ver
         FROM cartillas c
         LEFT JOIN fichas f     ON f.cartilla_id = c.id
         LEFT JOIN progreso p   ON p.ficha_id = f.id AND p.usuario_id = :uid
         GROUP BY c.id
         ORDER BY c.orden, c.id'
    );
    $stmt->execute(['uid' => $usuario_id]);
    return $stmt->fetchAll();
}

function pag_inicio(): void
{
    requiere_login();
    $usuario = usuario_actual();
    $titulo = 'Inicio';

    if (es_admin()) {
        $estadisticas = estadisticas_cartillas((int) $usuario['id']);
        $totales = [
            'usuarios'  => (int) db()->query('SELECT COUNT(*) AS n FROM usuarios')->fetch()['n'],
            'cartillas' => (int) db()->query('SELECT COUNT(*) AS n FROM cartillas')->fetch()['n'],
            'fichas'    => (int) db()->query('SELECT COUNT(*) AS n FROM fichas')->fetch()['n'],
            'registros' => (int) db()->query('SELECT COUNT(*) AS n FROM progreso')->fetch()['n'],
        ];
        require BASE_DIR . '/vistas/inicio_admin.php';
        return;
    }

    $cartillas = estadisticas_cartillas((int) $usuario['id']);
    require BASE_DIR . '/vistas/inicio_alumno.php';
}

function pag_estudiar(): void
{
    requiere_login();
    $cartilla_id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM cartillas WHERE id = ?');
    $stmt->execute([$cartilla_id]);
    $cartilla = $stmt->fetch();
    if (!$cartilla) {
        redirigir('inicio');
    }
    $solo = ($_GET['solo'] ?? '') === 'repasar' ? 'repasar' : '';

    $titulo = 'Estudiar · ' . $cartilla['titulo'];
    require BASE_DIR . '/vistas/estudiar.php';
}

function api_fichas(): void
{
    requiere_login();
    $usuario = usuario_actual();
    $cartilla_id = (int) ($_GET['cartilla'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM cartillas WHERE id = ?');
    $stmt->execute([$cartilla_id]);
    $cartilla = $stmt->fetch();
    if (!$cartilla) {
        json_out(['error' => 'Cartilla no encontrada.'], 404);
    }

    if (($_GET['solo'] ?? '') === 'repasar') {
        $sql = 'SELECT f.id, f.hangul, f.romanizacion, f.traduccion,
                       f.ejemplo, f.ejemplo_traduccion
                FROM fichas f
                LEFT JOIN progreso p ON p.ficha_id = f.id AND p.usuario_id = :uid
                WHERE f.cartilla_id = :cid
                  AND (p.id IS NULL OR p.ultimo_estado = \'repasar\')
                ORDER BY f.orden, f.id';
        $stmt = db()->prepare($sql);
        $stmt->execute(['uid' => (int) $usuario['id'], 'cid' => $cartilla_id]);
    } else {
        $sql = 'SELECT f.id, f.hangul, f.romanizacion, f.traduccion,
                       f.ejemplo, f.ejemplo_traduccion
                FROM fichas f
                WHERE f.cartilla_id = :cid
                ORDER BY f.orden, f.id';
        $stmt = db()->prepare($sql);
        $stmt->execute(['cid' => $cartilla_id]);
    }

    json_out([
        'cartilla' => [
            'id'    => (int) $cartilla['id'],
            'titulo' => $cartilla['titulo'],
        ],
        'fichas' => $stmt->fetchAll(),
    ]);
}

function api_progreso(): void
{
    requiere_login();
    $usuario = usuario_actual();
    $cuerpo = cuerpo_json();
    csrf_check();

    $ficha_id = (int) ($cuerpo['ficha_id'] ?? 0);
    $estado = ($cuerpo['estado'] ?? '') === 'lo_se' ? 'lo_se' : 'repasar';

    $stmt = db()->prepare('SELECT id FROM fichas WHERE id = ?');
    $stmt->execute([$ficha_id]);
    if (!$stmt->fetch()) {
        json_out(['error' => 'Ficha no encontrada.'], 404);
    }

    $stmt = db()->prepare(
        'INSERT INTO progreso (usuario_id, ficha_id, lo_se, repasar, vistas, ultimo_estado, actualizado_en)
         VALUES (:uid, :fid, :lo_se, :repasar, 1, :estado, datetime(\'now\'))
         ON CONFLICT (usuario_id, ficha_id) DO UPDATE SET
            lo_se          = progreso.lo_se + :lo_se,
            repasar        = progreso.repasar + :repasar,
            vistas         = progreso.vistas + 1,
            ultimo_estado  = :estado,
            actualizado_en = datetime(\'now\')'
    );
    $stmt->execute([
        'uid'     => (int) $usuario['id'],
        'fid'     => $ficha_id,
        'lo_se'   => $estado === 'lo_se' ? 1 : 0,
        'repasar' => $estado === 'repasar' ? 1 : 0,
        'estado'  => $estado,
    ]);

    json_out(['ok' => true, 'estado' => $estado]);
}

function api_tema(): void
{
    requiere_login();
    $usuario = usuario_actual();
    $cuerpo = cuerpo_json();
    csrf_check();

    $tema = ($cuerpo['tema'] ?? '') === 'claro' ? 'claro' : 'oscuro';
    $stmt = db()->prepare('UPDATE usuarios SET tema = ? WHERE id = ?');
    $stmt->execute([$tema, (int) $usuario['id']]);

    json_out(['ok' => true, 'tema' => $tema]);
}