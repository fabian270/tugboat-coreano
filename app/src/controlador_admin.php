<?php
declare(strict_types=1);

// ---------- Usuarios ----------

function usuario_libre(string $nombre_usuario, ?int $ignorar_id = null): bool
{
    $sql = 'SELECT COUNT(*) AS n FROM usuarios WHERE usuario = ?';
    $params = [$nombre_usuario];
    if ($ignorar_id !== null) {
        $sql .= ' AND id <> ?';
        $params[] = $ignorar_id;
    }
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetch()['n'] === 0;
}

function admin_crear_usuario(): void
{
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $nombre_usuario = trim((string) ($_POST['usuario'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $rol = ($_POST['rol'] ?? '') === 'admin' ? 'admin' : 'alumno';

    if ($nombre === '' || $nombre_usuario === '') {
        flash('error', 'El nombre y el usuario son obligatorios.');
        return;
    }
    if (!usuario_libre($nombre_usuario)) {
        flash('error', "El usuario «{$nombre_usuario}» ya existe.");
        return;
    }
    if ($password === '') {
        $password = generar_password();
        $generada = true;
    } else {
        $generada = false;
    }
    if (strlen($password) < 4) {
        flash('error', 'La contraseña debe tener al menos 4 caracteres.');
        return;
    }

    $stmt = db()->prepare('INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nombre, $nombre_usuario, password_hash($password, PASSWORD_DEFAULT), $rol]);

    $aviso = $generada
        ? "Usuario «{$nombre_usuario}» creado. Contraseña generada: {$password} (anótala)."
        : "Usuario «{$nombre_usuario}» creado.";
    flash('ok', $aviso);
}

function admin_editar_usuario(int $id): void
{
    if ($id <= 0) {
        return;
    }
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $nombre_usuario = trim((string) ($_POST['usuario'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $rol = ($_POST['rol'] ?? '') === 'admin' ? 'admin' : 'alumno';
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre === '' || $nombre_usuario === '') {
        flash('error', 'El nombre y el usuario son obligatorios.');
        return;
    }
    if (!usuario_libre($nombre_usuario, $id)) {
        flash('error', "El usuario «{$nombre_usuario}» ya existe.");
        return;
    }

    if ($password === '') {
        $stmt = db()->prepare('UPDATE usuarios SET nombre = ?, usuario = ?, rol = ?, activo = ? WHERE id = ?');
        $stmt->execute([$nombre, $nombre_usuario, $rol, $activo, $id]);
    } else {
        if (strlen($password) < 4) {
            flash('error', 'La nueva contraseña debe tener al menos 4 caracteres.');
            return;
        }
        $stmt = db()->prepare(
            'UPDATE usuarios SET nombre = ?, usuario = ?, rol = ?, activo = ?, password_hash = ? WHERE id = ?'
        );
        $stmt->execute([$nombre, $nombre_usuario, $rol, $activo, password_hash($password, PASSWORD_DEFAULT), $id]);
    }
    flash('ok', 'Usuario actualizado.');
}

function admin_eliminar_usuario(int $id): void
{
    $actual = usuario_actual();
    if ((int) $actual['id'] === $id) {
        flash('error', 'No puedes eliminar tu propia cuenta.');
        return;
    }
    $stmt = db()->prepare('SELECT rol, activo FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        return;
    }
    if ($fila['rol'] === 'admin' && (int) $fila['activo'] === 1) {
        $admins = db()->query('SELECT COUNT(*) AS n FROM usuarios WHERE rol = \'admin\' AND activo = 1');
        if ((int) $admins->fetch()['n'] <= 1) {
            flash('error', 'No puedes eliminar el último admin activo.');
            return;
        }
    }
    $stmt = db()->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    flash('ok', 'Usuario eliminado.');
}

function admin_toggle_usuario(int $id): void
{
    $actual = usuario_actual();
    if ((int) $actual['id'] === $id) {
        flash('error', 'No puedes desactivar tu propia cuenta.');
        return;
    }
    $stmt = db()->prepare('SELECT activo, rol FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        return;
    }
    if ($fila['rol'] === 'admin' && (int) $fila['activo'] === 1) {
        $admins = db()->query('SELECT COUNT(*) AS n FROM usuarios WHERE rol = \'admin\' AND activo = 1');
        if ((int) $admins->fetch()['n'] <= 1) {
            flash('error', 'No puedes desactivar el último admin activo.');
            return;
        }
    }
    $nuevo = (int) $fila['activo'] === 1 ? 0 : 1;
    $stmt = db()->prepare('UPDATE usuarios SET activo = ? WHERE id = ?');
    $stmt->execute([$nuevo, $id]);
    flash('ok', $nuevo ? 'Usuario activado.' : 'Usuario desactivado.');
}

function admin_reset_password(int $id): void
{
    $stmt = db()->prepare('SELECT nombre FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        return;
    }
    $nueva = generar_password();
    $stmt = db()->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?');
    $stmt->execute([password_hash($nueva, PASSWORD_DEFAULT), $id]);
    flash('ok', "Nueva contraseña para «{$fila['nombre']}»: {$nueva} (anótala).");
}

function pag_admin_usuarios(): void
{
    requiere_admin();

    if (es_post()) {
        csrf_check();
        $accion = (string) ($_POST['accion'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        switch ($accion) {
            case 'crear':
                admin_crear_usuario();
                break;
            case 'editar':
                admin_editar_usuario($id);
                break;
            case 'eliminar':
                admin_eliminar_usuario($id);
                break;
            case 'activar':
                admin_toggle_usuario($id);
                break;
            case 'reset_password':
                admin_reset_password($id);
                break;
        }
        redirigir('admin/usuarios');
    }

    $editar_id = (int) ($_GET['editar'] ?? 0);
    $editar = null;
    if ($editar_id > 0) {
        $stmt = db()->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$editar_id]);
        $editar = $stmt->fetch();
    }

    $usuarios = db()->query(
        'SELECT u.*,
                COALESCE(SUM(CASE WHEN p.ultimo_estado = \'lo_se\' THEN 1 ELSE 0 END), 0) AS dominadas,
                COALESCE(SUM(CASE WHEN p.id IS NULL THEN 0 ELSE 1 END), 0) AS vistas
         FROM usuarios u
         LEFT JOIN progreso p ON p.usuario_id = u.id
         GROUP BY u.id
         ORDER BY u.rol, u.nombre'
    )->fetchAll();

    $titulo = 'Usuarios';
    require BASE_DIR . '/vistas/admin_usuarios.php';
}

// ---------- Cartillas ----------

function admin_crear_cartilla(): void
{
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $color = ($_POST['color'] ?? '');
    $orden = (int) ($_POST['orden'] ?? 0);

    if ($titulo === '') {
        flash('error', 'El título de la cartilla es obligatorio.');
        return;
    }
    if (!color_valido($color)) {
        $color = 'primary';
    }
    $stmt = db()->prepare('INSERT INTO cartillas (titulo, descripcion, color, orden) VALUES (?, ?, ?, ?)');
    $stmt->execute([$titulo, $descripcion, $color, $orden]);
    flash('ok', "Cartilla «{$titulo}» creada.");
}

function admin_editar_cartilla(int $id): void
{
    if ($id <= 0) {
        return;
    }
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $color = ($_POST['color'] ?? '');
    $orden = (int) ($_POST['orden'] ?? 0);

    if ($titulo === '') {
        flash('error', 'El título de la cartilla es obligatorio.');
        return;
    }
    if (!color_valido($color)) {
        $color = 'primary';
    }
    $stmt = db()->prepare('UPDATE cartillas SET titulo = ?, descripcion = ?, color = ?, orden = ? WHERE id = ?');
    $stmt->execute([$titulo, $descripcion, $color, $orden, $id]);
    flash('ok', 'Cartilla actualizada.');
}

function admin_eliminar_cartilla(int $id): void
{
    $stmt = db()->prepare('DELETE FROM cartillas WHERE id = ?');
    $stmt->execute([$id]);
    flash('ok', 'Cartilla eliminada (junto con sus fichas).');
}

function pag_admin_cartillas(): void
{
    requiere_admin();

    if (es_post()) {
        csrf_check();
        $accion = (string) ($_POST['accion'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        switch ($accion) {
            case 'crear':
                admin_crear_cartilla();
                break;
            case 'editar':
                admin_editar_cartilla($id);
                break;
            case 'eliminar':
                admin_eliminar_cartilla($id);
                break;
        }
        redirigir('admin/cartillas');
    }

    $editar_id = (int) ($_GET['editar'] ?? 0);
    $editar = null;
    if ($editar_id > 0) {
        $stmt = db()->prepare('SELECT * FROM cartillas WHERE id = ?');
        $stmt->execute([$editar_id]);
        $editar = $stmt->fetch();
    }

    $cartillas = db()->query(
        'SELECT c.*, COUNT(f.id) AS fichas
         FROM cartillas c
         LEFT JOIN fichas f ON f.cartilla_id = c.id
         GROUP BY c.id
         ORDER BY c.orden, c.id'
    )->fetchAll();

    $titulo = 'Cartillas';
    require BASE_DIR . '/vistas/admin_cartillas.php';
}

// ---------- Fichas ----------

function admin_crear_ficha(int $cartilla_id): void
{
    $hangul = trim((string) ($_POST['hangul'] ?? ''));
    $romanizacion = trim((string) ($_POST['romanizacion'] ?? ''));
    $traduccion = trim((string) ($_POST['traduccion'] ?? ''));
    $ejemplo = trim((string) ($_POST['ejemplo'] ?? ''));
    $ejemplo_traduccion = trim((string) ($_POST['ejemplo_traduccion'] ?? ''));
    $orden = (int) ($_POST['orden'] ?? 0);

    if ($hangul === '' || $traduccion === '') {
        flash('error', 'El hangul y la traducción son obligatorios.');
        return;
    }
    $stmt = db()->prepare(
        'INSERT INTO fichas (cartilla_id, hangul, romanizacion, traduccion, ejemplo, ejemplo_traduccion, orden)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$cartilla_id, $hangul, $romanizacion, $traduccion, $ejemplo, $ejemplo_traduccion, $orden]);
    flash('ok', 'Ficha agregada.');
}

function admin_editar_ficha(): void
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        return;
    }
    $hangul = trim((string) ($_POST['hangul'] ?? ''));
    $romanizacion = trim((string) ($_POST['romanizacion'] ?? ''));
    $traduccion = trim((string) ($_POST['traduccion'] ?? ''));
    $ejemplo = trim((string) ($_POST['ejemplo'] ?? ''));
    $ejemplo_traduccion = trim((string) ($_POST['ejemplo_traduccion'] ?? ''));
    $orden = (int) ($_POST['orden'] ?? 0);

    if ($hangul === '' || $traduccion === '') {
        flash('error', 'El hangul y la traducción son obligatorios.');
        return;
    }
    $stmt = db()->prepare(
        'UPDATE fichas SET hangul = ?, romanizacion = ?, traduccion = ?, ejemplo = ?, ejemplo_traduccion = ?, orden = ?
         WHERE id = ?'
    );
    $stmt->execute([$hangul, $romanizacion, $traduccion, $ejemplo, $ejemplo_traduccion, $orden, $id]);
    flash('ok', 'Ficha actualizada.');
}

function admin_eliminar_ficha(int $id): void
{
    $stmt = db()->prepare('DELETE FROM fichas WHERE id = ?');
    $stmt->execute([$id]);
    flash('ok', 'Ficha eliminada.');
}

function pag_admin_fichas(): void
{
    requiere_admin();

    $cartilla_id = (int) ($_GET['cartilla'] ?? ($_POST['cartilla_id'] ?? 0));
    $stmt = db()->prepare('SELECT * FROM cartillas WHERE id = ?');
    $stmt->execute([$cartilla_id]);
    $cartilla = $stmt->fetch();
    if (!$cartilla) {
        redirigir('admin/cartillas');
    }

    if (es_post()) {
        csrf_check();
        $accion = (string) ($_POST['accion'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        switch ($accion) {
            case 'crear':
                admin_crear_ficha($cartilla_id);
                break;
            case 'editar':
                admin_editar_ficha();
                break;
            case 'eliminar':
                admin_eliminar_ficha($id);
                break;
        }
        redirigir('admin/fichas?cartilla=' . $cartilla_id);
    }

    $editar_id = (int) ($_GET['editar'] ?? 0);
    $editar = null;
    if ($editar_id > 0) {
        $stmt = db()->prepare('SELECT * FROM fichas WHERE id = ? AND cartilla_id = ?');
        $stmt->execute([$editar_id, $cartilla_id]);
        $editar = $stmt->fetch();
    }

    $stmt = db()->prepare('SELECT * FROM fichas WHERE cartilla_id = ? ORDER BY orden, id');
    $stmt->execute([$cartilla_id]);
    $fichas = $stmt->fetchAll();

    $titulo = 'Fichas · ' . $cartilla['titulo'];
    require BASE_DIR . '/vistas/admin_fichas.php';
}

// ---------- Progreso ----------

function pag_admin_progreso(): void
{
    requiere_admin();

    $usuarios = db()->query('SELECT * FROM usuarios ORDER BY nombre')->fetchAll();

    $usuario_id = (int) ($_GET['usuario'] ?? 0);
    $seleccion = null;
    foreach ($usuarios as $u) {
        if ((int) $u['id'] === $usuario_id) {
            $seleccion = $u;
            break;
        }
    }
    if ($seleccion === null && $usuarios) {
        $seleccion = $usuarios[0];
        $usuario_id = (int) $usuarios[0]['id'];
    }

    $por_cartilla = [];
    $resumen = ['dominadas' => 0, 'total' => 0];
    if ($seleccion !== null) {
        $por_cartilla = estadisticas_cartillas($usuario_id);
        foreach ($por_cartilla as $c) {
            $resumen['dominadas'] += (int) $c['dominadas'];
            $resumen['total'] += (int) $c['total'];
        }
    }

    $cartilla_id = (int) ($_GET['cartilla'] ?? 0);
    $detalle = [];
    if ($seleccion !== null && $cartilla_id > 0) {
        $stmt = db()->prepare(
            'SELECT f.id, f.hangul, f.romanizacion, f.traduccion,
                    COALESCE(p.ultimo_estado, \'sin_ver\') AS estado,
                    COALESCE(p.vistas, 0) AS vistas,
                    p.actualizado_en
             FROM fichas f
             LEFT JOIN progreso p ON p.ficha_id = f.id AND p.usuario_id = :uid
             WHERE f.cartilla_id = :cid
             ORDER BY f.orden, f.id'
        );
        $stmt->execute(['uid' => $usuario_id, 'cid' => $cartilla_id]);
        $detalle = $stmt->fetchAll();
    }

    $titulo = 'Progreso';
    require BASE_DIR . '/vistas/admin_progreso.php';
}