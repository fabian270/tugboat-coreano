<?php
declare(strict_types=1);

function crear_admin_si_no_existe(): void
{
    $stmt = db()->prepare('SELECT COUNT(*) AS n FROM usuarios WHERE rol = ?');
    $stmt->execute(['admin']);
    if ((int) $stmt->fetch()['n'] > 0) {
        return;
    }
    $ins = db()->prepare('INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES (?, ?, ?, ?)');
    $ins->execute([ADMIN_NOMBRE, ADMIN_USUARIO, password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT), 'admin']);
}

function fichas_por_cartilla(): array
{
    return [
        [
            'titulo'      => 'Hangul · Consonantes',
            'descripcion' => 'Los 14 consonantes básicos del alfabeto coreano (한글).',
            'color'       => 'primary',
            'orden'       => 1,
            'fichas'      => [
                ['ㄱ', 'g / k',     "Consonante 'giyeok' (기역). Suena como 'g' o 'k'.", '', ''],
                ['ㄴ', 'n',         "Consonante 'nieun' (니은). Como la 'n' de «nube».", '', ''],
                ['ㄷ', 'd / t',     "Consonante 'digeut' (디귿). Suena como 'd' o 't'.", '', ''],
                ['ㄹ', 'r / l',     "Consonante 'rieul' (리을). Sonido entre 'r' y 'l'.", '', ''],
                ['ㅁ', 'm',         "Consonante 'mieum' (미음). Como la 'm' de «mano».", '', ''],
                ['ㅂ', 'b / p',     "Consonante 'bieup' (비읍). Suena como 'b' o 'p'.", '', ''],
                ['ㅅ', 's',         "Consonante 'siot' (시옷). Como la 's' de «sol».", '', ''],
                ['ㅇ', 'ng o muda', "Consonante 'ieung' (이응). Muda al inicio; suena 'ng' al final.", '', ''],
                ['ㅈ', 'j',         "Consonante 'jieut' (지읒). Como 'j' suave o 'y'.", '', ''],
                ['ㅊ', 'ch',        "Consonante 'chieut' (치읓). Como 'ch' de «chile».", '', ''],
                ['ㅋ', 'k fuerte',  "Consonante 'kieuk' (키읔). 'k' con aire (aspirada).", '', ''],
                ['ㅌ', 't fuerte',  "Consonante 'tieut' (티읕). 't' con aire (aspirada).", '', ''],
                ['ㅍ', 'p fuerte',  "Consonante 'pieup' (피읖). 'p' con aire (aspirada).", '', ''],
                ['ㅎ', 'h',         "Consonante 'hieut' (히읗). 'h' aspirada, como en «hola».", '', ''],
            ],
        ],
        [
            'titulo'      => 'Hangul · Vocales',
            'descripcion' => 'Las 10 vocales básicas del alfabeto coreano (한글).',
            'color'       => 'info',
            'orden'       => 2,
            'fichas'      => [
                ['ㅏ', 'a',   'Vocal «a». Como en «casa».', '', ''],
                ['ㅑ', 'ya',  'Vocal «ya». «a» precedida de sonido «y».', '', ''],
                ['ㅓ', 'eo',  'Vocal «eo». Sonido entre «o» y «e», boca abierta.', '', ''],
                ['ㅕ', 'yeo', 'Vocal «yeo». «eo» precedida de «y».', '', ''],
                ['ㅗ', 'o',   'Vocal «o». Como en «oso», labios redondeados.', '', ''],
                ['ㅛ', 'yo',  'Vocal «yo». «o» precedida de «y».', '', ''],
                ['ㅜ', 'u',   'Vocal «u». Como en «luna».', '', ''],
                ['ㅠ', 'yu',  'Vocal «yu». «u» precedida de «y».', '', ''],
                ['ㅡ', 'eu',  'Vocal «eu». Sonido neutro, labios estirados.', '', ''],
                ['ㅣ', 'i',   'Vocal «i». Como en «misa».', '', ''],
            ],
        ],
        [
            'titulo'      => 'Saludos',
            'descripcion' => 'Frases esenciales para saludar y presentarse en coreano.',
            'color'       => 'success',
            'orden'       => 3,
            'fichas'      => [
                ['안녕하세요', 'annyeonghaseyo',       'Hola (formal) / Buenos días.',                     '안녕하세요!', '¡Hola!'],
                ['안녕히 가세요', 'annyeonghi gaseyo', 'Adiós (cuando la otra persona se va).',            '', ''],
                ['안녕히 계세요', 'annyeonghi gyeseyo', 'Adiós (cuando tú te vas).',                        '', ''],
                ['감사합니다', 'gamsahamnida',        'Gracias (formal).',                                 '감사합니다!', '¡Gracias!'],
                ['죄송합니다', 'joesonghamnida',      'Lo siento / Perdón.',                              '', ''],
                ['괜찮아요', 'gwaenchanayo',         'Está bien / No pasa nada.',                         '', ''],
                ['네', 'ne',                          'Sí.',                                               '', ''],
                ['아니요', 'aniyo',                   'No.',                                               '', ''],
                ['천천히 말해 주세요', 'cheoncheonhi malhae juseyo', 'Hable despacio, por favor.',         '', ''],
                ['이름이 뭐예요?', 'ireumi mwoyeyo',  '¿Cómo te llamas?',                                  '제 이름은 ...이에요.', 'Me llamo ...'],
                ['만나서 반갑습니다', 'mannaseo bangapseumnida', 'Mucho gusto (en conocerte).',            '만나서 반갑습니다!', '¡Mucho gusto!'],
                ['잘 자요', 'jal jayo',               'Buenas noches (despedida, informal).',              '', ''],
            ],
        ],
        [
            'titulo'      => 'Números · nativos',
            'descripcion' => 'Números coreanos nativos (se usan para contar objetos y edades).',
            'color'       => 'warning',
            'orden'       => 4,
            'fichas'      => [
                ['하나', 'hana',   'Uno (1).',   '', ''],
                ['둘',   'dul',    'Dos (2).',   '', ''],
                ['셋',   'set',    'Tres (3).',  '', ''],
                ['넷',   'net',    'Cuatro (4).', '', ''],
                ['다섯', 'daseot', 'Cinco (5).', '', ''],
                ['여섯', 'yeoseot', 'Seis (6).',  '', ''],
                ['일곱', 'ilgop',  'Siete (7).', '', ''],
                ['여덟', 'yeodeol', 'Ocho (8).',  '', ''],
                ['아홉', 'ahop',   'Nueve (9).', '', ''],
                ['열',   'yeol',   'Diez (10).', '', ''],
            ],
        ],
        [
            'titulo'      => 'Números · sino-coreanos',
            'descripcion' => 'Números de origen chino (se usan para precios, fechas y teléfonos).',
            'color'       => 'danger',
            'orden'       => 5,
            'fichas'      => [
                ['일', 'il',  'Uno (1).',   '', ''],
                ['이', 'i',   'Dos (2).',   '', ''],
                ['삼', 'sam', 'Tres (3).',  '', ''],
                ['사', 'sa',  'Cuatro (4).', '', ''],
                ['오', 'o',   'Cinco (5).', '', ''],
                ['육', 'yuk', 'Seis (6).',  '', ''],
                ['칠', 'chil', 'Siete (7).', '', ''],
                ['팔', 'pal', 'Ocho (8).',  '', ''],
                ['구', 'gu',  'Nueve (9).', '', ''],
                ['십', 'sip', 'Diez (10).', '', ''],
            ],
        ],
        [
            'titulo'      => 'Vocabulario cotidiano',
            'descripcion' => 'Palabras básicas para la vida diaria.',
            'color'       => 'secondary',
            'orden'       => 6,
            'fichas'      => [
                ['물',     'mul',      'Agua.',        '물 주세요.', 'Agua, por favor.'],
                ['밥',     'bap',      'Arroz / Comida.', '밥을 먹어요.', 'Como arroz.'],
                ['집',     'jip',      'Casa.',        '저의 집이에요.', 'Es mi casa.'],
                ['학교',   'hakgyo',   'Escuela.',     '학교에 가요.', 'Voy a la escuela.'],
                ['친구',   'chingu',   'Amigo (confianza).', '친구가 있어요.', 'Tengo amigos.'],
                ['사랑',   'sarang',   'Amor.',        '사랑해요.', 'Te quiero.'],
                ['고양이', 'goyangi',  'Gato.',        '고양이를 좋아해요.', 'Me gustan los gatos.'],
                ['강아지', 'gangaji',  'Perrito / Cachorro.', '', ''],
                ['사람',   'saram',    'Persona.',     '', ''],
                ['나라',   'nara',     'País.',        '', ''],
                ['오늘',   'oneul',    'Hoy.',         '오늘은 좋아요.', 'Hoy está bien.'],
                ['내일',   'naeil',    'Mañana.',      '', ''],
                ['어제',   'eoje',     'Ayer.',        '', ''],
                ['시간',   'sigan',    'Hora / Tiempo.', '지금 몇 시예요?', '¿Qué hora es?'],
                ['돈',     'don',      'Dinero.',      '', ''],
                ['일',     'il',       'Trabajo.',     '일을 해요.', 'Trabajo.'],
            ],
        ],
        [
            'titulo'      => 'Frases útiles',
            'descripcion' => 'Frases prácticas para conversar en situaciones comunes.',
            'color'       => 'primary',
            'orden'       => 7,
            'fichas'      => [
                ['저는 한국어를 배워요', 'jeoneun hangugeoreul baewoyo',   'Estoy aprendiendo coreano.',  '', ''],
                ['한국어를 할 수 있어요?', 'hangugeoreul hal su isseoyo',  '¿Hablas coreano?',           '', ''],
                ['화장실이 어디예요?', 'hwajangsiri eodieyo',            '¿Dónde está el baño?',        '', ''],
                ['얼마예요?', 'eolmayeyo',                              '¿Cuánto cuesta?',             '', ''],
                ['맛있어요!', 'masisseoyo',                             '¡Está delicioso!',            '진짜 맛있어요!', '¡Está de verdad delicioso!'],
                ['도와주세요', 'dowajuseyo',                            'Ayúdeme, por favor.',         '', ''],
                ['이해했어요', 'ihaehaesseoyo',                          'Entendí.',                    '', ''],
                ['잘 모르겠어요', 'jal moreugesseoyo',                   'No lo entiendo.',             '잘 모르겠어요.', 'No lo entiendo.'],
                ['다시 말해 주세요', 'dasi malhae juseyo',               'Repítalo, por favor.',        '', ''],
                ['좋아요', 'joayo',                                     'Me gusta / Está bien.',       '', ''],
            ],
        ],
    ];
}

function sembrar_contenido_si_vacio(): void
{
    $n = (int) db()->query('SELECT COUNT(*) AS n FROM cartillas')->fetch()['n'];
    if ($n > 0) {
        return;
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $insCartilla = $pdo->prepare(
            'INSERT INTO cartillas (titulo, descripcion, color, orden) VALUES (?, ?, ?, ?)'
        );
        $insFicha = $pdo->prepare(
            'INSERT INTO fichas (cartilla_id, hangul, romanizacion, traduccion, ejemplo, ejemplo_traduccion, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        foreach (fichas_por_cartilla() as $i => $cartilla) {
            $insCartilla->execute([
                $cartilla['titulo'],
                $cartilla['descripcion'],
                $cartilla['color'],
                $cartilla['orden'],
            ]);
            $cartilla_id = (int) $pdo->lastInsertId();
            foreach ($cartilla['fichas'] as $j => $f) {
                $insFicha->execute([
                    $cartilla_id,
                    $f[0],
                    $f[1],
                    $f[2],
                    $f[3],
                    $f[4],
                    $j + 1,
                ]);
            }
        }
        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        throw $ex;
    }
}